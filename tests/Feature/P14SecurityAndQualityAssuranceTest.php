<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class P14SecurityAndQualityAssuranceTest extends TestCase
{
    use RefreshDatabase;

    public function test_mass_assignment_privilege_escalation_is_blocked_on_web_and_api_registration(): void
    {
        // Attempt privilege escalation via API registration
        $apiRes = $this->postJson('/api/v1/auth/register', [
            'name' => 'Attacker API',
            'email' => 'attacker-api@example.com',
            'password' => 'StrongPassword!123',
            'password_confirmation' => 'StrongPassword!123',
            'role' => User::ROLE_ADMIN,
        ]);

        $apiRes->assertStatus(201)
            ->assertJsonPath('data.user.role', User::ROLE_CUSTOMER);

        $this->assertDatabaseHas('users', [
            'email' => 'attacker-api@example.com',
            'role' => User::ROLE_CUSTOMER,
        ]);

        // Attempt privilege escalation via Web registration
        $webRes = $this->post('/register', [
            'name' => 'Attacker Web',
            'email' => 'attacker-web@example.com',
            'password' => 'StrongPassword!123',
            'password_confirmation' => 'StrongPassword!123',
            'role' => User::ROLE_OPERATOR,
        ]);

        $webRes->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'attacker-web@example.com',
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function test_mass_assignment_cannot_tamper_shipment_delay_metrics_or_tracking_number_on_update(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('op')->plainTextToken;

        $shipment = Shipment::factory()->create([
            'tracking_number' => 'CT-20261111-ORIG',
            'status' => Shipment::STATUS_CREATED,
            'is_delayed' => false,
            'delay_hours' => 0,
        ]);

        $res = $this->withToken($token)->putJson("/api/v1/shipments/{$shipment->id}", [
            'tracking_number' => 'CT-TAMPERED-9999',
            'is_delayed' => true,
            'delay_hours' => 999,
            'notes' => 'Catatan operasional tervalidasi.',
        ]);

        $res->assertStatus(200);
        $fresh = $shipment->fresh();

        $this->assertEquals('CT-20261111-ORIG', $fresh->tracking_number);
        $this->assertFalse((bool) $fresh->is_delayed);
        $this->assertEquals(0, $fresh->delay_hours);
        $this->assertEquals('Catatan operasional tervalidasi.', $fresh->notes);
    }

    public function test_public_tracking_strips_customer_pii_and_security_headers_are_present(): void
    {
        $customer = User::factory()->customer()->create([
            'name' => 'Rahasia Klien',
            'email' => 'rahasia-pii@example.com',
            'phone' => '+62-811-9999-8888',
        ]);

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'tracking_number' => 'CT-20261111-PII1',
        ]);

        // Public API tracking omits customer relation entirely
        $apiRes = $this->getJson('/api/v1/tracking/CT-20261111-PII1');
        $apiRes->assertStatus(200)
            ->assertJsonMissing(['email' => 'rahasia-pii@example.com'])
            ->assertJsonMissing(['phone' => '+62-811-9999-8888'])
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');

        // Public web detail view hides customer email from guest viewers
        $webGuestRes = $this->get('/shipments/CT-20261111-PII1');
        $webGuestRes->assertStatus(200)
            ->assertDontSee('rahasia-pii@example.com')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');

        // Verify web middleware group enforces CSRF validation
        $webMiddleware = Route::getMiddlewareGroups()['web'] ?? [];
        $hasCsrf = collect($webMiddleware)->contains(
            fn ($mw) => str_contains($mw, 'VerifyCsrfToken') || str_contains($mw, 'ValidateCsrfToken') || $mw === ValidateCsrfToken::class
        );
        $this->assertTrue($hasCsrf, 'Web middleware group must include CSRF protection.');
        $this->assertTrue(class_exists(SecurityHeadersMiddleware::class));
    }

    public function test_sql_injection_wildcards_and_xss_payloads_are_neutralized(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create(['name' => 'Aman Logistik']);
        $carrier = Carrier::factory()->create(['name' => 'Carrier 100% Express']);
        $origin = Location::factory()->create(['name' => 'Port Jakarta']);

        $xssTracking = 'CT-20261111-XSS1';
        $shipment = Shipment::factory()->create([
            'tracking_number' => $xssTracking,
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'notes' => '<script>alert("xss-notes")</script>',
        ]);

        // Stored XSS attempt inside tracking event description
        $this->actingAs($operator)->post("/shipments/{$xssTracking}/events", [
            'status' => Shipment::STATUS_OUT_FOR_DELIVERY,
            'location_id' => $origin->id,
            'location_name' => 'Hub <img src=x onerror=alert(1)>',
            'description' => '<script>alert("xss-event")</script>',
        ])->assertRedirect();

        $detailRes = $this->actingAs($operator)->get("/shipments/{$xssTracking}");
        $detailRes->assertStatus(200)
            ->assertDontSee('<script>alert("xss-event")</script>', false)
            ->assertDontSee('<script>alert("xss-notes")</script>', false)
            ->assertSee('&lt;script&gt;alert(&quot;xss-event&quot;)&lt;/script&gt;', false);

        // SQL Injection & LIKE wildcard abuse safety on shipments search + malicious sort_by
        $token = $operator->createToken('op')->plainTextToken;

        $sqliRes = $this->withToken($token)->getJson('/api/v1/shipments?' . http_build_query([
            'search' => "' OR '1'='1'; DROP TABLE shipments;--",
            'sort_by' => 'created_at; DROP TABLE users;--',
            'per_page' => 999999,
        ]));

        $sqliRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pagination.per_page', 100);

        // Literal % matching does not match unrelated shipments without 100%
        $plainCarrier = Carrier::factory()->create(['name' => 'Carrier Plain Express']);
        Shipment::factory()->create([
            'tracking_number' => 'CT-20261111-PLAIN',
            'customer_id' => $customer->id,
            'carrier_id' => $plainCarrier->id,
        ]);

        $wildcardRes = $this->withToken($token)->getJson('/api/v1/shipments?' . http_build_query([
            'search' => '100%',
        ]));

        $wildcardRes->assertStatus(200);
        $matched = collect($wildcardRes->json('data.items'))->pluck('tracking_number')->all();
        $this->assertEquals([$xssTracking], $matched);
    }

    public function test_complete_authorization_and_shipment_status_transition_matrix(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->operator()->create();
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $dest = Location::factory()->create();

        // Unauthenticated requests blocked with 401
        $this->getJson('/api/v1/shipments')->assertStatus(401);
        $this->postJson('/api/v1/carriers', ['name' => 'No Auth', 'code' => 'NOAU'])->assertStatus(401);

        // Customer blocked from staff routes on Web
        $this->actingAs($customerA)->get('/carriers')->assertStatus(403);
        $this->app['auth']->forgetGuards();

        // Customer blocked from staff operations on API via Sanctum token
        $tokenCustA = $customerA->createToken('custA')->plainTextToken;
        $this->withToken($tokenCustA)->postJson('/api/v1/shipments', [
            'customer_id' => $customerA->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'estimated_delivery_at' => now()->addDays(5)->toISOString(),
        ])->assertStatus(403);
        $this->withToken($tokenCustA)->getJson('/api/v1/carriers')->assertStatus(403);

        // Full valid Shipment CRUD + lifecycle CREATED -> BOOKED -> PICKED_UP -> IN_TRANSIT -> EXCEPTION -> IN_TRANSIT -> OUT_FOR_DELIVERY -> DELIVERED
        $this->app['auth']->forgetGuards();
        $tokenOp = $operator->createToken('op')->plainTextToken;
        $createRes = $this->withToken($tokenOp)->postJson('/api/v1/shipments', [
            'customer_id' => $customerA->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'estimated_delivery_at' => now()->addDays(7)->toISOString(),
        ]);
        $createRes->assertStatus(201)->assertJsonPath('data.status', Shipment::STATUS_CREATED);
        $shipmentId = $createRes->json('data.id');

        // Customer B cannot read Customer A's shipment (IDOR check)
        $this->app['auth']->forgetGuards();
        $tokenCustB = $customerB->createToken('custB')->plainTextToken;
        $this->withToken($tokenCustB)->getJson("/api/v1/shipments/{$shipmentId}")->assertStatus(403);
        $this->withToken($tokenCustB)->getJson("/api/v1/shipments/{$shipmentId}/eta")->assertStatus(403);

        // Invalid status jump CREATED -> DELIVERED rejected 422
        $this->app['auth']->forgetGuards();
        $this->withToken($tokenOp)->putJson("/api/v1/shipments/{$shipmentId}", [
            'status' => Shipment::STATUS_DELIVERED,
        ])->assertStatus(422)->assertJsonValidationErrors(['status']);

        // Valid sequential lifecycle transitions
        $validPath = [
            Shipment::STATUS_BOOKED,
            Shipment::STATUS_PICKED_UP,
            Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_EXCEPTION,
            Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_OUT_FOR_DELIVERY,
            Shipment::STATUS_DELIVERED,
        ];

        foreach ($validPath as $nextStatus) {
            $this->withToken($tokenOp)->putJson("/api/v1/shipments/{$shipmentId}", [
                'status' => $nextStatus,
            ])->assertStatus(200)->assertJsonPath('data.status', $nextStatus);
        }

        // Only Admin can delete shipment (Operator gets 403, Admin gets 200)
        $this->withToken($tokenOp)->deleteJson("/api/v1/shipments/{$shipmentId}")->assertStatus(403);

        $this->app['auth']->forgetGuards();
        $tokenAdmin = $admin->createToken('admin')->plainTextToken;
        $this->withToken($tokenAdmin)->deleteJson("/api/v1/shipments/{$shipmentId}")->assertStatus(200);
    }

    public function test_worldwide_eta_thresholds_12h_18h_24h_and_global_kpi_calculations(): void
    {
        $operator = User::factory()->operator()->create();
        $tokenOp = $operator->createToken('op')->plainTextToken;

        $expected = now()->addDays(5);
        $originSgp = Location::factory()->create(['country' => 'Singapore']);
        $destEu = Location::factory()->create(['country' => 'Netherlands']);
        $destUs = Location::factory()->create(['country' => 'United States']);
        $destAu = Location::factory()->create(['country' => 'Australia']);

        // 1. SEA->Europe Tier (12h threshold, +6h delay -> LOW & within threshold)
        $shipEu = Shipment::factory()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'origin_id' => $originSgp->id,
            'destination_id' => $destEu->id,
            'expected_delivery_at' => $expected->copy(),
            'estimated_delivery_at' => $expected->copy(),
        ]);

        $this->withToken($tokenOp)->patchJson("/api/v1/shipments/{$shipEu->id}/eta", [
            'estimated_delivery_at' => $expected->copy()->addHours(6)->toISOString(),
            'reason' => 'Antrean pendek pelabuhan Rotterdam',
        ])->assertStatus(200)
            ->assertJsonPath('data.evaluation.is_delayed', true)
            ->assertJsonPath('data.evaluation.delay_hours', 6)
            ->assertJsonPath('data.evaluation.threshold_hours', 12)
            ->assertJsonPath('data.evaluation.exceeds_threshold', false)
            ->assertJsonPath('data.evaluation.severity', ShipmentException::SEVERITY_LOW);

        // 2. Asia->NA Tier (18h threshold, +18h delay -> MEDIUM & at boundary; +20h exceeds)
        $shipUs = Shipment::factory()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'origin_id' => $originSgp->id,
            'destination_id' => $destUs->id,
            'expected_delivery_at' => $expected->copy(),
            'estimated_delivery_at' => $expected->copy(),
        ]);

        $this->withToken($tokenOp)->patchJson("/api/v1/shipments/{$shipUs->id}/eta", [
            'estimated_delivery_at' => $expected->copy()->addHours(20)->toISOString(),
            'reason' => 'Cuaca Pasifik menghambat laju kapal',
        ])->assertStatus(200)
            ->assertJsonPath('data.evaluation.is_delayed', true)
            ->assertJsonPath('data.evaluation.delay_hours', 20)
            ->assertJsonPath('data.evaluation.threshold_hours', 18)
            ->assertJsonPath('data.evaluation.exceeds_threshold', true)
            ->assertJsonPath('data.evaluation.severity', ShipmentException::SEVERITY_MEDIUM);

        // 3. SEA->Australia Tier (24h threshold, +28h delay -> HIGH & exceeds 24h threshold)
        $shipAu = Shipment::factory()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'origin_id' => $originSgp->id,
            'destination_id' => $destAu->id,
            'expected_delivery_at' => $expected->copy(),
            'estimated_delivery_at' => $expected->copy(),
        ]);

        $this->withToken($tokenOp)->patchJson("/api/v1/shipments/{$shipAu->id}/eta", [
            'estimated_delivery_at' => $expected->copy()->addHours(28)->toISOString(),
            'reason' => 'Kekurangan dokumen bea cukai Sydney',
        ])->assertStatus(200)
            ->assertJsonPath('data.evaluation.is_delayed', true)
            ->assertJsonPath('data.evaluation.delay_hours', 28)
            ->assertJsonPath('data.evaluation.threshold_hours', 24)
            ->assertJsonPath('data.evaluation.exceeds_threshold', true)
            ->assertJsonPath('data.evaluation.severity', ShipmentException::SEVERITY_HIGH);

        // Global KPI endpoint accurately averages the 3 delays: (6 + 20 + 28) / 3 = 18.0h
        $this->withToken($tokenOp)->getJson('/api/v1/analytics/kpis')
            ->assertStatus(200)
            ->assertJsonPath('data.delayed_count', 3)
            ->assertJsonPath('data.average_delay_hours', 18);
    }
}

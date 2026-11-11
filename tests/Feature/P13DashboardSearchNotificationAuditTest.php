<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P13DashboardSearchNotificationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_bento_grid_and_hero_selection(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Bento Grid System Overview')
            ->assertSee('Arsitektur Modern')
            ->assertSee('Pilih Skenario Rute Dunia (Hero Selection)')
            ->assertSee('SEA &rarr; Eropa', false)
            ->assertSee('Asia &rarr; Amerika', false)
            ->assertSee('SEA &rarr; Australia', false);
    }

    public function test_dashboard_renders_live_chart_js_payload_and_role_sidebar_consoles(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create(['name' => 'Maersk Ocean Line']);
        $origin = Location::factory()->create();
        $dest = Location::factory()->create();

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        ShipmentException::create([
            'shipment_id' => $shipment->id,
            'type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => ShipmentException::SEVERITY_HIGH,
            'status' => ShipmentException::STATUS_OPEN,
            'description' => 'Tinjauan Dokumen Bea Cukai di Pelabuhan Rotterdam.',
            'detected_at' => now(),
        ]);

        $adminRes = $this->actingAs($admin)->get('/dashboard');
        $adminRes->assertStatus(200)
            ->assertSee('Konsol Admin')
            ->assertSee('Log Audit Sistem')
            ->assertSee('statusChart')
            ->assertSee('carrierChart')
            ->assertSee('exceptionChart')
            ->assertSee('Maersk Ocean Line')
            ->assertSee('CUSTOMS_HOLD');

        $operatorRes = $this->actingAs($operator)->get('/dashboard');
        $operatorRes->assertStatus(200)
            ->assertSee('Konsol Staff')
            ->assertDontSee('Log Audit Sistem');

        $customerRes = $this->actingAs($customer)->get('/dashboard');
        $customerRes->assertStatus(200)
            ->assertSee('Portal Klien')
            ->assertDontSee('Log Audit Sistem');
    }

    public function test_advanced_filtering_and_sorting_on_web_and_api(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();

        $carrierA = Carrier::factory()->create(['name' => 'Carrier Alpha']);
        $carrierB = Carrier::factory()->create(['name' => 'Carrier Beta']);

        $portOrigin1 = Location::factory()->create(['name' => 'Port Jakarta']);
        $portOrigin2 = Location::factory()->create(['name' => 'Port Busan']);
        $portDest1 = Location::factory()->create(['name' => 'Port Rotterdam']);
        $portDest2 = Location::factory()->create(['name' => 'Port Sydney']);

        Shipment::factory()->create([
            'tracking_number' => 'CT-20261101-A001',
            'customer_id' => $customer->id,
            'carrier_id' => $carrierA->id,
            'origin_id' => $portOrigin1->id,
            'destination_id' => $portDest1->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'is_delayed' => true,
            'delay_hours' => 18,
            'estimated_delivery_at' => now()->addDays(2),
            'created_at' => now()->subDays(5),
        ]);

        Shipment::factory()->create([
            'tracking_number' => 'CT-20261110-B002',
            'customer_id' => $customer->id,
            'carrier_id' => $carrierB->id,
            'origin_id' => $portOrigin2->id,
            'destination_id' => $portDest2->id,
            'status' => Shipment::STATUS_BOOKED,
            'is_delayed' => false,
            'delay_hours' => 0,
            'estimated_delivery_at' => now()->addDays(10),
            'created_at' => now()->subDay(),
        ]);

        // Filter by origin_id + destination_id + is_delayed=1 on Web
        $webFiltered = $this->actingAs($operator)->get('/shipments?' . http_build_query([
            'origin_id' => $portOrigin1->id,
            'destination_id' => $portDest1->id,
            'is_delayed' => '1',
        ]));
        $webFiltered->assertStatus(200)
            ->assertSee('CT-20261101-A001')
            ->assertDontSee('CT-20261110-B002');

        // Filter by carrier_id + is_delayed=1 + sort_by via API
        $token = $operator->createToken('test')->plainTextToken;

        $apiDelayed = $this->withToken($token)->getJson('/api/v1/shipments?is_delayed=1&carrier_id=' . $carrierA->id);
        $apiDelayed->assertStatus(200)
            ->assertJsonPath('success', true);
        $ids = collect($apiDelayed->json('data.items'))->pluck('tracking_number')->all();
        $this->assertEquals(['CT-20261101-A001'], $ids);

        $apiSortedEtaAsc = $this->withToken($token)->getJson('/api/v1/shipments?sort_by=eta_asc');
        $apiSortedEtaAsc->assertStatus(200);
        $orderedNumbers = collect($apiSortedEtaAsc->json('data.items'))->pluck('tracking_number')->all();
        $this->assertEquals(['CT-20261101-A001', 'CT-20261110-B002'], $orderedNumbers);

        $apiSortedEtaDesc = $this->withToken($token)->getJson('/api/v1/shipments?sort_by=eta_desc');
        $orderedDesc = collect($apiSortedEtaDesc->json('data.items'))->pluck('tracking_number')->all();
        $this->assertEquals(['CT-20261110-B002', 'CT-20261101-A001'], $orderedDesc);
    }

    public function test_notifications_are_created_isolated_and_marked_as_read_via_web_and_api(): void
    {
        $operator = User::factory()->operator()->create();
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $dest = Location::factory()->create();

        // Creating a shipment triggers customer notification #1 & SHIPMENT_CREATED audit log
        $this->actingAs($operator)->post('/shipments', [
            'customer_id' => $customerA->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'estimated_delivery_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ])->assertRedirect();

        $shipment = Shipment::latest('id')->first();
        $this->assertNotNull($shipment);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $customerA->id,
            'shipment_id' => $shipment->id,
            'type' => AppNotification::TYPE_TRACKING_UPDATE,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'SHIPMENT_CREATED',
            'entity_type' => 'Shipment',
            'entity_id' => $shipment->id,
        ]);

        // Valid state transition CREATED -> BOOKED triggers notification #2
        $this->actingAs($operator)->post("/shipments/{$shipment->tracking_number}/events", [
            'status' => Shipment::STATUS_BOOKED,
            'location_id' => $origin->id,
            'description' => 'Muatan telah dijadwalkan ke kapal pelabuhan asal.',
        ])->assertRedirect();

        $notif = AppNotification::where('user_id', $customerA->id)->latest('id')->first();
        $this->assertNotNull($notif);
        $this->assertNull($notif->read_at);

        // Customer B CANNOT mark Customer A's notification as read (Web 403 & API 403)
        $this->actingAs($customerB)->patch("/notifications/{$notif->id}/read")->assertStatus(403);
        $tokenB = $customerB->createToken('test-b')->plainTextToken;
        $this->withToken($tokenB)->patchJson("/api/v1/notifications/{$notif->id}/read")->assertStatus(403);

        // Customer A views notifications & marks single notification as read via Web
        $this->actingAs($customerA)->get('/notifications')
            ->assertStatus(200)
            ->assertSee($notif->title);

        $this->actingAs($customerA)->patch("/notifications/{$notif->id}/read")
            ->assertRedirect('/notifications');

        $this->assertNotNull($notif->fresh()->read_at);

        // Customer A checks remaining unread (1 left from creation) & marks all read via API
        $tokenA = $customerA->createToken('test-a')->plainTextToken;
        $this->withToken($tokenA)->getJson('/api/v1/notifications')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.unread_count', 1);

        $this->withToken($tokenA)->postJson('/api/v1/notifications/read-all')
            ->assertStatus(200)
            ->assertJsonPath('data.marked_count', 1);

        $this->assertEquals(0, AppNotification::where('user_id', $customerA->id)->whereNull('read_at')->count());
    }

    public function test_audit_logs_enforce_admin_only_rbac_and_record_exceptions(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);

        // Operator reports exception -> AuditLog EXCEPTION_REPORTED + Customer notification
        $tokenOp = $operator->createToken('op')->plainTextToken;
        $excRes = $this->withToken($tokenOp)->postJson("/api/v1/shipments/{$shipment->id}/exceptions", [
            'type' => ShipmentException::TYPE_WEATHER_DELAY,
            'severity' => ShipmentException::SEVERITY_HIGH,
            'description' => 'Badai Samudra Pasifik mengharuskan penyesuaian rute pelayaran.',
        ]);
        $excRes->assertStatus(201);
        $excId = $excRes->json('data.id');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'EXCEPTION_REPORTED',
            'entity_type' => 'ShipmentException',
            'entity_id' => $excId,
        ]);

        // Operator & Customer denied /audit-logs & /api/v1/audit-logs (403)
        $this->actingAs($operator)->get('/audit-logs')->assertStatus(403);
        $this->actingAs($customer)->get('/audit-logs')->assertStatus(403);
        $this->withToken($tokenOp)->getJson('/api/v1/audit-logs')->assertStatus(403);

        $tokenCust = $customer->createToken('cust')->plainTextToken;
        $this->withToken($tokenCust)->getJson('/api/v1/audit-logs')->assertStatus(403);

        // Admin succeeds on both Web & API and filters logs cleanly
        $this->actingAs($admin)->get('/audit-logs?action=EXCEPTION_REPORTED')
            ->assertStatus(200)
            ->assertSee('EXCEPTION_REPORTED')
            ->assertSee('Jejak Audit Aktivitas Sistem');

        $tokenAdmin = $admin->createToken('admin')->plainTextToken;
        $this->withToken($tokenAdmin)->getJson('/api/v1/audit-logs?action=EXCEPTION_REPORTED')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.items');
    }
}

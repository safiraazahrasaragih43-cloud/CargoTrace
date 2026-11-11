<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_report_exception_and_transitions_shipment_status(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $expected = now()->addDays(5);
        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'expected_delivery_at' => $expected,
            'estimated_delivery_at' => $expected,
        ]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/exceptions", [
                'type' => ShipmentException::TYPE_CUSTOMS_HOLD,
                'severity' => ShipmentException::SEVERITY_HIGH,
                'description' => 'Customs inspection required on lithium cargo.',
                'new_estimated_delivery_at' => (clone $expected)->addHours(18)->toISOString(),
            ]);

        $res->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'type' => ShipmentException::TYPE_CUSTOMS_HOLD,
                    'severity' => ShipmentException::SEVERITY_HIGH,
                    'status' => ShipmentException::STATUS_OPEN,
                ],
            ]);

        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_EXCEPTION, $shipment->status);
        $this->assertEquals(18, $shipment->delay_hours);
        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'status' => Shipment::STATUS_EXCEPTION,
        ]);
    }

    public function test_customer_cannot_report_or_resolve_exception(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('cust')->plainTextToken;

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/exceptions", [
                'type' => ShipmentException::TYPE_WEATHER_DELAY,
                'severity' => ShipmentException::SEVERITY_LOW,
                'description' => 'Unauthorized exception.',
            ])
            ->assertStatus(403);

        $exception = ShipmentException::factory()->create([
            'shipment_id' => $shipment->id,
            'status' => ShipmentException::STATUS_OPEN,
        ]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/exceptions/{$exception->id}", [
                'status' => ShipmentException::STATUS_RESOLVED,
            ])
            ->assertStatus(403);
    }

    public function test_customer_can_view_own_exceptions_but_forbidden_from_others(): void
    {
        $custA = User::factory()->customer()->create();
        $custB = User::factory()->customer()->create();

        $shipA = Shipment::factory()->create(['customer_id' => $custA->id]);
        $shipB = Shipment::factory()->create(['customer_id' => $custB->id]);

        ShipmentException::factory()->create(['shipment_id' => $shipA->id]);
        ShipmentException::factory()->create(['shipment_id' => $shipB->id]);

        $tokenA = $custA->createToken('custA')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson("/api/v1/shipments/{$shipA->id}/exceptions")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson("/api/v1/shipments/{$shipB->id}/exceptions")
            ->assertStatus(403);
    }

    public function test_resolving_all_exceptions_resumes_shipment_to_in_transit(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_EXCEPTION,
        ]);

        $exception = ShipmentException::factory()->create([
            'shipment_id' => $shipment->id,
            'type' => ShipmentException::TYPE_DOCUMENTATION_ISSUE,
            'status' => ShipmentException::STATUS_OPEN,
            'detected_at' => now()->subHours(5),
        ]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/exceptions/{$exception->id}", [
                'status' => ShipmentException::STATUS_RESOLVED,
                'resolution_notes' => 'Bill of lading amended and validated.',
            ]);

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => ShipmentException::STATUS_RESOLVED,
                    'resolution_notes' => 'Bill of lading amended and validated.',
                ],
            ]);

        $this->assertNotNull($res->json('data.resolved_at'));
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->status);
    }

    public function test_cannot_report_exception_on_terminal_delivered_shipment(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_DELIVERED,
        ]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/exceptions", [
                'type' => ShipmentException::TYPE_DAMAGED,
                'severity' => ShipmentException::SEVERITY_HIGH,
                'description' => 'Too late exception.',
            ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['shipment']);
    }

    public function test_api_404_never_exposes_stack_trace(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin')->plainTextToken;

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/shipments/999999');

        $res->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ])
            ->assertJsonMissingPath('trace')
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('file');
    }
}


<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\EtaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtaAndDelayRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_detects_all_three_global_corridor_thresholds(): void
    {
        $etaService = app(EtaService::class);
        $origin = Location::factory()->create(['country' => 'Indonesia']);

        $euDest = Location::factory()->create(['country' => 'Netherlands']);
        $naDest = Location::factory()->create(['country' => 'United States']);
        $auDest = Location::factory()->create(['country' => 'Australia']);

        $sEu = Shipment::factory()->create(['origin_id' => $origin->id, 'destination_id' => $euDest->id]);
        $sNa = Shipment::factory()->create(['origin_id' => $origin->id, 'destination_id' => $naDest->id]);
        $sAu = Shipment::factory()->create(['origin_id' => $origin->id, 'destination_id' => $auDest->id]);

        $this->assertEquals(12, $etaService->detectCorridor($sEu)['delay_threshold_hours']);
        $this->assertEquals(18, $etaService->detectCorridor($sNa)['delay_threshold_hours']);
        $this->assertEquals(24, $etaService->detectCorridor($sAu)['delay_threshold_hours']);
    }

    public function test_deterministic_delay_severity_tiers_low_medium_high(): void
    {
        $etaService = app(EtaService::class);
        $origin = Location::factory()->create(['country' => 'Indonesia']);
        $euDest = Location::factory()->create(['country' => 'Netherlands']);
        $auDest = Location::factory()->create(['country' => 'Australia']);

        $baseTime = now()->addDays(5);

        // 0h -> ON_TIME
        $sOnTime = Shipment::factory()->create([
            'origin_id' => $origin->id,
            'destination_id' => $euDest->id,
            'expected_delivery_at' => $baseTime,
            'estimated_delivery_at' => $baseTime,
        ]);
        $res0 = $etaService->evaluateShipment($sOnTime);
        $this->assertEquals('ON_TIME', $res0['severity']);
        $this->assertFalse($res0['is_delayed']);

        // 4h -> LOW (within EU 12h limit)
        $sLow = Shipment::factory()->create([
            'origin_id' => $origin->id,
            'destination_id' => $euDest->id,
            'expected_delivery_at' => $baseTime,
            'estimated_delivery_at' => (clone $baseTime)->addHours(4),
        ]);
        $res4 = $etaService->evaluateShipment($sLow);
        $this->assertEquals('LOW', $res4['severity']);
        $this->assertTrue($res4['is_delayed']);
        $this->assertFalse($res4['exceeds_threshold']);

        // 16h -> MEDIUM (exceeds EU 12h limit, but within AU 24h limit)
        $sMedEu = Shipment::factory()->create([
            'origin_id' => $origin->id,
            'destination_id' => $euDest->id,
            'expected_delivery_at' => $baseTime,
            'estimated_delivery_at' => (clone $baseTime)->addHours(16),
        ]);
        $res16Eu = $etaService->evaluateShipment($sMedEu);
        $this->assertEquals('MEDIUM', $res16Eu['severity']);
        $this->assertTrue($res16Eu['exceeds_threshold']);

        $sMedAu = Shipment::factory()->create([
            'origin_id' => $origin->id,
            'destination_id' => $auDest->id,
            'expected_delivery_at' => $baseTime,
            'estimated_delivery_at' => (clone $baseTime)->addHours(16),
        ]);
        $res16Au = $etaService->evaluateShipment($sMedAu);
        $this->assertEquals('MEDIUM', $res16Au['severity']);
        $this->assertFalse($res16Au['exceeds_threshold']);

        // 28h -> HIGH (exceeds all limits)
        $sHigh = Shipment::factory()->create([
            'origin_id' => $origin->id,
            'destination_id' => $auDest->id,
            'expected_delivery_at' => $baseTime,
            'estimated_delivery_at' => (clone $baseTime)->addHours(28),
        ]);
        $res28 = $etaService->evaluateShipment($sHigh);
        $this->assertEquals('HIGH', $res28['severity']);
        $this->assertTrue($res28['exceeds_threshold']);
    }

    public function test_operator_can_recalculate_eta_via_api_and_customer_cannot(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();

        $opsToken = $operator->createToken('ops')->plainTextToken;
        $custToken = $customer->createToken('cust')->plainTextToken;

        $euDest = Location::factory()->create(['country' => 'Netherlands']);
        $expected = now()->addDays(4);

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'destination_id' => $euDest->id,
            'expected_delivery_at' => $expected,
            'estimated_delivery_at' => $expected,
        ]);

        // Customer blocked (403)
        $this->withHeader('Authorization', "Bearer {$custToken}")
            ->patchJson("/api/v1/shipments/{$shipment->id}/eta", [
                'estimated_delivery_at' => (clone $expected)->addHours(15)->toISOString(),
            ])
            ->assertStatus(403);

        $this->app['auth']->forgetGuards();

        // Operator allowed (200) & updates delay fields
        $res = $this->withHeader('Authorization', "Bearer {$opsToken}")
            ->patchJson("/api/v1/shipments/{$shipment->id}/eta", [
                'estimated_delivery_at' => (clone $expected)->addHours(15)->toISOString(),
                'reason' => 'Suez Canal backlog',
            ]);

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'evaluation' => [
                        'delay_hours' => 15,
                        'is_delayed' => true,
                        'exceeds_threshold' => true,
                        'severity' => 'MEDIUM',
                    ],
                ],
            ]);

        $shipment->refresh();
        $this->assertEquals(15, $shipment->delay_hours);
        $this->assertTrue($shipment->is_delayed);
        $this->assertStringContainsString('Suez Canal backlog', $shipment->delay_reason);
    }

    public function test_adding_late_tracking_event_triggers_eta_delay_detection(): void
    {
        $operator = User::factory()->operator()->create();
        $opsToken = $operator->createToken('ops')->plainTextToken;

        $expected = now()->subHours(10);
        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'expected_delivery_at' => $expected,
            'estimated_delivery_at' => $expected,
        ]);

        // Add tracking event at current time (10h overdue against expected arrival)
        $res = $this->withHeader('Authorization', "Bearer {$opsToken}")
            ->postJson("/api/v1/shipments/{$shipment->id}/tracking", [
                'status' => Shipment::STATUS_IN_TRANSIT,
                'location_name' => 'Singapore Anchorage',
                'description' => 'Awaiting berth slot.',
                'event_time' => now()->toISOString(),
            ]);

        $res->assertStatus(201);

        $shipment->refresh();
        $this->assertEquals(10, $shipment->delay_hours);
        $this->assertTrue($shipment->is_delayed);
    }

    public function test_global_kpis_endpoint_returns_combined_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin')->plainTextToken;

        $delivered = Shipment::factory()->create([
            'status' => Shipment::STATUS_DELIVERED,
            'delay_hours' => 0,
        ]);

        $delayed = Shipment::factory()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'delay_hours' => 20,
            'is_delayed' => true,
        ]);

        ShipmentException::factory()->create([
            'shipment_id' => $delivered->id,
            'status' => ShipmentException::STATUS_RESOLVED,
            'detected_at' => now()->subHours(6),
            'resolved_at' => now()->subHours(2),
        ]);

        ShipmentException::factory()->create([
            'shipment_id' => $delayed->id,
            'status' => ShipmentException::STATUS_OPEN,
        ]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/analytics/kpis');

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_shipments' => 2,
                    'delivered_count' => 1,
                    'delayed_count' => 1,
                    'on_time_delivery_rate' => 100.0,
                    'average_delay_hours' => 20.0,
                    'open_exceptions_count' => 1,
                    'avg_exception_resolution_hours' => 4.0,
                ],
            ]);
    }
}

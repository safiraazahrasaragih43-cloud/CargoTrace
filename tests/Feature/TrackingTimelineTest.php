<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_record_tracking_event_and_updates_shipment_state(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $location = Location::factory()->create([
            'name' => 'Port of Singapore',
            'latitude' => 1.2644,
            'longitude' => 103.8222,
        ]);

        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_BOOKED,
            'departure_at' => null,
        ]);

        $payload = [
            'status' => Shipment::STATUS_PICKED_UP,
            'location_id' => $location->id,
            'description' => 'Container sealed and loaded onto vessel.',
            'event_time' => now()->toISOString(),
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/tracking", $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => Shipment::STATUS_PICKED_UP,
                    'location_name' => "Port of Singapore ({$location->code})",
                    'description' => 'Container sealed and loaded onto vessel.',
                ],
            ]);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'status' => Shipment::STATUS_PICKED_UP,
            'created_by' => $operator->id,
        ]);

        // Verify shipment state auto-updated
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_PICKED_UP, $shipment->status);
        $this->assertEquals($location->id, $shipment->current_location_id);
        $this->assertNotNull($shipment->departure_at);
    }

    public function test_operator_can_record_intermediate_in_transit_scan_keeping_same_status(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $loc1 = Location::factory()->create();
        $loc2 = Location::factory()->create(['name' => 'Dubai Hub']);

        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'current_location_id' => $loc1->id,
        ]);

        $payload = [
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_id' => $loc2->id,
            'description' => 'Discharged at intermediate container yard for refuel.',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/tracking", $payload);

        $response->assertStatus(201);

        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->status);
        $this->assertEquals($loc2->id, $shipment->current_location_id);
    }

    public function test_customer_forbidden_from_recording_tracking_event(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('cust')->plainTextToken;

        $shipment = Shipment::factory()->create([
            'customer_id' => $customer->id,
            'status' => Shipment::STATUS_BOOKED,
        ]);

        $payload = [
            'status' => Shipment::STATUS_PICKED_UP,
            'description' => 'Customer trying to log event.',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/tracking", $payload);

        $response->assertStatus(403);
    }

    public function test_cannot_record_event_on_terminal_shipment(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_DELIVERED,
        ]);

        $payload = [
            'status' => Shipment::STATUS_IN_TRANSIT,
            'description' => 'Invalid attempt to resurrect delivered consignment.',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/tracking", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_cannot_make_invalid_status_transition(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        // Shipment created cannot jump straight to DELIVERED
        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_CREATED,
        ]);

        $payload = [
            'status' => Shipment::STATUS_DELIVERED,
            'description' => 'Premature delivery attempt without transit.',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$shipment->id}/tracking", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_customer_can_view_own_timeline_but_forbidden_from_others(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();

        $shipmentA = Shipment::factory()->create(['customer_id' => $customerA->id]);
        $shipmentB = Shipment::factory()->create(['customer_id' => $customerB->id]);

        TrackingEvent::factory()->create(['shipment_id' => $shipmentA->id]);
        TrackingEvent::factory()->create(['shipment_id' => $shipmentB->id]);

        $tokenA = $customerA->createToken('custA')->plainTextToken;

        // Customer A can view own timeline
        $responseA = $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson("/api/v1/shipments/{$shipmentA->id}/tracking");

        $responseA->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'status', 'description', 'event_time'],
                ],
            ]);

        // Customer A cannot view Customer B's timeline
        $responseB = $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson("/api/v1/shipments/{$shipmentB->id}/tracking");

        $responseB->assertStatus(403);
    }

    public function test_public_tracking_endpoint_returns_chronological_events(): void
    {
        $shipment = Shipment::factory()->create([
            'tracking_number' => 'CT-20261107-CHRONO',
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $e1 = TrackingEvent::factory()->create([
            'shipment_id' => $shipment->id,
            'status' => Shipment::STATUS_CREATED,
            'event_time' => now()->subDays(3),
        ]);

        $e2 = TrackingEvent::factory()->create([
            'shipment_id' => $shipment->id,
            'status' => Shipment::STATUS_PICKED_UP,
            'event_time' => now()->subDays(1),
        ]);

        $response = $this->getJson('/api/v1/tracking/CT-20261107-CHRONO');

        $response->assertStatus(200);
        $events = $response->json('data.tracking_events');
        $this->assertCount(2, $events);
        $this->assertEquals($e1->id, $events[0]['id']);
        $this->assertEquals($e2->id, $events[1]['id']);
    }

    public function test_web_timeline_page_renders_successfully(): void
    {
        $customer = User::factory()->customer()->create();
        $shipment = Shipment::factory()->create([
            'tracking_number' => 'CT-20261107-WEB',
            'customer_id' => $customer->id,
        ]);

        TrackingEvent::factory()->create([
            'shipment_id' => $shipment->id,
            'description' => 'Public arrival checkpoint at port.',
        ]);

        $response = $this->get("/shipments/CT-20261107-WEB");

        $response->assertStatus(200)
            ->assertSee('CT-20261107-WEB')
            ->assertSee('Public arrival checkpoint at port.');
    }

    public function test_web_store_event_creates_checkpoint_and_redirects(): void
    {
        $operator = User::factory()->operator()->create();
        $shipment = Shipment::factory()->create([
            'tracking_number' => 'CT-20261107-ACT',
            'status' => Shipment::STATUS_BOOKED,
        ]);

        $response = $this->actingAs($operator)
            ->post("/shipments/CT-20261107-ACT/events", [
                'status' => Shipment::STATUS_PICKED_UP,
                'location_name' => 'Depot 5 Gate',
                'description' => 'Loaded onto feeder truck.',
            ]);

        $response->assertRedirect('/shipments/CT-20261107-ACT');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'status' => Shipment::STATUS_PICKED_UP,
            'description' => 'Loaded onto feeder truck.',
        ]);
    }
}

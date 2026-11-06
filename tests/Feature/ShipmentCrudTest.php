<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_create_shipment_with_items(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        $payload = [
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $destination->id,
            'estimated_delivery_at' => now()->addDays(7)->toISOString(),
            'notes' => 'Urgent electronics',
            'items' => [
                [
                    'description' => 'Server Rack Modules',
                    'quantity' => 10,
                    'weight_kg' => 450.50,
                    'dimensions' => '100x60x40 cm',
                    'hs_code' => '8471.50',
                    'value_amount' => 35000.00,
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/shipments', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'tracking_number',
                    'status',
                    'items' => [
                        '*' => ['description', 'quantity', 'weight_kg'],
                    ],
                ],
            ]);

        $this->assertStringStartsWith('CT-', $response->json('data.tracking_number'));
        $this->assertEquals(Shipment::STATUS_CREATED, $response->json('data.status'));
        $this->assertDatabaseHas('shipment_items', ['description' => 'Server Rack Modules']);
    }

    public function test_customer_only_sees_own_shipments(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();

        $shipmentA = Shipment::factory()->create(['customer_id' => $customerA->id]);
        $shipmentB = Shipment::factory()->create(['customer_id' => $customerB->id]);

        $tokenA = $customerA->createToken('custA')->plainTextToken;

        // Listing scoped to customerA
        $response = $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson('/api/v1/shipments');

        $response->assertStatus(200);
        $ids = collect($response->json('data.items'))->pluck('id')->all();
        $this->assertContains($shipmentA->id, $ids);
        $this->assertNotContains($shipmentB->id, $ids);

        // Accessing customerB's shipment fails (403 Forbidden via policy)
        $detailResponse = $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson("/api/v1/shipments/{$shipmentB->id}");

        $detailResponse->assertStatus(403);
    }

    public function test_public_can_track_shipment_without_authentication(): void
    {
        $shipment = Shipment::factory()->create([
            'tracking_number' => 'CT-20261107-TEST',
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        $response = $this->getJson('/api/v1/tracking/CT-20261107-TEST');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'tracking_number' => 'CT-20261107-TEST',
                    'status' => Shipment::STATUS_IN_TRANSIT,
                ],
            ]);
    }

    public function test_cannot_transition_from_terminal_delivered_status(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $shipment = Shipment::factory()->create([
            'status' => Shipment::STATUS_DELIVERED,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/v1/shipments/{$shipment->id}", [
                'status' => Shipment::STATUS_IN_TRANSIT,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}

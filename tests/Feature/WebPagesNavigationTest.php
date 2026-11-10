<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPagesNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_navigate_all_sidebar_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/shipments')->assertStatus(200)->assertSee('Daftar Pengiriman Global');
        $this->actingAs($admin)->get('/track')->assertStatus(200)->assertSee('Lacak pengiriman apa pun');
        $this->actingAs($admin)->get('/carriers')->assertStatus(200)->assertSee('Daftar Ekspedisi');
        $this->actingAs($admin)->get('/locations')->assertStatus(200)->assertSee('Daftar Pelabuhan');
    }

    public function test_operator_can_create_shipment_from_web_manifest(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customer()->create();
        $carrier = Carrier::factory()->create();
        $origin = Location::factory()->create();
        $destination = Location::factory()->create();

        $response = $this->actingAs($operator)->post('/shipments', [
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $destination->id,
            'estimated_delivery_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'description' => 'Web Created Electronics',
                    'quantity' => 5,
                    'weight_kg' => 120.0,
                    'value_amount' => 4500.0,
                ],
            ],
        ]);

        $shipment = Shipment::latest('id')->first();
        $this->assertNotNull($shipment);
        $response->assertRedirect(route('shipments.show', $shipment->tracking_number));
        $this->assertDatabaseHas('shipment_items', ['description' => 'Web Created Electronics']);
    }

    public function test_admin_can_create_and_delete_carrier_from_web(): void
    {
        $admin = User::factory()->admin()->create();

        $storeRes = $this->actingAs($admin)->post('/carriers', [
            'name' => 'Hapag-Lloyd AG',
            'code' => 'HLCU',
            'is_active' => true,
        ]);
        $storeRes->assertRedirect('/carriers');
        $this->assertDatabaseHas('carriers', ['code' => 'HLCU']);

        $carrier = Carrier::where('code', 'HLCU')->first();
        $delRes = $this->actingAs($admin)->delete("/carriers/{$carrier->id}");
        $delRes->assertRedirect('/carriers');
        $this->assertDatabaseMissing('carriers', ['id' => $carrier->id]);
    }

    public function test_operator_can_create_location_from_web(): void
    {
        $operator = User::factory()->operator()->create();

        $res = $this->actingAs($operator)->post('/locations', [
            'name' => 'Port of Surabaya',
            'code' => 'IDSUB',
            'type' => Location::TYPE_PORT,
            'city' => 'Surabaya',
            'country' => 'Indonesia',
            'latitude' => -7.2036,
            'longitude' => 112.7317,
        ]);

        $res->assertRedirect('/locations');
        $this->assertDatabaseHas('locations', ['code' => 'IDSUB']);
    }

    public function test_customer_allowed_on_shipments_and_locations_but_forbidden_on_carriers(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get('/shipments')->assertStatus(200);
        $this->actingAs($customer)->get('/track')->assertStatus(200);
        $this->actingAs($customer)->get('/locations')->assertStatus(200);
        $this->actingAs($customer)->get('/carriers')->assertStatus(403);
    }
}


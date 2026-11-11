<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalIntegrationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_weather_api_returns_formatted_data_with_disclaimer(): void
    {
        Http::fake([
            '*open-meteo.com*' => Http::response([
                'current_weather' => [
                    'temperature' => 29.4,
                    'windspeed' => 14.2,
                    'weathercode' => 2,
                    'time' => '2026-11-11T10:00',
                ],
            ], 200),
        ]);

        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $hub = Location::factory()->create([
            'name' => 'Port of Singapore',
            'latitude' => 1.2644,
            'longitude' => 103.8220,
        ]);

        $shipment = Shipment::factory()->create([
            'current_location_id' => $hub->id,
        ]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/shipments/{$shipment->id}/weather");

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => true,
                    'status' => 'OK',
                    'temperature_c' => 29.4,
                    'wind_speed_kmh' => 14.2,
                    'condition' => 'Berawan Sebagian / Mendung',
                ],
            ]);

        $this->assertStringContainsString('bukan klaim bukti prediksi keterlambatan', $res->json('data.disclaimer'));
    }

    public function test_weather_api_handles_timeout_gracefully(): void
    {
        Http::fake(function () {
            throw new ConnectionException('cURL error 28: Operation timed out');
        });

        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $hub = Location::factory()->create(['latitude' => 51.9244, 'longitude' => 4.4777]);
        $shipment = Shipment::factory()->create(['current_location_id' => $hub->id]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/shipments/{$shipment->id}/weather");

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => false,
                    'status' => 'TIMEOUT_OR_NETWORK_ERROR',
                ],
            ]);
    }

    public function test_weather_api_handles_http_500_error_gracefully(): void
    {
        Http::fake([
            '*open-meteo.com*' => Http::response(['error' => 'Service down'], 500),
        ]);

        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $hub = Location::factory()->create(['latitude' => -33.9722, 'longitude' => 151.2167]);
        $shipment = Shipment::factory()->create(['current_location_id' => $hub->id]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/shipments/{$shipment->id}/weather");

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => false,
                    'status' => 'API_ERROR',
                    'http_status' => 500,
                ],
            ]);
    }

    public function test_weather_api_handles_empty_payload_gracefully(): void
    {
        Http::fake([
            '*open-meteo.com*' => Http::response([], 200),
        ]);

        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $hub = Location::factory()->create(['latitude' => -37.8409, 'longitude' => 144.9125]);
        $shipment = Shipment::factory()->create(['current_location_id' => $hub->id]);

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/shipments/{$shipment->id}/weather");

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => false,
                    'status' => 'EMPTY_DATA',
                ],
            ]);
    }

    public function test_geocoding_api_resolves_coordinates(): void
    {
        Http::fake([
            '*nominatim.openstreetmap.org*' => Http::response([
                [
                    'display_name' => 'Port of Tokyo, Minato, Tokyo, Japan',
                    'lat' => '35.619231',
                    'lon' => '139.778291',
                    'type' => 'harbour',
                    'address' => [
                        'city' => 'Tokyo',
                        'country' => 'Japan',
                    ],
                ],
            ], 200),
        ]);

        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/integrations/geocode?q=Port+of+Tokyo');

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => true,
                    'status' => 'OK',
                    'results' => [
                        [
                            'latitude' => 35.619231,
                            'longitude' => 139.778291,
                            'city' => 'Tokyo',
                            'country' => 'Japan',
                        ],
                    ],
                ],
            ]);
    }

    public function test_geocoding_api_handles_empty_results(): void
    {
        Http::fake([
            '*nominatim.openstreetmap.org*' => Http::response([], 200),
        ]);

        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/integrations/geocode?q=NonExistentPortXYZ');

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => true,
                    'status' => 'NO_RESULTS',
                    'results' => [],
                ],
            ]);
    }

    public function test_geocoding_api_handles_upstream_error(): void
    {
        Http::fake([
            '*nominatim.openstreetmap.org*' => Http::response('Rate limit exceeded', 503),
        ]);

        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $res = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/integrations/geocode?q=Singapore');

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'available' => false,
                    'status' => 'API_ERROR',
                ],
            ]);
    }

    public function test_customer_cannot_query_weather_for_other_customers_shipment(): void
    {
        $custA = User::factory()->customer()->create();
        $custB = User::factory()->customer()->create();
        $shipB = Shipment::factory()->create(['customer_id' => $custB->id]);

        $tokenA = $custA->createToken('custA')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson("/api/v1/shipments/{$shipB->id}/weather")
            ->assertStatus(403);
    }

    public function test_web_views_include_leaflet_map_container_and_osm_attribution(): void
    {
        $shipment = Shipment::factory()->create(['tracking_number' => 'CT-20261111-MAP1']);

        $this->get("/shipments/{$shipment->tracking_number}")
            ->assertStatus(200)
            ->assertSee('shipmentMap')
            ->assertSee('openstreetmap.org/copyright')
            ->assertSee('Kondisi Cuaca Lokasi');

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->get('/locations')
            ->assertStatus(200)
            ->assertSee('locationsWorldMap')
            ->assertSee('openstreetmap.org/copyright');
    }
}

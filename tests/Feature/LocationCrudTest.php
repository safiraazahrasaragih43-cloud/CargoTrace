<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_create_and_update_location(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/locations', [
                'name' => 'Tokyo Port',
                'code' => 'JPTYO',
                'type' => Location::TYPE_PORT,
                'address' => 'Koto City, Tokyo',
                'city' => 'Tokyo',
                'country' => 'Japan',
                'latitude' => 35.6190,
                'longitude' => 139.7960,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Tokyo Port',
                    'code' => 'JPTYO',
                ],
            ]);

        $locationId = $response->json('data.id');

        $updateRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/v1/locations/{$locationId}", [
                'name' => 'Tokyo International Container Terminal',
            ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['name' => 'Tokyo International Container Terminal'],
            ]);
    }

    public function test_customer_can_view_locations_but_cannot_modify(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('cust')->plainTextToken;
        $location = Location::factory()->create();

        // Customer can list
        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/locations')
            ->assertStatus(200);

        // Customer can view single
        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/locations/{$location->id}")
            ->assertStatus(200);

        // Customer cannot create
        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/locations', [
                'name' => 'Unauthorized Port',
                'code' => 'UAPRT',
                'type' => 'PORT',
                'city' => 'X',
                'country' => 'Y',
                'latitude' => 0,
                'longitude' => 0,
            ])
            ->assertStatus(403);
    }
}

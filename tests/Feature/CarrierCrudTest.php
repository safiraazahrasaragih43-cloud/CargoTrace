<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarrierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_and_create_carrier(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/carriers', [
                'name' => 'Pacific Ocean Express',
                'code' => 'POEX',
                'contact_email' => 'contact@poex.com',
                'phone' => '+62 21 555 1234',
                'tracking_url_template' => 'https://track.poex.com/{TRACKING_NUMBER}',
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Pacific Ocean Express',
                    'code' => 'POEX',
                ],
            ]);

        $this->assertDatabaseHas('carriers', ['code' => 'POEX']);
    }

    public function test_carrier_code_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin')->plainTextToken;
        Carrier::factory()->create(['code' => 'EXPR']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/carriers', [
                'name' => 'Duplicate Express',
                'code' => 'EXPR',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_operator_can_update_carrier_but_cannot_create(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops')->plainTextToken;
        $carrier = Carrier::factory()->create(['name' => 'Initial Name']);

        // Attempt create -> 403 Forbidden
        $createRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/carriers', [
                'name' => 'Unauthorized Carrier',
                'code' => 'UAC1',
            ]);
        $createRes->assertStatus(403);

        // Attempt update -> 200 OK
        $updateRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/v1/carriers/{$carrier->id}", [
                'name' => 'Updated Operator Name',
            ]);
        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['name' => 'Updated Operator Name'],
            ]);
    }

    public function test_customer_cannot_access_carriers_api(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('cust')->plainTextToken;
        $carrier = Carrier::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/carriers');

        $response->assertStatus(403);

        $showResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/carriers/{$carrier->id}");

        $showResponse->assertStatus(403);
    }

    public function test_only_admin_can_delete_carrier(): void
    {
        $operator = User::factory()->operator()->create();
        $admin = User::factory()->admin()->create();
        $carrier = Carrier::factory()->create();

        $opsToken = $operator->createToken('ops')->plainTextToken;
        $adminToken = $admin->createToken('admin')->plainTextToken;

        // Operator delete fails
        $this->withHeader('Authorization', "Bearer {$opsToken}")
            ->deleteJson("/api/v1/carriers/{$carrier->id}")
            ->assertStatus(403);

        $this->app['auth']->forgetGuards();

        // Admin delete succeeds
        $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->deleteJson("/api/v1/carriers/{$carrier->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('carriers', ['id' => $carrier->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth:sanctum', 'role:admin'])->get('/test/admin-only', function () {
            return response()->json(['success' => true, 'message' => 'Admin granted']);
        });

        Route::middleware(['auth:sanctum', 'role:operator,admin'])->get('/test/ops-only', function () {
            return response()->json(['success' => true, 'message' => 'Ops granted']);
        });
    }

    public function test_admin_can_access_admin_endpoint(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/test/admin-only');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Admin granted']);
    }

    public function test_customer_cannot_access_admin_endpoint(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('customer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/test/admin-only');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Forbidden: You do not have permission to access this resource.',
            ]);
    }

    public function test_operator_can_access_ops_endpoint(): void
    {
        $operator = User::factory()->operator()->create();
        $token = $operator->createToken('ops-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/test/ops-only');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Ops granted']);
    }

    public function test_customer_cannot_access_ops_endpoint(): void
    {
        $customer = User::factory()->customer()->create();
        $token = $customer->createToken('cust-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/test/ops-only');

        $response->assertStatus(403);
    }
}

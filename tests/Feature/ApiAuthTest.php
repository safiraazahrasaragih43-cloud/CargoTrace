<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_health_check_returns_success(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'CargoTrace API v1 operational',
            ]);
    }

    public function test_user_can_register_via_api(): void
    {
        $payload = [
            'name' => 'API Client',
            'email' => 'api.client@cargotrace.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+62812345678',
            'company_name' => 'Global Logistics API Co',
            'device_name' => 'postman-suite',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                    'token_type',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'api.client@cargotrace.com',
                        'role' => User::ROLE_CUSTOMER,
                    ],
                    'token_type' => 'Bearer',
                ],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'api.client@cargotrace.com']);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@cargotrace.com']);

        $payload = [
            'name' => 'Duplicate Attempt',
            'email' => 'existing@cargotrace.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_via_api_and_receives_bearer_token(): void
    {
        $user = User::factory()->create([
            'email' => 'operator.api@cargotrace.com',
            'password' => bcrypt('secret1234'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'operator.api@cargotrace.com',
            'password' => 'secret1234',
            'device_name' => 'mobile-handheld',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'operator.api@cargotrace.com',
                        'role' => User::ROLE_OPERATOR,
                    ],
                    'token_type' => 'Bearer',
                ],
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_user_cannot_login_with_invalid_credentials_via_api(): void
    {
        User::factory()->create([
            'email' => 'valid@cargotrace.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'valid@cargotrace.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_authenticated_user_can_access_me_endpoint(): void
    {
        $user = User::factory()->create(['email' => 'auth.me@cargotrace.com']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'auth.me@cargotrace.com',
                    ],
                ],
            ]);
    }

    public function test_unauthenticated_request_to_me_endpoint_is_rejected(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    public function test_user_can_logout_via_api_and_revoke_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('logout-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);

        // Clear in-memory guard cache
        $this->app['auth']->forgetGuards();

        // Accessing /me with the same token must now fail (401)
        $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(401);
    }
}

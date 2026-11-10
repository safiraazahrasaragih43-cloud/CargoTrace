<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke CargoTrace');
    }

    public function test_user_can_authenticate_via_web_form(): void
    {
        $user = User::factory()->create([
            'email' => 'operator@cargotrace.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $response = $this->post('/login', [
            'email' => 'operator@cargotrace.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@cargotrace.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'user@cargotrace.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_view_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Buat Akun Baru');
    }

    public function test_user_can_register_via_web_form(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'customer.new@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'phone' => '+123456789',
            'company_name' => 'Import Logistics Co',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'customer.new@example.com',
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}


<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_indonesian(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertEquals('id', app()->getLocale());
    }

    public function test_user_can_switch_language_to_english(): void
    {
        $response = $this->get('/lang/en');

        $response->assertRedirect('/');
        $response->assertSessionHas('locale', 'en');

        // Subsequent request uses English
        $dashboardResponse = $this->withSession(['locale' => 'en'])
            ->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/dashboard');

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Operational Dashboard');
        $dashboardResponse->assertSee('Shipments Manifest');
        $dashboardResponse->assertSee('Cargo Operations Center');
        $dashboardResponse->assertSee('Welcome back');
        $dashboardResponse->assertSee('Active shipment cycles running');
        $dashboardResponse->assertSee('Europe Route');
        $dashboardResponse->assertSee('Shipment Status Distribution');
        $dashboardResponse->assertSee('Volume by Carrier');
        $dashboardResponse->assertSee('Route Exception Analysis');
        $dashboardResponse->assertSee('Recent Shipments List');
        $dashboardResponse->assertSee('Active Ocean &amp; Air Carriers', false);
        $dashboardResponse->assertSee('Global Ports &amp; Logistics Hubs', false);
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_user_can_switch_language_to_indonesian(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/lang/id');

        $response->assertRedirect('/');
        $response->assertSessionHas('locale', 'id');

        // Subsequent request uses Indonesian
        $dashboardResponse = $this->withSession(['locale' => 'id'])
            ->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/dashboard');

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard Operasional');
        $dashboardResponse->assertSee('Daftar Pengiriman');
        $this->assertEquals('id', app()->getLocale());
    }

    public function test_invalid_locale_fallback_to_indonesian(): void
    {
        $response = $this->get('/lang/es');

        $response->assertRedirect('/');
        $response->assertSessionMissing('locale');
    }

    public function test_landing_page_renders_fully_in_english_when_switched(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('lang="en"', false);
        $response->assertSee('Live Tracking');
        $response->assertSee('Bento Architecture');
        $response->assertSee('Role Matrix');
        $response->assertSee('Sign In');
        $response->assertSee('Create Account');
        $response->assertSee('Smart visibility for');
        $response->assertSee('your global cargo');
        $response->assertSee('Enter tracking number');
        $response->assertSee('Track Cargo');
        $response->assertSee('Select Global Corridor Scenario (Hero Selection)');
        $response->assertSee('12h Limit');
        $response->assertSee('SEA &rarr; Europe', false);
        $response->assertSee('Asia &rarr; America', false);
        $response->assertSee('Europe Corridor');
        $response->assertSee('Selected Corridor');
        $response->assertSee('Origin');
        $response->assertSee('Destination');
        $response->assertSee('Active Tracking Number');
        $response->assertSee('Inspect Now');
        $response->assertSee('Bento Grid System Overview');
        $response->assertSee('Modern Architecture &amp; Unified Core Features', false);
        $response->assertSee('User Role Matrix');
        $response->assertSee('Administrator');
        $response->assertSee('Full Access');
    }

    public function test_inner_console_pages_render_fully_in_english_when_switched(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Audit Logs Page
        $auditResponse = $this->withSession(['locale' => 'en'])
            ->actingAs($admin)
            ->get('/audit-logs');
        $auditResponse->assertStatus(200);
        $auditResponse->assertSee('System Activity Audit Trail');
        $auditResponse->assertSee('Admin Only');
        $auditResponse->assertSee('Security &amp; Operational Compliance', false);

        // 2. Carriers Page
        $carrierResponse = $this->withSession(['locale' => 'en'])
            ->actingAs($admin)
            ->get('/carriers');
        $carrierResponse->assertStatus(200);
        $carrierResponse->assertSee('Global Carriers &amp; Airlines Directory', false);
        $carrierResponse->assertSee('Register New Carrier');

        // 3. Locations Page
        $locationResponse = $this->withSession(['locale' => 'en'])
            ->actingAs($admin)
            ->get('/locations');
        $locationResponse->assertStatus(200);
        $locationResponse->assertSee('Global Ports &amp; Logistics Hubs Directory', false);
        $locationResponse->assertSee('Global Ports &amp; Hubs Network Map', false);

        // 4. Notifications Page
        $notifResponse = $this->withSession(['locale' => 'en'])
            ->actingAs($admin)
            ->get('/notifications');
        $notifResponse->assertStatus(200);
        $notifResponse->assertSee('Operational Notifications Center');

        // 5. Tracking Quick Page
        $trackResponse = $this->withSession(['locale' => 'en'])
            ->actingAs($admin)
            ->get('/track');
        $trackResponse->assertStatus(200);
        $trackResponse->assertSee('Real-Time REST Telemetry');
        $trackResponse->assertSee('Track Shipment');

        // 6. Login & Register Pages (as guest)
        auth()->logout();
        $loginResponse = $this->withSession(['locale' => 'en'])->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Sign in to CargoTrace');
        $loginResponse->assertSee('Quick Demo Accounts');

        $registerResponse = $this->withSession(['locale' => 'en'])->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Create New Account');
        $registerResponse->assertSee('Customer Registration');
    }
}



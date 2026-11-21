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
}


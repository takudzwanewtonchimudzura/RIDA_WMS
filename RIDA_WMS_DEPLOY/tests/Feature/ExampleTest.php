<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_uses_guest_layout_without_application_navigation(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Sign in')
            ->assertSee('RIDA RWIMS')
            ->assertDontSee('id="sidebar"');
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }

    public function test_inactive_users_cannot_sign_in_but_active_users_can(): void
    {
        $inactiveUser = User::factory()->create(['is_active' => false]);
        $this->post(route('login'), [
            'email' => $inactiveUser->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $activeUser = User::factory()->create(['is_active' => true]);
        $this->post(route('login'), [
            'email' => $activeUser->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($activeUser);
    }
}

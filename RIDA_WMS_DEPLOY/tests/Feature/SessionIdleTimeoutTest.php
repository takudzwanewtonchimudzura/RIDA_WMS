<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SessionIdleTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_requests_are_allowed_within_the_idle_window(): void
    {
        Config::set('session.idle_timeout', 20);
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->withSession(['last_activity_at' => time() - 60])
            ->get(route('dashboard'))
            ->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_is_logged_out_after_exceeding_the_idle_timeout(): void
    {
        Config::set('session.idle_timeout', 20);
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)
            ->withSession(['last_activity_at' => time() - (21 * 60)])
            ->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertGuest();
    }

    public function test_heartbeat_route_keeps_an_active_session_alive(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->withSession(['last_activity_at' => time() - 60])
            ->post(route('session.heartbeat'))
            ->assertNoContent();

        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_menu_is_available_to_authenticated_users(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('logout'), false);
    }
}

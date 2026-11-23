<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_pages_are_guest_safe_and_log_mailer_does_not_claim_delivery(): void
    {
        config(['mail.default' => 'log']);
        $this->get(route('password.request'))->assertOk()->assertSee('Email delivery is not configured');
        $this->get(route('password.reset', ['token' => 'sample-token', 'email' => 'admin@rida.gov.zw']))
            ->assertOk()->assertSee('Reset password');
        $this->from(route('password.request'))->post(route('password.email'), ['email' => 'admin@rida.gov.zw'])
            ->assertSessionHasErrors('email')->assertSessionMissing('status');
    }

    public function test_email_recovery_is_private_active_only_and_rate_limited(): void
    {
        config(['mail.default' => 'smtp']);
        Notification::fake();
        $active = User::factory()->create();
        $inactive = User::factory()->create(['is_active' => false]);
        $archived = User::factory()->create();
        $archived->delete();
        $messages = [];
        foreach ([$active->email, $inactive->email, $archived->email, 'unknown@rida.gov.zw'] as $email) {
            $response = $this->post(route('password.email'), compact('email'))->assertRedirect()->assertSessionHasNoErrors();
            $messages[] = $response->getSession()->get('status');
        }
        $this->assertCount(1, array_unique($messages));
        Notification::assertSentTo($active, ResetPassword::class);
        Notification::assertNotSentTo($inactive, ResetPassword::class);
        Notification::assertNotSentTo($archived, ResetPassword::class);
        $this->post(route('password.email'), ['email' => $active->email])->assertRedirect();
        Notification::assertSentToTimes($active, ResetPassword::class, 1);
        $this->post(route('password.email'), ['email' => $active->email])->assertStatus(429);
    }

    public function test_email_reset_requires_valid_unexpired_single_use_token_and_revokes_other_access(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-remember-token']);
        $token = Password::createToken($user);
        $payload = ['email' => $user->email, 'token' => $token, 'password' => 'NewSecurePassword123!', 'password_confirmation' => 'NewSecurePassword123!'];
        $this->postJson(route('password.update'), array_replace($payload, ['token' => 'wrong-token']))
            ->assertSessionHasErrors('email');
        DB::table('password_reset_tokens')->where('email', $user->email)->update(['created_at' => now()->subMinutes(61)]);
        $this->post(route('password.update'), $payload)->assertSessionHasErrors('email');
        $token = Password::createToken($user);
        $this->post(route('password.update'), array_replace($payload, ['token' => $token]))
            ->assertRedirect(route('login'))->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertTrue(Hash::check($payload['password'], $user->password));
        $this->assertSame(1, $user->password_version);
        $this->assertNotSame('old-remember-token', $user->remember_token);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
        $this->assertDatabaseHas('audit_logs', ['record_id' => $user->id, 'action' => 'password_reset']);
        $this->post(route('password.update'), array_replace($payload, ['token' => $token]))->assertSessionHasErrors('email');
        $this->actingAs($user)->withSession(['password_version' => 0])->getJson(route('dashboard'))->assertUnauthorized();
    }

    public function test_inactive_or_archived_accounts_cannot_use_previously_issued_reset_tokens(): void
    {
        foreach (['inactive', 'archived'] as $state) {
            $user = User::factory()->create();
            $token = Password::createToken($user);
            if ($state === 'inactive') {
                $user->update(['is_active' => false]);
            } else {
                $user->delete();
            }
            $this->post(route('password.update'), [
                'email' => $user->email, 'token' => $token, 'password' => 'NewSecurePassword123!',
                'password_confirmation' => 'NewSecurePassword123!',
            ])->assertSessionHasErrors('email');
            $this->assertTrue(Hash::check('password', User::withTrashed()->findOrFail($user->id)->password));
        }
    }

    public function test_admin_temporary_password_is_audited_single_display_expires_and_forces_change(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->viewer()->create();
        $oldToken = Password::createToken($user);
        $this->actingAs($admin);
        $this->post(route('users.temporary-password', $user), ['current_password' => 'incorrect'])
            ->assertSessionHasErrors('current_password');
        $response = $this->post(route('users.temporary-password', $user), ['current_password' => 'password'])
            ->assertOk()->assertHeader('cache-control', 'no-store, private');
        $temporary = $response->viewData('temporaryPassword');
        $user->refresh();
        $this->assertTrue(Hash::check($temporary, $user->password));
        $this->assertTrue($user->must_change_password);
        $this->assertTrue($user->temporary_password_expires_at->isFuture());
        $this->assertFalse(Password::tokenExists($user, $oldToken));
        $audit = AuditLog::where('action', 'temporary_password_issued')->firstOrFail();
        $this->assertSame($admin->id, $audit->user_id);
        $this->assertStringNotContainsString($temporary, $audit->toJson());
        $this->get(route('users.edit', $user))->assertDontSee($temporary);
        $this->post(route('users.temporary-password', $admin), ['current_password' => 'password'])->assertForbidden();

        auth()->logout();
        $this->post(route('login'), ['email' => $user->email, 'password' => $temporary])
            ->assertRedirect(route('account.password.edit'));
        $this->get(route('dashboard'))->assertRedirect(route('account.password.edit'));
        $this->getJson(route('water-points.index'))->assertForbidden();
        $this->get(route('account.password.edit'))->assertOk();
        $newPassword = 'MyNewPrivatePassword123!';
        $this->put(route('account.password.update'), [
            'current_password' => $temporary, 'password' => $newPassword, 'password_confirmation' => $newPassword,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertFalse($user->fresh()->must_change_password);
        $this->assertNull($user->fresh()->temporary_password_expires_at);
        $this->get(route('dashboard'))->assertOk();
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));

        auth()->logout();
        $user->forceFill(['must_change_password' => true, 'temporary_password_expires_at' => now()->subMinute()])->save();
        $this->post(route('login'), ['email' => $user->email, 'password' => $newPassword])
            ->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_all_roles_can_change_own_password_but_only_admin_can_issue_temporary_passwords(): void
    {
        $target = User::factory()->viewer()->create();
        foreach (array_keys(User::ROLES) as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->withSession(['password_version' => 0]);
            $this->put(route('account.password.update'), [
                'current_password' => 'wrong', 'password' => 'NewPrivatePassword123!', 'password_confirmation' => 'NewPrivatePassword123!',
            ])->assertSessionHasErrors('current_password');
            $this->put(route('account.password.update'), [
                'current_password' => 'password', 'password' => 'short', 'password_confirmation' => 'short',
            ])->assertSessionHasErrors('password');
            $this->put(route('account.password.update'), [
                'current_password' => 'password', 'password' => 'NewPrivatePassword123!', 'password_confirmation' => 'NewPrivatePassword123!',
            ])->assertRedirect()->assertSessionHasNoErrors();
            $this->assertTrue(Hash::check('NewPrivatePassword123!', $user->fresh()->password));
            $this->get(route('account.password.edit'))->assertOk();
            if ($role !== 'admin') {
                $this->post(route('users.temporary-password', $target), ['current_password' => 'NewPrivatePassword123!'])->assertForbidden();
            }
        }
    }

    public function test_password_changes_remove_database_sessions_and_general_user_edit_cannot_bypass_recovery(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        config(['session.driver' => 'database']);
        DB::table('sessions')->insert(['id' => 'old-session', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);
        $this->actingAs($admin)->post(route('users.temporary-password', $user), ['current_password' => 'password'])->assertOk();
        $this->assertDatabaseMissing('sessions', ['id' => 'old-session']);
        $this->put(route('users.update', $user), [
            'name' => $user->name, 'email' => $user->email, 'role' => $user->role,
            'is_active' => 1, 'password' => 'UnauditedPassword123!', 'password_confirmation' => 'UnauditedPassword123!',
        ])->assertSessionHasErrors('password');
        $this->assertFalse(Hash::check('UnauditedPassword123!', $user->fresh()->password));
    }
}

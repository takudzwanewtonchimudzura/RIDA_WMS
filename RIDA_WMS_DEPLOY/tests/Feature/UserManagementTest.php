<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_accounts_require_official_emails_and_support_specialized_roles(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $payload = [
            'name' => 'UAT staff', 'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!',
            'role' => 'supervisor', 'is_active' => '1',
        ];
        $this->postJson(route('users.store'), $payload + ['email' => 'staff@example.org'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        foreach (['supervisor', 'finance', 'stores'] as $role) {
            $this->post(route('users.store'), array_replace($payload, ['role' => $role, 'email' => $role . '@rida.gov.zw']))
                ->assertRedirect(route('users.index'))->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', ['role' => $role, 'email' => $role . '@rida.gov.zw', 'is_active' => true]);
        }
    }

    public function test_admin_can_create_and_deactivate_an_operator_account(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->post(route('users.store'), [
            'name' => 'Field Operator',
            'email' => 'operator@rida.gov.zw',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'role' => 'operator',
            'is_active' => '0',
        ])->assertRedirect(route('users.index'));

        $operator = User::where('email', 'operator@rida.gov.zw')->firstOrFail();
        $this->assertSame('operator', $operator->role);
        $this->assertFalse($operator->is_active);

        $this->patch(route('users.toggle-active', $operator))
            ->assertRedirect(route('users.index'));
        $this->assertTrue($operator->fresh()->is_active);
    }

    public function test_last_active_administrator_cannot_be_demoted_or_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'operator',
            'is_active' => '1',
        ])->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
        $this->delete(route('users.destroy', $admin))->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin', 'deleted_at' => null]);
    }
}
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_deactivate_an_operator_account(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->post(route('users.store'), [
            'name' => 'Field Operator',
            'email' => 'operator@example.test',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'role' => 'operator',
            'is_active' => '0',
        ])->assertRedirect(route('users.index'));

        $operator = User::where('email', 'operator@example.test')->firstOrFail();
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
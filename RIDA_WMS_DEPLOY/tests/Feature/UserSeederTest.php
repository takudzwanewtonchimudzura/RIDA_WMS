<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_creates_a_configured_initial_admin_without_hard_coded_credentials(): void
    {
        $repository = Env::getRepository();
        $repository->set('INITIAL_ADMIN_EMAIL', 'bootstrap-admin@rida.gov.zw');
        $repository->set('INITIAL_ADMIN_PASSWORD', 'UniqueBootstrapPass123!');

        try {
            $this->seed(UserSeeder::class);

            $admin = User::where('email', 'bootstrap-admin@rida.gov.zw')->firstOrFail();
            $this->assertSame('admin', $admin->role);
            $this->assertTrue($admin->is_active);
            $this->assertTrue(Hash::check('UniqueBootstrapPass123!', $admin->password));

            $admin->update(['role' => 'operator']);
            $this->seed(UserSeeder::class);
            $this->assertSame('operator', $admin->fresh()->role);
        } finally {
            $repository->clear('INITIAL_ADMIN_EMAIL');
            $repository->clear('INITIAL_ADMIN_PASSWORD');
        }
    }

    public function test_existing_admin_password_only_changes_with_explicit_reset_flag(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'existing-admin@rida.gov.zw']);
        $repository = Env::getRepository();
        $repository->set('INITIAL_ADMIN_EMAIL', 'existing-admin@rida.gov.zw');
        $repository->set('INITIAL_ADMIN_PASSWORD', 'NewBootstrapPassword123!');
        $repository->set('INITIAL_ADMIN_RESET_PASSWORD', 'false');

        try {
            $this->seed(UserSeeder::class);
            $this->assertTrue(Hash::check('password', $admin->fresh()->password));

            $repository->set('INITIAL_ADMIN_RESET_PASSWORD', 'true');
            $this->seed(UserSeeder::class);
            $this->assertTrue(Hash::check('NewBootstrapPassword123!', $admin->fresh()->password));
            $this->assertSame('admin', $admin->fresh()->role);
            $this->assertSame(1, $admin->fresh()->password_version);
            $this->assertDatabaseHas('audit_logs', ['record_type' => User::class, 'record_id' => $admin->id, 'action' => 'bootstrap_password_reset']);
        } finally {
            $repository->clear('INITIAL_ADMIN_EMAIL');
            $repository->clear('INITIAL_ADMIN_PASSWORD');
            $repository->clear('INITIAL_ADMIN_RESET_PASSWORD');
        }
    }
}

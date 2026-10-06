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
        $repository->set('INITIAL_ADMIN_EMAIL', 'bootstrap-admin@example.test');
        $repository->set('INITIAL_ADMIN_PASSWORD', 'UniqueBootstrapPass123!');

        try {
            $this->seed(UserSeeder::class);

            $admin = User::where('email', 'bootstrap-admin@example.test')->firstOrFail();
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
        $admin = User::factory()->admin()->create(['email' => 'existing-admin@example.test']);
        $repository = Env::getRepository();
        $repository->set('INITIAL_ADMIN_EMAIL', 'existing-admin@example.test');
        $repository->set('INITIAL_ADMIN_PASSWORD', 'NewBootstrapPassword123!');
        $repository->set('INITIAL_ADMIN_RESET_PASSWORD', 'false');

        try {
            $this->seed(UserSeeder::class);
            $this->assertTrue(Hash::check('password', $admin->fresh()->password));

            $repository->set('INITIAL_ADMIN_RESET_PASSWORD', 'true');
            $this->seed(UserSeeder::class);
            $this->assertTrue(Hash::check('NewBootstrapPassword123!', $admin->fresh()->password));
            $this->assertSame('admin', $admin->fresh()->role);
        } finally {
            $repository->clear('INITIAL_ADMIN_EMAIL');
            $repository->clear('INITIAL_ADMIN_PASSWORD');
            $repository->clear('INITIAL_ADMIN_RESET_PASSWORD');
        }
    }
}

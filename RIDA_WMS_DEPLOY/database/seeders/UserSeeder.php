<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('INITIAL_ADMIN_EMAIL');
        $password = env('INITIAL_ADMIN_PASSWORD');

        if (!$email || !$password) {
            $this->command?->warn('No bootstrap administrator created. Set INITIAL_ADMIN_EMAIL and INITIAL_ADMIN_PASSWORD for first setup.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
            $this->command?->error('Bootstrap admin requires a valid email and a password of at least 12 characters.');
            return;
        }

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if (!$existingUser->isAdmin()) {
                $this->command?->error('That email belongs to a non-administrator; no account was changed.');
                return;
            }

            if (filter_var(env('INITIAL_ADMIN_RESET_PASSWORD', false), FILTER_VALIDATE_BOOLEAN)) {
                $existingUser->update(['password' => Hash::make($password)]);
                $this->command?->info("Administrator password reset for {$email}.");
                return;
            }

            $this->command?->warn('That administrator already exists; no password was changed. Set INITIAL_ADMIN_RESET_PASSWORD=true for a one-time reset.');
            return;
        }

        User::create([
            'name' => 'System Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'department' => 'Administration',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->command?->info("Bootstrap administrator created for {$email}.");
    }
}
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $roles = ['admin', 'operator', 'viewer'];
        $departments = ['Field Operations', 'Geology', 'Quality Control', 'IT', 'Management', 'Research'];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement($roles),
            'phone' => fake()->phoneNumber(),
            'department' => fake()->randomElement($departments),
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'last_login_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'department' => 'IT Department',
        ]);
    }

    public function operator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'operator',
            'department' => fake()->randomElement(['Field Operations', 'Geology Department']),
        ]);
    }

    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'viewer',
            'department' => fake()->randomElement(['Quality Control', 'Management', 'Research']),
        ]);
    }
}
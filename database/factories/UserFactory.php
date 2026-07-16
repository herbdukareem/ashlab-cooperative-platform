<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Password123!'),
            'status' => UserStatus::Active,
            'is_platform_admin' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function platformAdmin(): static
    {
        return $this->state(fn () => ['cooperative_id' => null, 'is_platform_admin' => true]);
    }
}


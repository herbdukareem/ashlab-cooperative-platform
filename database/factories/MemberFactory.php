<?php

namespace Database\Factories;

use App\Enums\KycStatus;
use App\Enums\MemberStatus;
use App\Models\Cooperative;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cooperative_id' => Cooperative::factory(),
            'membership_number' => fake()->unique()->bothify('MBR-2026-######'),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years'),
            'phone' => fake()->unique()->numerify('080########'),
            'email' => fake()->unique()->safeEmail(),
            'residential_address' => fake()->address(),
            'date_joined' => today(),
            'status' => MemberStatus::Pending,
            'kyc_status' => KycStatus::NotStarted,
        ];
    }
}


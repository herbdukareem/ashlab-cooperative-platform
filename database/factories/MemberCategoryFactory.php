<?php

namespace Database\Factories;

use App\Models\Cooperative;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cooperative_id' => Cooperative::factory(),
            'name' => 'Regular Member',
            'code' => fake()->unique()->bothify('CAT-###'),
            'description' => fake()->sentence(),
            'registration_fee_minor' => 500000,
            'minimum_contribution_minor' => 1000000,
            'requires_guarantor' => false,
            'required_guarantors' => 0,
            'requires_kyc' => true,
            'is_active' => true,
        ];
    }
}


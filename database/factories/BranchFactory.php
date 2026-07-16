<?php

namespace Database\Factories;

use App\Models\Cooperative;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cooperative_id' => Cooperative::factory(),
            'name' => fake()->city().' Branch',
            'code' => fake()->unique()->bothify('BR-###'),
            'type' => 'branch',
            'status' => 'active',
        ];
    }
}


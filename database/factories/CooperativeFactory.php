<?php

namespace Database\Factories;

use App\Enums\CooperativeStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CooperativeFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company().' Cooperative Society';
        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'registration_number' => 'CS-'.fake()->unique()->numerify('######'),
            'registration_date' => fake()->dateTimeBetween('-20 years', '-1 year'),
            'type' => 'staff',
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->unique()->numerify('080########'),
            'address' => fake()->address(),
            'state' => 'Niger',
            'local_government_area' => 'Chanchaga',
            'currency' => 'NGN',
            'financial_year_start_month' => 1,
            'status' => CooperativeStatus::Active,
        ];
    }
}


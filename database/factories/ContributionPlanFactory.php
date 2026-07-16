<?php
namespace Database\Factories;
use App\Enums\ContributionFrequency;
use App\Models\Cooperative;
use Illuminate\Database\Eloquent\Factories\Factory;
class ContributionPlanFactory extends Factory
{
    public function definition(): array { return ['cooperative_id' => Cooperative::factory(), 'name' => fake()->words(3, true), 'code' => strtoupper(fake()->unique()->bothify('PLAN-####')), 'frequency' => ContributionFrequency::Monthly, 'minimum_amount_minor' => 100000, 'fixed_amount_minor' => 100000, 'is_fixed_amount' => true, 'is_mandatory' => false, 'grace_period_days' => 5, 'is_active' => true]; }
}

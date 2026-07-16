<?php
namespace Database\Factories;
use App\Models\Cooperative;
use Illuminate\Database\Eloquent\Factories\Factory;
class SavingsProductFactory extends Factory
{
    public function definition(): array { return ['cooperative_id' => Cooperative::factory(), 'name' => fake()->words(2, true), 'code' => strtoupper(fake()->unique()->bothify('SAV-####')), 'minimum_opening_balance_minor' => 0, 'minimum_balance_minor' => 10000, 'minimum_withdrawal_minor' => 10000, 'lock_in_days' => 0, 'interest_rate_basis_points' => 0, 'allow_multiple_accounts' => false, 'allows_withdrawal' => true, 'is_active' => true]; }
}

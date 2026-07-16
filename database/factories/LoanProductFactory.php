<?php
namespace Database\Factories;
use App\Enums\InterestMethod; use App\Enums\RepaymentFrequency; use App\Models\Cooperative; use Illuminate\Database\Eloquent\Factories\Factory;
class LoanProductFactory extends Factory
{
    public function definition():array{return ['cooperative_id'=>Cooperative::factory(),'name'=>fake()->words(3,true),'code'=>strtoupper(fake()->unique()->bothify('LOAN-####')),'minimum_principal_minor'=>100000,'maximum_principal_minor'=>10000000,'minimum_tenure'=>1,'maximum_tenure'=>24,'interest_method'=>InterestMethod::ReducingBalance,'annual_interest_rate_basis_points'=>1200,'repayment_frequency'=>RepaymentFrequency::Monthly,'grace_period_days'=>0,'moratorium_periods'=>0,'requires_guarantors'=>true,'minimum_guarantors'=>1,'maximum_guarantors'=>2,'minimum_membership_months'=>3,'guarantor_maximum_exposure_basis_points'=>10000,'guarantor_maximum_active_loans'=>3,'allows_early_settlement'=>true,'is_active'=>true];}
}

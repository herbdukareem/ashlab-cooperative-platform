<?php
namespace Database\Factories;
use App\Enums\ChargeCalculationType; use App\Models\Cooperative; use Illuminate\Database\Eloquent\Factories\Factory;
class ChargeFactory extends Factory
{
    public function definition():array{return ['cooperative_id'=>Cooperative::factory(),'name'=>fake()->words(2,true),'code'=>strtoupper(fake()->unique()->bothify('FEE-####')),'calculation_type'=>ChargeCalculationType::Percentage,'rate_basis_points'=>100,'calculation_basis'=>'principal','application_timing'=>'disbursement','treatment'=>'deduct_from_disbursement','is_refundable'=>false,'is_active'=>true];}
}

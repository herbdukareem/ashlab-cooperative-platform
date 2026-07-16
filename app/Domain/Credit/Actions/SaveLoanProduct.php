<?php
namespace App\Domain\Credit\Actions;
use App\Models\LoanProduct; use Illuminate\Support\Arr; use Illuminate\Support\Facades\DB;
class SaveLoanProduct
{
    public function execute(array $data, ?LoanProduct $product=null): LoanProduct
    {
        return DB::transaction(function()use($data,$product):LoanProduct{ $product??=new LoanProduct(); $product->fill(Arr::except($data,['charges','eligibility_rules']))->save();
            if(array_key_exists('charges',$data)){ $sync=[]; foreach($data['charges'] as $item){$sync[$item['charge_id']]=['sequence'=>$item['sequence'],'is_mandatory'=>$item['is_mandatory'],'overrides'=>isset($item['overrides'])?json_encode($item['overrides']):null];} $product->charges()->sync($sync); }
            if(array_key_exists('eligibility_rules',$data)){ $product->eligibilityRules()->delete(); foreach($data['eligibility_rules'] as $rule)$product->eligibilityRules()->create($rule); }
            return $product->refresh()->load(['charges','eligibilityRules','approvalWorkflow.steps']); });
    }
}

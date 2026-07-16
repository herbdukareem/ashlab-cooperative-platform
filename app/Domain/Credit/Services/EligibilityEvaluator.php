<?php
namespace App\Domain\Credit\Services;
use App\Models\LoanProduct;
class EligibilityEvaluator
{
    public function evaluate(LoanProduct $product, array $facts): array
    {
        $product->loadMissing('eligibilityRules'); $results=[];
        foreach($product->eligibilityRules->where('is_active',true) as $rule){ $actual=data_get($facts,$rule->field); $expected=$rule->comparison_value['value'] ?? $rule->comparison_value; $passed=match($rule->operator){'eq'=>$actual==$expected,'neq'=>$actual!=$expected,'gt'=>$actual>$expected,'gte'=>$actual>=$expected,'lt'=>$actual<$expected,'lte'=>$actual<=$expected,'in'=>in_array($actual,(array)$expected,true),'not_in'=>!in_array($actual,(array)$expected,true),'between'=>$actual>=($expected[0]??null)&&$actual<=($expected[1]??null),default=>false}; $results[]=['rule_id'=>$rule->id,'name'=>$rule->name,'passed'=>$passed,'is_hard_rule'=>$rule->is_hard_rule,'message'=>$passed?null:$rule->failure_message]; }
        return ['eligible'=>collect($results)->doesntContain(fn($result)=>!$result['passed']&&$result['is_hard_rule']),'results'=>$results];
    }
}

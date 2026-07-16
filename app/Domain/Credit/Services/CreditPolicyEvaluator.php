<?php
namespace App\Domain\Credit\Services;
use App\Models\LoanProduct;
class CreditPolicyEvaluator
{
    public function evaluate(LoanProduct $product,int $requestedPrincipalMinor,array $facts):array
    {
        $limits=[$product->maximum_principal_minor];
        if($product->contribution_limit_multiplier_basis_points!==null)$limits[]=intdiv((int)($facts['total_contributions_minor']??0)*$product->contribution_limit_multiplier_basis_points,10000);
        if($product->savings_limit_multiplier_basis_points!==null)$limits[]=intdiv((int)($facts['savings_balance_minor']??0)*$product->savings_limit_multiplier_basis_points,10000);
        $maximum=max(0,min($limits)); $membershipPassed=(int)($facts['membership_months']??0)>=$product->minimum_membership_months;
        $dti=null;$affordabilityPassed=true;if($product->maximum_debt_to_income_basis_points!==null){$income=max(1,(int)($facts['monthly_income_minor']??0));$debt=(int)($facts['existing_monthly_debt_minor']??0)+(int)($facts['proposed_monthly_payment_minor']??0);$dti=intdiv($debt*10000,$income);$affordabilityPassed=$dti<=$product->maximum_debt_to_income_basis_points;}
        return ['eligible'=>$membershipPassed&&$affordabilityPassed&&$requestedPrincipalMinor>=$product->minimum_principal_minor&&$requestedPrincipalMinor<=$maximum,'maximum_eligible_principal_minor'=>$maximum,'membership_passed'=>$membershipPassed,'affordability_passed'=>$affordabilityPassed,'debt_to_income_basis_points'=>$dti];
    }
    public function guarantorCapacity(LoanProduct $product,array $facts):array
    {
        $base=(int)($facts['savings_balance_minor']??0)+(int)($facts['total_contributions_minor']??0);$limit=intdiv($base*$product->guarantor_maximum_exposure_basis_points,10000);$available=max(0,$limit-(int)($facts['current_guaranteed_exposure_minor']??0));$active=(int)($facts['active_guaranteed_loans']??0);
        return ['eligible'=>$active<$product->guarantor_maximum_active_loans&&$available>0,'exposure_limit_minor'=>$limit,'available_exposure_minor'=>$available,'active_guaranteed_loans'=>$active,'maximum_active_loans'=>$product->guarantor_maximum_active_loans];
    }
}

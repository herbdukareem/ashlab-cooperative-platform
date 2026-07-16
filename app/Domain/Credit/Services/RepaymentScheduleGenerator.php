<?php
namespace App\Domain\Credit\Services;
use App\Enums\InterestMethod; use App\Enums\RepaymentFrequency; use App\Models\LoanProduct; use Carbon\CarbonImmutable; use Illuminate\Validation\ValidationException;
class RepaymentScheduleGenerator
{
    public function generate(LoanProduct $product, int $principalMinor, int $tenure, CarbonImmutable $startDate): array
    {
        if ($principalMinor<$product->minimum_principal_minor || $principalMinor>$product->maximum_principal_minor) throw ValidationException::withMessages(['principal_minor'=>['Principal is outside the product limits.']]);
        if ($tenure<$product->minimum_tenure || $tenure>$product->maximum_tenure) throw ValidationException::withMessages(['tenure'=>['Tenure is outside the product limits.']]);
        $isBullet=$product->repayment_frequency===RepaymentFrequency::Bullet; $periods=$isBullet ? 1 : $tenure; $interestPeriods=$isBullet ? $tenure : $periods; $ppy=$this->periodsPerYear($product->repayment_frequency); $balance=$principalMinor; $rows=[]; $principalBase=intdiv($principalMinor,$periods); $flatTotal=$product->interest_method===InterestMethod::Flat ? intdiv($principalMinor*$product->annual_interest_rate_basis_points*$interestPeriods,$ppy*10000) : 0; $flatBase=intdiv($flatTotal,$periods); $interestTotal=0;
        for($i=1;$i<=$periods;$i++) { $principal=$i===$periods ? $balance : $principalBase; $interest=match($product->interest_method){ InterestMethod::None=>0, InterestMethod::Flat=>$i===$periods ? $flatTotal-$interestTotal : $flatBase, InterestMethod::ReducingBalance=>$isBullet ? intdiv($balance*$product->annual_interest_rate_basis_points*$interestPeriods,$ppy*10000) : intdiv($balance*$product->annual_interest_rate_basis_points,$ppy*10000) }; $balance-=$principal; $interestTotal+=$interest; $duePeriod=$isBullet ? $tenure+$product->moratorium_periods : $i+$product->moratorium_periods; $rows[]=['installment_number'=>$i,'due_date'=>$this->dueDate($startDate,$product->repayment_frequency,$duePeriod)->toDateString(),'principal_minor'=>$principal,'interest_minor'=>$interest,'total_due_minor'=>$principal+$interest,'balance_after_minor'=>$balance]; }
        return ['principal_minor'=>$principalMinor,'interest_minor'=>$interestTotal,'repayable_minor'=>$principalMinor+$interestTotal,'installments'=>$rows];
    }
    private function periodsPerYear(RepaymentFrequency $frequency): int { return match($frequency){RepaymentFrequency::Daily=>365,RepaymentFrequency::Weekly=>52,RepaymentFrequency::Biweekly=>26,RepaymentFrequency::Monthly=>12,RepaymentFrequency::Quarterly=>4,RepaymentFrequency::Bullet=>12}; }
    private function dueDate(CarbonImmutable $start, RepaymentFrequency $frequency, int $period): CarbonImmutable { return match($frequency){RepaymentFrequency::Daily=>$start->addDays($period),RepaymentFrequency::Weekly=>$start->addWeeks($period),RepaymentFrequency::Biweekly=>$start->addWeeks($period*2),RepaymentFrequency::Monthly=>$start->addMonthsNoOverflow($period),RepaymentFrequency::Quarterly=>$start->addMonthsNoOverflow($period*3),RepaymentFrequency::Bullet=>$start->addMonthsNoOverflow($period)}; }
}

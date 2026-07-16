<?php
namespace App\Domain\Credit\Services;
use App\Enums\ChargeCalculationType; use App\Models\Charge; use App\Models\LoanProduct;
class ChargeCalculator
{
    public function calculate(LoanProduct $product, int $principalMinor, ?string $memberCategoryId = null): array
    {
        $product->loadMissing('charges');
        return $product->charges->filter(fn (Charge $charge) => $charge->is_active && ! in_array($memberCategoryId, $charge->exempt_member_category_ids ?? [], true))->map(function (Charge $charge) use ($principalMinor): array {
            $rawOverrides=$charge->pivot->overrides; $overrides=is_array($rawOverrides)?$rawOverrides:(json_decode((string)$rawOverrides,true)?:[]); $amount=$charge->calculation_type===ChargeCalculationType::Fixed ? (int)($overrides['fixed_amount_minor'] ?? $charge->fixed_amount_minor) : intdiv($principalMinor*(int)($overrides['rate_basis_points'] ?? $charge->rate_basis_points),10000);
            $minimum=$overrides['minimum_amount_minor'] ?? $charge->minimum_amount_minor; $maximum=$overrides['maximum_amount_minor'] ?? $charge->maximum_amount_minor;
            if ($minimum!==null) $amount=max($amount,(int)$minimum); if ($maximum!==null) $amount=min($amount,(int)$maximum);
            return ['charge_id'=>$charge->id,'code'=>$charge->code,'name'=>$charge->name,'amount_minor'=>$amount,'application_timing'=>$charge->application_timing,'treatment'=>$charge->treatment,'is_mandatory'=>(bool)$charge->pivot->is_mandatory,'is_refundable'=>$charge->is_refundable];
        })->values()->all();
    }
}

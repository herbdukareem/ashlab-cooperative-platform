<?php

namespace App\Domain\Contributions\Services;

use App\Enums\ContributionFrequency;
use App\Models\ContributionPlan;
use Carbon\CarbonImmutable;

class ContributionSchedule
{
    public function next(ContributionPlan $plan, CarbonImmutable $current): ?CarbonImmutable
    {
        return match ($plan->frequency) {
            ContributionFrequency::Daily => $current->addDay(),
            ContributionFrequency::Weekly => $current->addWeek(),
            ContributionFrequency::Biweekly => $current->addWeeks(2),
            ContributionFrequency::Monthly => $current->addMonthNoOverflow(),
            ContributionFrequency::Quarterly => $current->addMonthsNoOverflow(3),
            ContributionFrequency::SemiAnnually => $current->addMonthsNoOverflow(6),
            ContributionFrequency::Annually => $current->addYearNoOverflow(),
            ContributionFrequency::OneTime => null,
            ContributionFrequency::Custom => $this->nextCustom($plan, $current),
        };
    }

    private function nextCustom(ContributionPlan $plan, CarbonImmutable $current): ?CarbonImmutable
    {
        $dates = collect($plan->schedule_configuration['dates'] ?? [])->map(fn ($date) => CarbonImmutable::parse($date))->sort();
        return $dates->first(fn (CarbonImmutable $date) => $date->greaterThan($current));
    }
}

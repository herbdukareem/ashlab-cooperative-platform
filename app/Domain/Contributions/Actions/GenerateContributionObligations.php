<?php

namespace App\Domain\Contributions\Actions;

use App\Domain\Contributions\Services\ContributionSchedule;
use App\Enums\ContributionObligationStatus;
use App\Models\ContributionObligation;
use App\Models\MemberContributionPlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class GenerateContributionObligations
{
    public function __construct(private readonly ContributionSchedule $schedule) {}

    public function execute(MemberContributionPlan $enrollment, CarbonImmutable $through): int
    {
        return DB::transaction(function () use ($enrollment, $through): int {
            $enrollment = MemberContributionPlan::query()->with('plan')->lockForUpdate()->findOrFail($enrollment->id);
            if ($enrollment->status !== 'active' || ! $enrollment->next_due_date) return 0;

            $created = 0;
            $due = CarbonImmutable::parse($enrollment->next_due_date);
            $planEnd = $enrollment->end_date ?? $enrollment->plan->end_date;

            while ($due->lessThanOrEqualTo($through) && (! $planEnd || $due->lessThanOrEqualTo($planEnd))) {
                $status = $due->isFuture() ? ContributionObligationStatus::Upcoming : ContributionObligationStatus::Due;
                $obligation = ContributionObligation::query()->firstOrCreate(
                    ['member_contribution_plan_id' => $enrollment->id, 'due_date' => $due->toDateString()],
                    [
                        'member_id' => $enrollment->member_id,
                        'contribution_plan_id' => $enrollment->contribution_plan_id,
                        'period_start' => $due->toDateString(),
                        'period_end' => $due->toDateString(),
                        'amount_due_minor' => $enrollment->contribution_amount_minor,
                        'status' => $status,
                    ],
                );
                if ($obligation->wasRecentlyCreated) $created++;
                $next = $this->schedule->next($enrollment->plan, $due);
                if (! $next || $next->lessThanOrEqualTo($due)) { $due = null; break; }
                $due = $next;
            }

            $enrollment->update(['next_due_date' => $due?->toDateString()]);
            return $created;
        });
    }
}

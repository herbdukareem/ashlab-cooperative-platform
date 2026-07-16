<?php

namespace App\Domain\Contributions\Actions;

use App\Enums\ContributionObligationStatus;
use App\Models\ContributionObligation;

class RefreshObligationStatuses
{
    public function execute(): int
    {
        $updated = 0;
        ContributionObligation::query()->with('plan')
            ->whereIn('status', [ContributionObligationStatus::Upcoming->value, ContributionObligationStatus::Due->value, ContributionObligationStatus::PartiallyPaid->value])
            ->chunkById(200, function ($obligations) use (&$updated): void {
                foreach ($obligations as $obligation) {
                    $overdueDate = $obligation->due_date->addDays($obligation->plan->grace_period_days);
                    $status = $overdueDate->isPast() ? ContributionObligationStatus::Overdue
                        : ($obligation->amount_paid_minor > 0 ? ContributionObligationStatus::PartiallyPaid
                            : ($obligation->due_date->isFuture() ? ContributionObligationStatus::Upcoming : ContributionObligationStatus::Due));
                    if ($obligation->status !== $status) { $obligation->update(['status' => $status]); $updated++; }
                }
            });
        return $updated;
    }
}

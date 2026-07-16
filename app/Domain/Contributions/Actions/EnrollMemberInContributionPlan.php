<?php

namespace App\Domain\Contributions\Actions;

use App\Models\ContributionPlan;
use App\Models\Member;
use App\Models\MemberContributionPlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollMemberInContributionPlan
{
    public function __construct(private readonly GenerateContributionObligations $generate) {}

    public function execute(Member $member, ContributionPlan $plan, array $data): MemberContributionPlan
    {
        abort_unless($member->cooperative_id === $plan->cooperative_id, 404);
        if (! $plan->is_active) throw ValidationException::withMessages(['contribution_plan_id' => ['This contribution plan is inactive.']]);
        if ($plan->eligible_member_category_ids && ! in_array($member->member_category_id, $plan->eligible_member_category_ids, true)) {
            throw ValidationException::withMessages(['contribution_plan_id' => ['The member category is not eligible for this plan.']]);
        }

        $amount = $plan->is_fixed_amount ? $plan->fixed_amount_minor : (int) $data['contribution_amount_minor'];
        if ($amount < $plan->minimum_amount_minor || ($plan->maximum_amount_minor !== null && $amount > $plan->maximum_amount_minor)) {
            throw ValidationException::withMessages(['contribution_amount_minor' => ['The contribution amount is outside the plan limits.']]);
        }

        $enrollment = DB::transaction(fn () => MemberContributionPlan::query()->create([
            'member_id' => $member->id,
            'contribution_plan_id' => $plan->id,
            'contribution_amount_minor' => $amount,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'next_due_date' => $data['start_date'],
            'status' => 'active',
        ]));

        $this->generate->execute($enrollment, CarbonImmutable::parse($data['start_date']));
        return $enrollment->refresh()->load('plan');
    }
}

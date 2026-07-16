<?php

namespace App\Domain\Members\Actions;

use App\Enums\MemberStatus;
use App\Enums\ConsentStatus;
use App\Enums\KycStatus;
use App\Models\Member;
use App\Models\MemberStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeMemberStatus
{
    private const ALLOWED = [
        'pending' => ['active', 'rejected'],
        'active' => ['suspended', 'inactive', 'retired', 'resigned', 'deceased', 'terminated', 'blacklisted'],
        'suspended' => ['active', 'terminated', 'blacklisted'],
        'inactive' => ['active', 'resigned', 'deceased', 'terminated'],
        'rejected' => ['pending'],
        'blacklisted' => ['suspended'],
        'retired' => [], 'resigned' => [], 'deceased' => [], 'terminated' => [],
    ];

    public function execute(Member $member, MemberStatus $to, ?string $reason): Member
    {
        $from = $member->status;

        if (! in_array($to->value, self::ALLOWED[$from->value] ?? [], true)) {
            throw ValidationException::withMessages(['status' => ["A member cannot move from {$from->value} to {$to->value}."]]);
        }

        if ($from === MemberStatus::Pending && $to === MemberStatus::Active) {
            $member->loadMissing('category');
            if ($member->category?->requires_kyc && $member->kyc_status !== KycStatus::Verified) {
                throw ValidationException::withMessages(['status' => ['KYC must be verified before this member can be approved.']]);
            }
            if ($member->category?->requires_guarantor) {
                $accepted = $member->guarantors()->where('is_active', true)->where('consent_status', ConsentStatus::Accepted->value)->count();
                if ($accepted < $member->category->required_guarantors) {
                    throw ValidationException::withMessages(['status' => ['The required number of guarantors have not accepted.']]);
                }
            }
        }

        return DB::transaction(function () use ($member, $from, $to, $reason): Member {
            $attributes = ['status' => $to, 'status_reason' => $reason];

            if ($to === MemberStatus::Active && $from === MemberStatus::Pending) {
                $attributes['approved_by'] = Auth::id();
                $attributes['approved_at'] = now();
            }

            $member->update($attributes);

            MemberStatusHistory::query()->create([
                'member_id' => $member->id,
                'actor_id' => Auth::id(),
                'from_status' => $from->value,
                'to_status' => $to->value,
                'reason' => $reason,
            ]);

            return $member->refresh();
        });
    }
}

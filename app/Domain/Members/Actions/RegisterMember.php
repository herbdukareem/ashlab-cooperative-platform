<?php

namespace App\Domain\Members\Actions;

use App\Enums\KycStatus;
use App\Enums\MemberStatus;
use App\Models\Member;
use App\Models\MemberStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterMember
{
    public function __construct(private readonly GenerateMembershipNumber $numbers) {}

    public function execute(array $data): Member
    {
        return DB::transaction(function () use ($data): Member {
            $member = Member::query()->create([
                ...$data,
                'membership_number' => $this->numbers->execute(),
                'date_joined' => $data['date_joined'] ?? today(),
                'status' => MemberStatus::Pending,
                'kyc_status' => KycStatus::NotStarted,
            ]);

            MemberStatusHistory::query()->create([
                'member_id' => $member->id,
                'actor_id' => Auth::id(),
                'from_status' => null,
                'to_status' => MemberStatus::Pending->value,
                'reason' => 'Member registration created.',
            ]);

            return $member;
        });
    }
}


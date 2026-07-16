<?php

namespace App\Domain\Members\Actions;

use App\Enums\KycStatus;
use App\Enums\VerificationStatus;
use App\Models\Member;
use App\Models\MemberIdentification;
use App\Support\Security\ProtectedIdentifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreMemberIdentification
{
    public function __construct(private readonly ProtectedIdentifier $protector) {}

    public function execute(Member $member, array $data): MemberIdentification
    {
        $protected = $this->protector->protect($data['identifier']);

        if (mb_strlen($this->protector->normalize($data['identifier'])) < 4) {
            throw ValidationException::withMessages(['identifier' => ['The identification number must contain at least four letters or digits.']]);
        }

        if (MemberIdentification::query()->where('type', $data['type'])->where('identifier_hash', $protected['hash'])->exists()) {
            throw ValidationException::withMessages(['identifier' => ['This identification number already belongs to a member in this cooperative.']]);
        }

        return DB::transaction(function () use ($member, $data, $protected): MemberIdentification {
            $identification = MemberIdentification::query()->create([
                'member_id' => $member->id,
                'type' => $data['type'],
                'identifier_encrypted' => $protected['encrypted'],
                'identifier_hash' => $protected['hash'],
                'identifier_last_four' => $protected['last_four'],
                'country' => $data['country'] ?? 'NG',
                'verification_status' => VerificationStatus::Pending,
                'metadata' => $data['metadata'] ?? null,
            ]);

            $member->update(['kyc_status' => KycStatus::Pending]);

            return $identification;
        });
    }
}

<?php

namespace App\Domain\Members\Actions;

use App\Enums\KycStatus;
use App\Enums\VerificationStatus;
use App\Integrations\Contracts\IdentityGateway;
use App\Models\MemberIdentification;
use App\Support\Security\ProtectedIdentifier;
use Illuminate\Support\Facades\DB;

class VerifyMemberIdentification
{
    public function __construct(
        private IdentityGateway $gateway,
        private ProtectedIdentifier $protector,
    ) {}

    public function execute(MemberIdentification $identification): MemberIdentification
    {
        $result = $this->gateway->verify(
            $identification->type,
            $this->protector->reveal($identification->identifier_encrypted),
            ['member_id' => $identification->member_id],
        );

        return DB::transaction(function () use ($identification, $result): MemberIdentification {
            $status = $result['verified'] ? VerificationStatus::Verified : VerificationStatus::Rejected;
            $identification->update([
                'verification_status' => $status,
                'verified_at' => $result['verified'] ? now() : null,
                'rejection_reason' => $result['reason'],
                'metadata' => [
                    ...($identification->metadata ?? []),
                    'provider_reference' => $result['reference'],
                    'provider_attributes' => $result['attributes'],
                ],
            ]);

            $member = $identification->member;
            $hasRejected = $member->identifications()->where('verification_status', VerificationStatus::Rejected->value)->exists();
            $hasPending = $member->identifications()->where('verification_status', '!=', VerificationStatus::Verified->value)->exists();
            $member->update(['kyc_status' => $hasRejected ? KycStatus::Rejected : ($hasPending ? KycStatus::Pending : KycStatus::Verified)]);

            return $identification->refresh();
        });
    }
}

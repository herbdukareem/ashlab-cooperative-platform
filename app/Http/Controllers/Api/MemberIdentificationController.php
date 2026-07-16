<?php

namespace App\Http\Controllers\Api;

use App\Domain\Members\Actions\StoreMemberIdentification;
use App\Enums\KycStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIdentificationRequest;
use App\Http\Requests\VerifyRecordRequest;
use App\Http\Resources\MemberIdentificationResource;
use App\Models\Member;
use App\Models\MemberIdentification;
use Illuminate\Support\Facades\Auth;

class MemberIdentificationController extends Controller
{
    public function store(StoreIdentificationRequest $request, Member $member, StoreMemberIdentification $store): MemberIdentificationResource
    {
        return new MemberIdentificationResource($store->execute($member, $request->validated()));
    }

    public function verify(VerifyRecordRequest $request, Member $member, MemberIdentification $identification): MemberIdentificationResource
    {
        $this->owns($member, $identification); $data = $request->validated(); $status = VerificationStatus::from($data['status']);
        $identification->update(['verification_status' => $status, 'verified_by' => Auth::id(), 'verified_at' => $status === VerificationStatus::Verified ? now() : null, 'rejection_reason' => $data['reason'] ?? null]);
        $this->refreshKycStatus($member);
        return new MemberIdentificationResource($identification->refresh());
    }

    public function destroy(Member $member, MemberIdentification $identification): mixed
    {
        $this->owns($member, $identification); abort_if($identification->verification_status === VerificationStatus::Verified, 422, 'A verified identification cannot be deleted.');
        $identification->delete(); $this->refreshKycStatus($member); return response()->noContent();
    }

    private function owns(Member $member, MemberIdentification $record): void { abort_unless($record->member_id === $member->id, 404); }

    private function refreshKycStatus(Member $member): void
    {
        $identifications = $member->identifications();
        $status = ! $identifications->exists() ? KycStatus::NotStarted
            : ($member->identifications()->where('verification_status', VerificationStatus::Rejected->value)->exists() ? KycStatus::Rejected
                : ($member->identifications()->where('verification_status', '!=', VerificationStatus::Verified->value)->exists() ? KycStatus::Pending : KycStatus::Verified));
        $member->update(['kyc_status' => $status]);
    }
}

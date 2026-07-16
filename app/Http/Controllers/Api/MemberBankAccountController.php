<?php

namespace App\Http\Controllers\Api;

use App\Domain\Members\Actions\StoreMemberBankAccount;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\VerifyRecordRequest;
use App\Http\Resources\MemberBankAccountResource;
use App\Models\Member;
use App\Models\MemberBankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberBankAccountController extends Controller
{
    public function store(StoreBankAccountRequest $request, Member $member, StoreMemberBankAccount $store): MemberBankAccountResource
    {
        return new MemberBankAccountResource($store->execute($member, $request->validated()));
    }

    public function verify(VerifyRecordRequest $request, Member $member, MemberBankAccount $bankAccount): MemberBankAccountResource
    {
        $this->owns($member, $bankAccount); $data = $request->validated(); $status = VerificationStatus::from($data['status']);
        $bankAccount->update(['verification_status' => $status, 'provider_reference' => $data['provider_reference'] ?? null, 'verified_by' => Auth::id(), 'verified_at' => $status === VerificationStatus::Verified ? now() : null]);
        return new MemberBankAccountResource($bankAccount->refresh());
    }

    public function makePrimary(Member $member, MemberBankAccount $bankAccount): MemberBankAccountResource
    {
        $this->owns($member, $bankAccount); abort_unless($bankAccount->verification_status === VerificationStatus::Verified, 422, 'Only a verified bank account can be selected as primary.');
        DB::transaction(function () use ($member, $bankAccount): void { $member->bankAccounts()->update(['is_primary' => false]); $bankAccount->update(['is_primary' => true]); });
        return new MemberBankAccountResource($bankAccount->refresh());
    }

    public function destroy(Member $member, MemberBankAccount $bankAccount): mixed
    {
        $this->owns($member, $bankAccount); abort_if($bankAccount->is_primary, 422, 'Select another primary account before deleting this account.');
        $bankAccount->delete(); return response()->noContent();
    }

    private function owns(Member $member, MemberBankAccount $record): void { abort_unless($record->member_id === $member->id, 404); }
}


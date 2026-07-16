<?php

namespace App\Domain\Members\Actions;

use App\Enums\VerificationStatus;
use App\Models\Member;
use App\Models\MemberBankAccount;
use App\Support\Security\ProtectedIdentifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreMemberBankAccount
{
    public function __construct(private readonly ProtectedIdentifier $protector) {}

    public function execute(Member $member, array $data): MemberBankAccount
    {
        $protected = $this->protector->protect($data['account_number']);

        if (MemberBankAccount::query()->where('bank_code', $data['bank_code'])->where('account_number_hash', $protected['hash'])->exists()) {
            throw ValidationException::withMessages(['account_number' => ['This bank account is already registered in this cooperative.']]);
        }

        return DB::transaction(function () use ($member, $data, $protected): MemberBankAccount {
            $makePrimary = (bool) ($data['is_primary'] ?? ! $member->bankAccounts()->exists());
            if ($makePrimary) $member->bankAccounts()->update(['is_primary' => false]);

            return MemberBankAccount::query()->create([
                'member_id' => $member->id,
                'bank_code' => $data['bank_code'],
                'bank_name' => $data['bank_name'],
                'account_number_encrypted' => $protected['encrypted'],
                'account_number_hash' => $protected['hash'],
                'account_number_last_four' => $protected['last_four'],
                'account_name' => $data['account_name'],
                'is_primary' => $makePrimary,
                'verification_status' => VerificationStatus::Pending,
            ]);
        });
    }
}

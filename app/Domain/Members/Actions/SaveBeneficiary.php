<?php

namespace App\Domain\Members\Actions;

use App\Models\Member;
use App\Models\MemberBeneficiary;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveBeneficiary
{
    public function execute(Member $member, array $data, ?MemberBeneficiary $beneficiary = null): MemberBeneficiary
    {
        $allocated = (float) $member->beneficiaries()->when($beneficiary, fn ($q) => $q->whereKeyNot($beneficiary->id))->sum('entitlement_percentage');
        if ($allocated + (float) $data['entitlement_percentage'] > 100.0) {
            throw ValidationException::withMessages(['entitlement_percentage' => ['The total beneficiary entitlement cannot exceed 100%.']]);
        }

        return DB::transaction(function () use ($member, $data, $beneficiary): MemberBeneficiary {
            if (! empty($data['identification'])) {
                $data['identification_encrypted'] = Crypt::encryptString($data['identification']);
            }
            unset($data['identification']);
            $beneficiary ??= new MemberBeneficiary(['member_id' => $member->id]);
            $beneficiary->fill($data)->save();
            return $beneficiary->refresh();
        });
    }
}


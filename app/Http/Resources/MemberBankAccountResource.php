<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberBankAccountResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'bank_code' => $this->bank_code, 'bank_name' => $this->bank_name, 'masked_account_number' => '******'.$this->account_number_last_four, 'account_name' => $this->account_name, 'is_primary' => $this->is_primary, 'verification_status' => $this->verification_status, 'provider_reference' => $this->provider_reference, 'verified_at' => $this->verified_at]; }
}


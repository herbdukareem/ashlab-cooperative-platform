<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberGuarantorResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'guarantor_member_id' => $this->guarantor_member_id, 'external_name' => $this->external_name, 'relationship' => $this->relationship, 'phone' => $this->phone, 'email' => $this->email, 'address' => $this->address, 'employer' => $this->employer, 'guarantee_limit_minor' => $this->guarantee_limit_minor, 'guaranteed_amount_minor' => $this->guaranteed_amount_minor, 'consent_status' => $this->consent_status, 'consented_at' => $this->consented_at, 'is_active' => $this->is_active]; }
}


<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberBeneficiaryResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'full_name' => $this->full_name, 'relationship' => $this->relationship, 'phone' => $this->phone, 'email' => $this->email, 'address' => $this->address, 'entitlement_percentage' => $this->entitlement_percentage, 'identification_type' => $this->identification_type, 'is_minor' => $this->is_minor]; }
}


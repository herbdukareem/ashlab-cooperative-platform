<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'code' => $this->code, 'description' => $this->description, 'registration_fee_minor' => $this->registration_fee_minor, 'minimum_contribution_minor' => $this->minimum_contribution_minor, 'requires_guarantor' => $this->requires_guarantor, 'required_guarantors' => $this->required_guarantors, 'requires_kyc' => $this->requires_kyc, 'is_active' => $this->is_active];
    }
}

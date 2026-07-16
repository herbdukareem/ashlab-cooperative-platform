<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberIdentificationResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'type' => $this->type, 'masked_identifier' => str_repeat('*', 6).$this->identifier_last_four, 'country' => $this->country, 'verification_status' => $this->verification_status, 'verified_at' => $this->verified_at, 'rejection_reason' => $this->rejection_reason, 'created_at' => $this->created_at]; }
}


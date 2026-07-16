<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberDocumentResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'type' => $this->type, 'original_name' => $this->original_name, 'mime_type' => $this->mime_type, 'size_bytes' => $this->size_bytes, 'verification_status' => $this->verification_status, 'verified_at' => $this->verified_at, 'rejection_reason' => $this->rejection_reason, 'created_at' => $this->created_at]; }
}


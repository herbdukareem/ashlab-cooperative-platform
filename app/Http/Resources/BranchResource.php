<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'state' => $this->state,
            'local_government_area' => $this->local_government_area,
            'status' => $this->status,
            'manager_id' => $this->manager_id,
            'created_at' => $this->created_at,
        ];
    }
}


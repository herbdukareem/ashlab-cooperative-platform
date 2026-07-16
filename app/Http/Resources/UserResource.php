<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim($this->first_name.' '.$this->last_name),
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'is_platform_admin' => $this->is_platform_admin,
            'last_login_at' => $this->last_login_at,
            'cooperative' => new CooperativeResource($this->whenLoaded('cooperative')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->created_at,
        ];
    }
}


<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CooperativeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'registration_number' => $this->registration_number,
            'registration_date' => $this->registration_date,
            'type' => $this->type,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'state' => $this->state,
            'local_government_area' => $this->local_government_area,
            'currency' => $this->currency,
            'financial_year_start_month' => $this->financial_year_start_month,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}


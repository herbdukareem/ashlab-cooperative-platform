<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'membership_number' => $this->membership_number,
            'first_name' => $this->first_name, 'middle_name' => $this->middle_name, 'last_name' => $this->last_name,
            'full_name' => trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name]))),
            'gender' => $this->gender, 'date_of_birth' => $this->date_of_birth, 'marital_status' => $this->marital_status,
            'phone' => $this->phone, 'email' => $this->email, 'residential_address' => $this->residential_address,
            'state_of_origin' => $this->state_of_origin, 'local_government_area' => $this->local_government_area,
            'occupation' => $this->occupation, 'employer' => $this->employer, 'staff_number' => $this->staff_number, 'department' => $this->department,
            'date_joined' => $this->date_joined, 'status' => $this->status, 'kyc_status' => $this->kyc_status,
            'status_reason' => $this->status_reason, 'approved_at' => $this->approved_at,
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'category' => new MemberCategoryResource($this->whenLoaded('category')),
            'identifications' => MemberIdentificationResource::collection($this->whenLoaded('identifications')),
            'documents' => MemberDocumentResource::collection($this->whenLoaded('documents')),
            'bank_accounts' => MemberBankAccountResource::collection($this->whenLoaded('bankAccounts')),
            'beneficiaries' => MemberBeneficiaryResource::collection($this->whenLoaded('beneficiaries')),
            'guarantors' => MemberGuarantorResource::collection($this->whenLoaded('guarantors')),
            'status_history' => $this->whenLoaded('statusHistory'),
            'created_at' => $this->created_at,
        ];
    }
}


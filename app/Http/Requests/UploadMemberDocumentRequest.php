<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadMemberDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['passport_photograph', 'signature', 'nin_slip', 'bvn_evidence', 'staff_id', 'voter_card', 'drivers_licence', 'international_passport', 'address_evidence', 'membership_form', 'guarantor_form', 'other'])],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}


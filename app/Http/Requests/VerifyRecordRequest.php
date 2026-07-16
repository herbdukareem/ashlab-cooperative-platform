<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyRecordRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['verified', 'rejected'])],
            'reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:2000'],
            'provider_reference' => ['nullable', 'string', 'max:180'],
        ];
    }
}


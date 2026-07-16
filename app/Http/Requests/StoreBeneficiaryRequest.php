<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiaryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:180'],
            'relationship' => ['required', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:1000'],
            'entitlement_percentage' => ['required', 'numeric', 'gt:0', 'max:100'],
            'identification_type' => ['nullable', 'string', 'max:40'],
            'identification' => ['nullable', 'string', 'max:100'],
            'is_minor' => ['required', 'boolean'],
        ];
    }
}


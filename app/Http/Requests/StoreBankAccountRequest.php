<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'bank_code' => ['required', 'string', 'max:20'],
            'bank_name' => ['required', 'string', 'max:180'],
            'account_number' => ['required', 'digits_between:6,20'],
            'account_name' => ['required', 'string', 'max:180'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}


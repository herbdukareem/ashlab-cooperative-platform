<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreIdentificationRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['nin', 'bvn', 'staff_id', 'voter_card', 'drivers_licence', 'international_passport', 'employer_id', 'other'])],
            'identifier' => ['required', 'string', 'min:4', 'max:100'],
            'country' => ['nullable', 'string', 'size:2'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if (in_array($this->input('type'), ['nin', 'bvn'], true) && ! preg_match('/^\d{11}$/', preg_replace('/\s+/', '', (string) $this->input('identifier')))) {
                $validator->errors()->add('identifier', 'NIN and BVN values must contain exactly 11 digits.');
            }
        }];
    }
}

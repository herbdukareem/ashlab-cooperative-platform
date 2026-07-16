<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCooperativeRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->is_platform_admin === true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:100', 'unique:cooperatives,slug'],
            'registration_number' => ['nullable', 'string', 'max:100', 'unique:cooperatives,registration_number'],
            'registration_date' => ['nullable', 'date', 'before_or_equal:today'],
            'type' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'state' => ['nullable', 'string', 'max:100'],
            'local_government_area' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', Rule::in(['NGN', 'USD', 'GBP', 'EUR'])],
            'financial_year_start_month' => ['required', 'integer', 'between:1,12'],
            'admin.first_name' => ['required', 'string', 'max:100'],
            'admin.last_name' => ['required', 'string', 'max:100'],
            'admin.email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'admin.phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'admin.password' => ['required', 'string', 'min:12', 'confirmed'],
        ];
    }
}


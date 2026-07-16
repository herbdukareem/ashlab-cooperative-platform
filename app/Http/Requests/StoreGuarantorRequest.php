<?php

namespace App\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuarantorRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'guarantor_member_id' => ['nullable', 'different:member_id', Rule::exists('members', 'id')->where('cooperative_id', app(TenantContext::class)->id())],
            'external_name' => ['nullable', 'required_without:guarantor_member_id', 'string', 'max:180'],
            'relationship' => ['required', 'string', 'max:80'],
            'phone' => ['nullable', 'required_without:guarantor_member_id', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:1000'],
            'employer' => ['nullable', 'string', 'max:180'],
            'guarantee_limit_minor' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}


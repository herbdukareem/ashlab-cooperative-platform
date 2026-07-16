<?php

namespace App\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'alpha_dash:ascii', 'max:30', Rule::unique('member_categories')->where('cooperative_id', app(TenantContext::class)->id())->ignore($this->route('member_category'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'registration_fee_minor' => ['required', 'integer', 'min:0'],
            'minimum_contribution_minor' => ['required', 'integer', 'min:0'],
            'requires_guarantor' => ['required', 'boolean'],
            'required_guarantors' => ['required', 'integer', 'between:0,10'],
            'requires_kyc' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}

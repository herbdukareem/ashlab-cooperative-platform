<?php

namespace App\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $tenantId = app(TenantContext::class)->id();
        $member = $this->route('member');

        return [
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('cooperative_id', $tenantId)],
            'member_category_id' => ['required', Rule::exists('member_categories', 'id')->where('cooperative_id', $tenantId)->where('is_active', true)],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed', 'separated'])],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('members')->where('cooperative_id', $tenantId)->ignore($member)],
            'email' => ['nullable', 'email', 'max:180', Rule::unique('members')->where('cooperative_id', $tenantId)->ignore($member)],
            'residential_address' => ['nullable', 'string', 'max:1000'],
            'state_of_origin' => ['nullable', 'string', 'max:100'],
            'local_government_area' => ['nullable', 'string', 'max:100'],
            'occupation' => ['nullable', 'string', 'max:150'],
            'employer' => ['nullable', 'string', 'max:180'],
            'staff_number' => ['nullable', 'string', 'max:80'],
            'department' => ['nullable', 'string', 'max:180'],
            'date_joined' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}


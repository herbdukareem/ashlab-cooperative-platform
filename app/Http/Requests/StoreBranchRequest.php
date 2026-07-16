<?php

namespace App\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $tenantId = app(TenantContext::class)->id();
        return [
            'name' => ['required', 'string', 'max:180'],
            'code' => ['required', 'alpha_dash:ascii', 'max:30', Rule::unique('branches')->where('cooperative_id', $tenantId)->ignore($this->route('branch'))],
            'type' => ['required', Rule::in(['head_office', 'branch', 'department', 'unit'])],
            'manager_id' => ['nullable', Rule::exists('users', 'id')->where('cooperative_id', $tenantId)],
            'email' => ['nullable', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'state' => ['nullable', 'string', 'max:100'],
            'local_government_area' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }
}

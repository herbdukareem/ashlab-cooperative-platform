<?php

namespace App\Http\Requests;

use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $tenantId = app(TenantContext::class)->id();
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('cooperative_id', $tenantId)],
            'status' => ['nullable', Rule::in(['pending', 'active', 'suspended', 'disabled'])],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => [Rule::exists('roles', 'id')->where('cooperative_id', $tenantId)],
        ];
    }
}


<?php

namespace App\Http\Requests;

use App\Enums\MemberStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeMemberStatusRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(MemberStatus::class)],
            'reason' => ['nullable', 'required_unless:status,active', 'string', 'max:2000'],
        ];
    }
}


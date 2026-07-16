<?php

namespace App\Http\Requests;

use App\Enums\ContributionFrequency;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContributionPlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'code' => ['required', 'alpha_dash:ascii', 'max:40', Rule::unique('contribution_plans')->where('cooperative_id', app(TenantContext::class)->id())->ignore($this->route('contribution_plan'))],
            'description' => ['nullable', 'string', 'max:2000'],
            'frequency' => ['required', Rule::enum(ContributionFrequency::class)],
            'minimum_amount_minor' => ['required', 'integer', 'min:0'],
            'maximum_amount_minor' => ['nullable', 'integer', 'gte:minimum_amount_minor'],
            'fixed_amount_minor' => ['nullable', 'required_if:is_fixed_amount,true', 'integer', 'min:0'],
            'is_fixed_amount' => ['required', 'boolean'], 'is_mandatory' => ['required', 'boolean'],
            'start_date' => ['nullable', 'date'], 'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'grace_period_days' => ['required', 'integer', 'between:0,365'],
            'eligible_member_category_ids' => ['nullable', 'array'],
            'eligible_member_category_ids.*' => ['ulid', Rule::exists('member_categories', 'id')->where('cooperative_id', app(TenantContext::class)->id())],
            'withdrawal_rules' => ['nullable', 'array'], 'penalty_rules' => ['nullable', 'array'], 'schedule_configuration' => ['nullable', 'array'],
            'schedule_configuration.dates' => ['required_if:frequency,custom', 'array'], 'schedule_configuration.dates.*' => ['date'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}

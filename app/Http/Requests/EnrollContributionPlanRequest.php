<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class EnrollContributionPlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['contribution_plan_id' => ['required', 'ulid'], 'contribution_amount_minor' => ['nullable', 'integer', 'min:1'], 'start_date' => ['required', 'date'], 'end_date' => ['nullable', 'date', 'after_or_equal:start_date']]; }
}

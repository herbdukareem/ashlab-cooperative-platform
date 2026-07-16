<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class OpenSavingsAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['savings_product_id' => ['required','ulid'], 'name' => ['nullable','string','max:160'], 'goal_amount_minor' => ['nullable','integer','min:1'], 'maturity_date' => ['nullable','date','after:today']]; }
}

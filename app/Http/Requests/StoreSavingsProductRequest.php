<?php
namespace App\Http\Requests;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreSavingsProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return [
        'name' => ['required','string','max:160'], 'code' => ['required','alpha_dash:ascii','max:40', Rule::unique('savings_products')->where('cooperative_id', app(TenantContext::class)->id())->ignore($this->route('savings_product'))],
        'description' => ['nullable','string','max:2000'], 'minimum_opening_balance_minor' => ['required','integer','min:0'], 'minimum_balance_minor' => ['required','integer','min:0'],
        'minimum_withdrawal_minor' => ['required','integer','min:0'], 'maximum_withdrawal_minor' => ['nullable','integer','gte:minimum_withdrawal_minor'], 'lock_in_days' => ['required','integer','between:0,3650'],
        'interest_rate_basis_points' => ['required','integer','between:0,10000'], 'allow_multiple_accounts' => ['required','boolean'], 'allows_withdrawal' => ['required','boolean'], 'is_active' => ['required','boolean'], 'rules' => ['nullable','array'], 'rules.withdrawal_fee_minor' => ['nullable','integer','min:0'],
    ]; }
}

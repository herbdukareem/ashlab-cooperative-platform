<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class RecordCollectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return [
        'idempotency_key' => ['required','string','max:120'], 'channel' => ['required','in:cash,bank_transfer,card,direct_debit,mobile_money,ussd,cheque,other'], 'currency' => ['sometimes','string','size:3'],
        'amount_minor' => ['required','integer','min:1'], 'contribution_amount_minor' => ['sometimes','integer','min:0'], 'external_reference' => ['nullable','string','max:160'], 'received_at' => ['nullable','date'], 'notes' => ['nullable','string','max:2000'],
        'savings_allocations' => ['sometimes','array'], 'savings_allocations.*.savings_account_id' => ['required','ulid'], 'savings_allocations.*.amount_minor' => ['required','integer','min:1'],
    ]; }
}

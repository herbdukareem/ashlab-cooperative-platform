<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class RequestSavingsWithdrawalRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['amount_minor' => ['required','integer','min:1'], 'reason' => ['nullable','string','max:2000']]; }
}

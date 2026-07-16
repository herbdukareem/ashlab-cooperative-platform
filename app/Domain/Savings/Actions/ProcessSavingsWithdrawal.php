<?php

namespace App\Domain\Savings\Actions;

use App\Enums\SavingsAccountStatus;
use App\Enums\SavingsTransactionType;
use App\Enums\WithdrawalStatus;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Models\SavingsWithdrawalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProcessSavingsWithdrawal
{
    public function request(Member $member, SavingsAccount $account, array $data): SavingsWithdrawalRequest
    {
        abort_unless($account->member_id === $member->id, 404);
        $account->loadMissing('product');
        if ($account->status !== SavingsAccountStatus::Active || ! $account->product->allows_withdrawal) throw ValidationException::withMessages(['savings_account_id' => ['Withdrawals are not allowed on this account.']]);
        if ($account->opened_at->addDays($account->product->lock_in_days)->isFuture()) throw ValidationException::withMessages(['amount_minor' => ['The savings account is still within its lock-in period.']]);
        $amount = (int) $data['amount_minor'];
        if ($amount < $account->product->minimum_withdrawal_minor || ($account->product->maximum_withdrawal_minor !== null && $amount > $account->product->maximum_withdrawal_minor)) {
            throw ValidationException::withMessages(['amount_minor' => ['The withdrawal amount is outside the product limits.']]);
        }
        $fee = (int) ($account->product->rules['withdrawal_fee_minor'] ?? 0);
        $total = $amount + $fee;
        if ($account->available_balance_minor - $total < $account->product->minimum_balance_minor) throw ValidationException::withMessages(['amount_minor' => ['Insufficient available balance after applying the minimum-balance rule.']]);

        return SavingsWithdrawalRequest::query()->create([
            'member_id' => $member->id, 'savings_account_id' => $account->id,
            'reference' => 'WDR-'.strtoupper(Str::ulid()), 'amount_minor' => $amount,
            'fee_minor' => $fee, 'total_debit_minor' => $total, 'status' => WithdrawalStatus::Pending,
            'reason' => $data['reason'] ?? null, 'requested_at' => now(), 'requested_by' => Auth::id(),
        ]);
    }

    public function approve(SavingsWithdrawalRequest $withdrawal, ?string $reason = null): SavingsWithdrawalRequest
    {
        return DB::transaction(function () use ($withdrawal, $reason): SavingsWithdrawalRequest {
            $withdrawal = SavingsWithdrawalRequest::query()->lockForUpdate()->findOrFail($withdrawal->id);
            if ($withdrawal->status !== WithdrawalStatus::Pending) throw ValidationException::withMessages(['status' => ['Only a pending withdrawal can be approved.']]);
            $account = SavingsAccount::query()->with('product')->lockForUpdate()->findOrFail($withdrawal->savings_account_id);
            if ($account->available_balance_minor - $withdrawal->total_debit_minor < $account->product->minimum_balance_minor) throw ValidationException::withMessages(['amount_minor' => ['The available balance is no longer sufficient.']]);
            $account->update(['available_balance_minor' => $account->available_balance_minor - $withdrawal->total_debit_minor]);
            $withdrawal->update(['status' => WithdrawalStatus::Approved, 'approved_at' => now(), 'approved_by' => Auth::id(), 'decision_reason' => $reason]);
            return $withdrawal->refresh();
        });
    }

    public function reject(SavingsWithdrawalRequest $withdrawal, string $reason): SavingsWithdrawalRequest
    {
        if ($withdrawal->status !== WithdrawalStatus::Pending) throw ValidationException::withMessages(['status' => ['Only a pending withdrawal can be rejected.']]);
        $withdrawal->update(['status' => WithdrawalStatus::Rejected, 'approved_at' => now(), 'approved_by' => Auth::id(), 'decision_reason' => $reason]);
        return $withdrawal->refresh();
    }

    public function complete(SavingsWithdrawalRequest $withdrawal): SavingsWithdrawalRequest
    {
        return DB::transaction(function () use ($withdrawal): SavingsWithdrawalRequest {
            $withdrawal = SavingsWithdrawalRequest::query()->lockForUpdate()->findOrFail($withdrawal->id);
            if ($withdrawal->status !== WithdrawalStatus::Approved) throw ValidationException::withMessages(['status' => ['Only an approved withdrawal can be completed.']]);
            $account = SavingsAccount::query()->lockForUpdate()->findOrFail($withdrawal->savings_account_id);
            if ($account->balance_minor < $withdrawal->total_debit_minor) throw ValidationException::withMessages(['amount_minor' => ['The account balance is insufficient.']]);
            $newBalance = $account->balance_minor - $withdrawal->total_debit_minor;
            $account->update(['balance_minor' => $newBalance]);
            SavingsTransaction::query()->create([
                'savings_account_id' => $account->id, 'reference' => $withdrawal->reference,
                'type' => SavingsTransactionType::Withdrawal, 'amount_minor' => $withdrawal->total_debit_minor,
                'balance_after_minor' => $newBalance, 'effective_at' => now(), 'performed_by' => Auth::id(),
                'description' => 'Savings withdrawal, including applicable fee.', 'metadata' => ['withdrawal_request_id' => $withdrawal->id, 'member_amount_minor' => $withdrawal->amount_minor, 'fee_minor' => $withdrawal->fee_minor],
            ]);
            $withdrawal->update(['status' => WithdrawalStatus::Paid, 'completed_at' => now(), 'completed_by' => Auth::id()]);
            return $withdrawal->refresh();
        });
    }
}

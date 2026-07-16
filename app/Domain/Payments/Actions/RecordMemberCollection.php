<?php

namespace App\Domain\Payments\Actions;

use App\Enums\ContributionObligationStatus;
use App\Enums\PaymentStatus;
use App\Enums\SavingsAccountStatus;
use App\Enums\SavingsTransactionType;
use App\Models\ContributionObligation;
use App\Models\Member;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Domain\Accounting\Services\AutoPoster;

class RecordMemberCollection
{
    public function __construct(private readonly AutoPoster $accounting) {}
    public function execute(Member $member, array $data): Payment
    {
        if ($existing = Payment::query()->where('idempotency_key', $data['idempotency_key'])->first()) return $existing->load('allocations');

        $savingsTotal = collect($data['savings_allocations'] ?? [])->sum('amount_minor');
        $requested = (int) ($data['contribution_amount_minor'] ?? 0) + $savingsTotal;
        if ($requested > (int) $data['amount_minor']) throw ValidationException::withMessages(['amount_minor' => ['Requested allocations exceed the payment amount.']]);

        return DB::transaction(function () use ($member, $data): Payment {
            $payment = Payment::query()->create([
                'member_id' => $member->id,
                'reference' => 'PAY-'.strtoupper(Str::ulid()),
                'idempotency_key' => $data['idempotency_key'],
                'type' => 'collection',
                'channel' => $data['channel'],
                'currency' => $data['currency'] ?? 'NGN',
                'amount_minor' => $data['amount_minor'],
                'allocated_minor' => 0,
                'unallocated_minor' => $data['amount_minor'],
                'status' => PaymentStatus::Successful,
                'external_reference' => $data['external_reference'] ?? null,
                'received_at' => $data['received_at'] ?? now(),
                'recorded_by' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]);

            $allocated = $this->allocateContributions($payment, $member, (int) ($data['contribution_amount_minor'] ?? 0));
            foreach ($data['savings_allocations'] ?? [] as $index => $allocation) {
                $allocated += $this->depositSavings($payment, $member, $allocation, $index + 1);
            }

            $payment->update(['allocated_minor' => $allocated, 'unallocated_minor' => $payment->amount_minor - $allocated]);
            $payment->load('allocations');
            $context = ['member_id' => $member->id, 'entry_date' => $payment->received_at->toDateString(), 'currency' => $payment->currency];
            $this->accounting->postIfConfigured('collection.contribution', $payment, (int) $payment->allocations->where('allocation_type', 'contribution_obligation')->sum('amount_minor'), $context);
            $this->accounting->postIfConfigured('collection.savings', $payment, (int) $payment->allocations->where('allocation_type', 'savings_account')->sum('amount_minor'), $context);
            $this->accounting->postIfConfigured('collection.unallocated', $payment, $payment->unallocated_minor, $context);
            return $payment->refresh()->load('allocations');
        });
    }

    private function allocateContributions(Payment $payment, Member $member, int $amount): int
    {
        $remaining = $amount;
        $obligations = ContributionObligation::query()->where('member_id', $member->id)
            ->whereNotIn('status', [ContributionObligationStatus::Paid->value, ContributionObligationStatus::Waived->value, ContributionObligationStatus::Cancelled->value])
            ->orderBy('due_date')->lockForUpdate()->get();

        foreach ($obligations as $obligation) {
            if ($remaining <= 0) break;
            $outstanding = $obligation->amount_due_minor - $obligation->amount_paid_minor;
            $applied = min($remaining, $outstanding);
            if ($applied <= 0) continue;
            $newPaid = $obligation->amount_paid_minor + $applied;
            $obligation->update([
                'amount_paid_minor' => $newPaid,
                'status' => $newPaid >= $obligation->amount_due_minor ? ContributionObligationStatus::Paid : ContributionObligationStatus::PartiallyPaid,
                'paid_at' => $newPaid >= $obligation->amount_due_minor ? now() : null,
            ]);
            PaymentAllocation::query()->create(['payment_id' => $payment->id, 'allocation_type' => 'contribution_obligation', 'allocation_id' => $obligation->id, 'amount_minor' => $applied]);
            $remaining -= $applied;
        }
        return $amount - $remaining;
    }

    private function depositSavings(Payment $payment, Member $member, array $allocation, int $sequence): int
    {
        $account = SavingsAccount::query()->where('member_id', $member->id)->lockForUpdate()->findOrFail($allocation['savings_account_id']);
        if ($account->status !== SavingsAccountStatus::Active) throw ValidationException::withMessages(['savings_allocations' => ['Savings deposits require an active account.']]);
        $amount = (int) $allocation['amount_minor'];
        $newBalance = $account->balance_minor + $amount;
        $account->update(['balance_minor' => $newBalance, 'available_balance_minor' => $account->available_balance_minor + $amount]);
        SavingsTransaction::query()->create([
            'savings_account_id' => $account->id, 'payment_id' => $payment->id,
            'reference' => $payment->reference.'-S'.$sequence, 'type' => SavingsTransactionType::Deposit,
            'amount_minor' => $amount, 'balance_after_minor' => $newBalance,
            'effective_at' => $payment->received_at, 'performed_by' => Auth::id(), 'description' => 'Member savings deposit',
        ]);
        PaymentAllocation::query()->create(['payment_id' => $payment->id, 'allocation_type' => 'savings_account', 'allocation_id' => $account->id, 'amount_minor' => $amount]);
        return $amount;
    }
}

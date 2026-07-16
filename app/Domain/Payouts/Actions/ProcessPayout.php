<?php

namespace App\Domain\Payouts\Actions;

use App\Domain\Accounting\Actions\ReverseJournal;
use App\Domain\Accounting\Services\AutoPoster;
use App\Enums\JournalStatus;
use App\Enums\LoanApplicationStatus;
use App\Enums\LoanStatus;
use App\Enums\PayoutStatus;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\Payout;
use App\Models\PayoutEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcessPayout
{
    public function __construct(private AutoPoster $accounting, private ReverseJournal $reversals) {}

    public function approve(Payout $payout): Payout
    {
        if ($payout->status !== PayoutStatus::PendingReview) throw ValidationException::withMessages(['status' => ['Only pending payouts can be approved.']]);
        if ($payout->created_by === Auth::id()) throw ValidationException::withMessages(['actor' => ['The payout creator cannot approve the same payout.']]);
        $payout->update(['status' => PayoutStatus::Approved, 'reviewed_by' => Auth::id()]);
        return $payout->refresh();
    }

    public function release(Payout $payout, string $provider): Payout
    {
        if ($payout->status !== PayoutStatus::Approved) throw ValidationException::withMessages(['status' => ['Only approved payouts can be released.']]);
        if ($payout->created_by === Auth::id()) throw ValidationException::withMessages(['actor' => ['The payout creator cannot release the same payout.']]);
        if ($payout->scheduled_for?->isFuture()) throw ValidationException::withMessages(['scheduled_for' => ['This payout is not due for release.']]);
        $payout->update(['status' => PayoutStatus::Processing, 'provider' => $provider, 'released_by' => Auth::id(), 'released_at' => now()]);
        $payout->events()->create(['event_type' => 'released', 'payload' => ['provider' => $provider], 'occurred_at' => now()]);
        return $payout->refresh();
    }

    public function providerEvent(Payout $payout, array $data): Payout
    {
        return DB::transaction(function () use ($payout, $data): Payout {
            if (PayoutEvent::query()->where('provider_event_id', $data['provider_event_id'])->exists()) return $payout->refresh();
            if (in_array($data['event_type'], ['paid', 'failed'], true) && $payout->status !== PayoutStatus::Processing) throw ValidationException::withMessages(['status' => ['The payout has not been released for processing.']]);
            if ($data['event_type'] === 'reversed' && $payout->status !== PayoutStatus::Paid) throw ValidationException::withMessages(['status' => ['Only a paid payout can be reversed.']]);
            $status = match ($data['event_type']) { 'paid' => PayoutStatus::Paid, 'failed' => PayoutStatus::Failed, 'reversed' => PayoutStatus::Reversed, default => $payout->status };
            $payout->update(['status' => $status, 'provider_reference' => $data['provider_reference'] ?? $payout->provider_reference, 'failure_reason' => $data['failure_reason'] ?? null, 'paid_at' => $status === PayoutStatus::Paid ? now() : $payout->paid_at]);
            $payout->events()->create(['event_type' => $data['event_type'], 'provider_event_id' => $data['provider_event_id'], 'payload' => $data['payload'] ?? null, 'occurred_at' => now()]);

            if ($status === PayoutStatus::Paid) {
                $context = ['member_id' => $payout->member_id, 'entry_date' => today()->toDateString(), 'currency' => $payout->currency];
                if ($payout->payable_type === Loan::class) {
                    $loan = Loan::query()->findOrFail($payout->payable_id);
                    $context['loan_id'] = $loan->id;
                    $gross = (int) ($payout->metadata['gross_principal_minor'] ?? $payout->amount_minor);
                    $deducted = (int) ($payout->metadata['deducted_charges_minor'] ?? 0);
                    $this->accounting->postIfConfigured('loan.principal_recognized', $payout, $gross, $context);
                    $this->accounting->postIfConfigured('loan.charge_added', $payout, (int) $loan->charges()->where('treatment', 'add_to_balance')->sum('amount_minor'), $context);
                    $this->accounting->postIfConfigured('loan.disbursement_paid', $payout, $payout->amount_minor, $context);
                    $this->accounting->postIfConfigured('loan.disbursement_charge', $payout, $deducted, $context);
                    $loan->update(['status' => LoanStatus::Active, 'disbursed_at' => now()->toDateString()]);
                    $loan->application()->update(['status' => LoanApplicationStatus::Disbursed]);
                } else {
                    $this->accounting->postIfConfigured('payout.'.$payout->type->value, $payout, $payout->amount_minor, $context);
                }
            }

            if ($status === PayoutStatus::Reversed) {
                JournalEntry::query()->where('source_type', $payout->getMorphClass())->where('source_id', $payout->id)->where('status', JournalStatus::Posted->value)->get()->each(fn (JournalEntry $entry) => $this->reversals->execute($entry, 'Provider payout reversal', today()->toDateString()));
                if ($payout->payable_type === Loan::class) {
                    $loan = Loan::query()->findOrFail($payout->payable_id);
                    $loan->update(['status' => LoanStatus::PendingDisbursement]);
                    $loan->application()->update(['status' => LoanApplicationStatus::Approved]);
                }
            }
            return $payout->refresh();
        });
    }
}

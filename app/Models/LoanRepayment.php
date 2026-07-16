<?php

namespace App\Models;

use App\Domain\Accounting\Actions\ReverseJournal;
use App\Enums\JournalStatus;
use App\Enums\RepaymentStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanRepayment extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'loan_id', 'member_id', 'reversal_of_id', 'reference', 'idempotency_key', 'channel', 'currency', 'amount_minor', 'allocated_minor', 'unallocated_minor', 'status', 'external_reference', 'received_at', 'recorded_by', 'notes', 'reversed_at', 'reversed_by', 'reversal_reason'];
    protected function casts(): array { return ['amount_minor' => 'integer', 'allocated_minor' => 'integer', 'unallocated_minor' => 'integer', 'status' => RepaymentStatus::class, 'received_at' => 'datetime', 'reversed_at' => 'datetime']; }

    protected static function booted(): void
    {
        static::updated(function (LoanRepayment $repayment): void {
            if (! $repayment->wasChanged('status') || $repayment->status !== RepaymentStatus::Reversed) return;
            JournalEntry::query()->where('source_type', $repayment->getMorphClass())->where('source_id', $repayment->id)->where('status', JournalStatus::Posted->value)->get()->each(
                fn (JournalEntry $entry) => app(ReverseJournal::class)->execute($entry, $repayment->reversal_reason ?? 'Repayment reversal', today()->toDateString())
            );
        });
    }

    public function loan(): BelongsTo { return $this->belongsTo(Loan::class); }
    public function allocations(): HasMany { return $this->hasMany(LoanRepaymentAllocation::class); }
    public function reversalOf(): BelongsTo { return $this->belongsTo(self::class, 'reversal_of_id'); }
}

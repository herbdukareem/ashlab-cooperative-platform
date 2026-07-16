<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsWithdrawalRequest extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'member_id', 'savings_account_id', 'reference', 'amount_minor', 'fee_minor', 'total_debit_minor', 'status', 'reason', 'decision_reason', 'requested_at', 'requested_by', 'approved_at', 'approved_by', 'completed_at', 'completed_by'];
    protected function casts(): array { return ['amount_minor' => 'integer', 'fee_minor' => 'integer', 'total_debit_minor' => 'integer', 'status' => WithdrawalStatus::class, 'requested_at' => 'datetime', 'approved_at' => 'datetime', 'completed_at' => 'datetime']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function account(): BelongsTo { return $this->belongsTo(SavingsAccount::class, 'savings_account_id'); }
}

<?php

namespace App\Models;

use App\Domain\Accounting\Services\AutoPoster;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRecoveryAction extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable = ['cooperative_id', 'loan_recovery_case_id', 'type', 'notes', 'next_action_at', 'expense_minor', 'performed_by', 'performed_at'];
    protected function casts(): array { return ['next_action_at' => 'datetime', 'expense_minor' => 'integer', 'performed_at' => 'datetime']; }
    protected static function booted(): void { static::created(function (LoanRecoveryAction $action): void { if ($action->expense_minor <= 0) return; $case = $action->recoveryCase()->with('loan')->firstOrFail(); app(AutoPoster::class)->postIfConfigured('recovery.expense_assessed', $action, $action->expense_minor, ['member_id' => $case->member_id, 'loan_id' => $case->loan_id, 'entry_date' => $action->performed_at->toDateString()]); }); }
    public function recoveryCase(): BelongsTo { return $this->belongsTo(LoanRecoveryCase::class, 'loan_recovery_case_id'); }
}

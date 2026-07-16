<?php

namespace App\Models;

use App\Enums\ContributionObligationStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContributionObligation extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'member_id', 'contribution_plan_id', 'member_contribution_plan_id', 'period_start', 'period_end', 'due_date', 'amount_due_minor', 'amount_paid_minor', 'status', 'paid_at'];
    protected function casts(): array { return ['period_start' => 'date', 'period_end' => 'date', 'due_date' => 'date', 'amount_due_minor' => 'integer', 'amount_paid_minor' => 'integer', 'status' => ContributionObligationStatus::class, 'paid_at' => 'datetime']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function plan(): BelongsTo { return $this->belongsTo(ContributionPlan::class, 'contribution_plan_id'); }
    public function enrollment(): BelongsTo { return $this->belongsTo(MemberContributionPlan::class, 'member_contribution_plan_id'); }
}

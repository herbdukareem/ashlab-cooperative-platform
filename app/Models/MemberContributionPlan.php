<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberContributionPlan extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'member_id', 'contribution_plan_id', 'contribution_amount_minor', 'start_date', 'end_date', 'next_due_date', 'status'];
    protected function casts(): array { return ['contribution_amount_minor' => 'integer', 'start_date' => 'date', 'end_date' => 'date', 'next_due_date' => 'date']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function plan(): BelongsTo { return $this->belongsTo(ContributionPlan::class, 'contribution_plan_id'); }
    public function obligations(): HasMany { return $this->hasMany(ContributionObligation::class); }
}

<?php

namespace App\Models;

use App\Enums\ContributionFrequency;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributionPlan extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['cooperative_id', 'name', 'code', 'description', 'frequency', 'minimum_amount_minor', 'maximum_amount_minor', 'fixed_amount_minor', 'is_fixed_amount', 'is_mandatory', 'start_date', 'end_date', 'grace_period_days', 'eligible_member_category_ids', 'withdrawal_rules', 'penalty_rules', 'schedule_configuration', 'is_active'];
    protected function casts(): array { return ['frequency' => ContributionFrequency::class, 'minimum_amount_minor' => 'integer', 'maximum_amount_minor' => 'integer', 'fixed_amount_minor' => 'integer', 'is_fixed_amount' => 'boolean', 'is_mandatory' => 'boolean', 'start_date' => 'date', 'end_date' => 'date', 'grace_period_days' => 'integer', 'eligible_member_category_ids' => 'array', 'withdrawal_rules' => 'array', 'penalty_rules' => 'array', 'schedule_configuration' => 'array', 'is_active' => 'boolean']; }
    public function enrollments(): HasMany { return $this->hasMany(MemberContributionPlan::class); }
    public function obligations(): HasMany { return $this->hasMany(ContributionObligation::class); }
}

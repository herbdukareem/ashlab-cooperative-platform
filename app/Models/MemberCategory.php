<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberCategory extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['cooperative_id', 'name', 'code', 'description', 'registration_fee_minor', 'minimum_contribution_minor', 'requires_guarantor', 'required_guarantors', 'requires_kyc', 'is_active'];
    protected function casts(): array { return ['registration_fee_minor' => 'integer', 'minimum_contribution_minor' => 'integer', 'requires_guarantor' => 'boolean', 'required_guarantors' => 'integer', 'requires_kyc' => 'boolean', 'is_active' => 'boolean']; }
    public function members(): HasMany { return $this->hasMany(Member::class); }
}

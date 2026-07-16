<?php

namespace App\Models;

use App\Enums\ConsentStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberGuarantor extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable = ['cooperative_id', 'member_id', 'guarantor_member_id', 'external_name', 'relationship', 'phone', 'email', 'address', 'employer', 'guarantee_limit_minor', 'guaranteed_amount_minor', 'consent_status', 'consented_at', 'is_active'];
    protected function casts(): array { return ['guarantee_limit_minor' => 'integer', 'guaranteed_amount_minor' => 'integer', 'consent_status' => ConsentStatus::class, 'consented_at' => 'datetime', 'is_active' => 'boolean']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function guarantorMember(): BelongsTo { return $this->belongsTo(Member::class, 'guarantor_member_id'); }
}


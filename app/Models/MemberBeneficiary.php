<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberBeneficiary extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable = ['cooperative_id', 'member_id', 'full_name', 'relationship', 'phone', 'email', 'address', 'entitlement_percentage', 'identification_type', 'identification_encrypted', 'is_minor'];
    protected $hidden = ['identification_encrypted'];
    protected function casts(): array { return ['entitlement_percentage' => 'decimal:2', 'is_minor' => 'boolean']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}


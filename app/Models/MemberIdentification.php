<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberIdentification extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable = ['cooperative_id', 'member_id', 'type', 'identifier_encrypted', 'identifier_hash', 'identifier_last_four', 'country', 'verification_status', 'verified_by', 'verified_at', 'rejection_reason', 'metadata'];
    protected $hidden = ['identifier_encrypted', 'identifier_hash'];
    protected function casts(): array { return ['verification_status' => VerificationStatus::class, 'verified_at' => 'datetime', 'metadata' => 'array']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}


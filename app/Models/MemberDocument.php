<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDocument extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable = ['cooperative_id', 'member_id', 'type', 'disk', 'path', 'original_name', 'mime_type', 'size_bytes', 'checksum_sha256', 'verification_status', 'verified_by', 'verified_at', 'rejection_reason'];
    protected $hidden = ['path'];
    protected function casts(): array { return ['size_bytes' => 'integer', 'verification_status' => VerificationStatus::class, 'verified_at' => 'datetime']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}


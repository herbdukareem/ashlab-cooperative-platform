<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberBankAccount extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;
    protected $fillable = ['cooperative_id', 'member_id', 'bank_code', 'bank_name', 'account_number_encrypted', 'account_number_hash', 'account_number_last_four', 'account_name', 'is_primary', 'verification_status', 'provider_reference', 'verified_by', 'verified_at'];
    protected $hidden = ['account_number_encrypted', 'account_number_hash'];
    protected function casts(): array { return ['is_primary' => 'boolean', 'verification_status' => VerificationStatus::class, 'verified_at' => 'datetime']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}


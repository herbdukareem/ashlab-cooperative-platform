<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'member_id', 'reference', 'idempotency_key', 'type', 'channel', 'currency', 'amount_minor', 'allocated_minor', 'unallocated_minor', 'status', 'external_reference', 'received_at', 'recorded_by', 'notes', 'metadata'];
    protected function casts(): array { return ['amount_minor' => 'integer', 'allocated_minor' => 'integer', 'unallocated_minor' => 'integer', 'status' => PaymentStatus::class, 'received_at' => 'datetime', 'metadata' => 'array']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
    public function allocations(): HasMany { return $this->hasMany(PaymentAllocation::class); }
}

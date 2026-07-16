<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    use BelongsToTenant, HasUlids;
    const UPDATED_AT = null;
    protected $fillable = ['cooperative_id', 'payment_id', 'allocation_type', 'allocation_id', 'amount_minor', 'reversed_at', 'reversed_by', 'reversal_reason'];
    protected function casts(): array { return ['amount_minor' => 'integer', 'reversed_at' => 'datetime']; }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    protected static function booted(): void { static::updating(fn () => throw new \LogicException('Payment allocations are immutable; create a reversal.')); static::deleting(fn () => throw new \LogicException('Payment allocations are immutable.')); }
}

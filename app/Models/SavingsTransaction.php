<?php

namespace App\Models;

use App\Enums\SavingsTransactionType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsTransaction extends Model
{
    use BelongsToTenant, HasUlids;
    const UPDATED_AT = null;
    protected $fillable = ['cooperative_id', 'savings_account_id', 'payment_id', 'related_transaction_id', 'reference', 'type', 'amount_minor', 'balance_after_minor', 'effective_at', 'performed_by', 'description', 'metadata'];
    protected function casts(): array { return ['type' => SavingsTransactionType::class, 'amount_minor' => 'integer', 'balance_after_minor' => 'integer', 'effective_at' => 'datetime', 'metadata' => 'array']; }
    public function account(): BelongsTo { return $this->belongsTo(SavingsAccount::class, 'savings_account_id'); }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    protected static function booted(): void { static::updating(fn () => throw new \LogicException('Savings transactions are immutable; create a reversal.')); static::deleting(fn () => throw new \LogicException('Savings transactions are immutable.')); }
}

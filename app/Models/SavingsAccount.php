<?php

namespace App\Models;

use App\Enums\SavingsAccountStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsAccount extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'member_id', 'savings_product_id', 'account_number', 'name', 'balance_minor', 'available_balance_minor', 'goal_amount_minor', 'maturity_date', 'opened_at', 'status'];
    protected function casts(): array { return ['balance_minor' => 'integer', 'available_balance_minor' => 'integer', 'goal_amount_minor' => 'integer', 'maturity_date' => 'date', 'opened_at' => 'datetime', 'status' => SavingsAccountStatus::class]; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function product(): BelongsTo { return $this->belongsTo(SavingsProduct::class, 'savings_product_id'); }
    public function transactions(): HasMany { return $this->hasMany(SavingsTransaction::class); }
    public function withdrawalRequests(): HasMany { return $this->hasMany(SavingsWithdrawalRequest::class); }
}

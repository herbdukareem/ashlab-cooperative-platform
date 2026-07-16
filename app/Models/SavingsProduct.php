<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingsProduct extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['cooperative_id', 'name', 'code', 'description', 'minimum_opening_balance_minor', 'minimum_balance_minor', 'minimum_withdrawal_minor', 'maximum_withdrawal_minor', 'lock_in_days', 'interest_rate_basis_points', 'allow_multiple_accounts', 'allows_withdrawal', 'is_active', 'rules'];
    protected function casts(): array { return ['minimum_opening_balance_minor' => 'integer', 'minimum_balance_minor' => 'integer', 'minimum_withdrawal_minor' => 'integer', 'maximum_withdrawal_minor' => 'integer', 'lock_in_days' => 'integer', 'interest_rate_basis_points' => 'integer', 'allow_multiple_accounts' => 'boolean', 'allows_withdrawal' => 'boolean', 'is_active' => 'boolean', 'rules' => 'array']; }
    public function accounts(): HasMany { return $this->hasMany(SavingsAccount::class); }
}

<?php

namespace App\Models;

use App\Enums\BranchStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['cooperative_id', 'manager_id', 'name', 'code', 'type', 'email', 'phone', 'address', 'state', 'local_government_area', 'status'];

    protected function casts(): array { return ['status' => BranchStatus::class]; }

    public function manager(): BelongsTo { return $this->belongsTo(User::class, 'manager_id'); }
    public function users(): HasMany { return $this->hasMany(User::class); }
}


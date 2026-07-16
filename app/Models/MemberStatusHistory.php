<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberStatusHistory extends Model
{
    use BelongsToTenant, HasUlids;
    const UPDATED_AT = null;
    protected $fillable = ['cooperative_id', 'member_id', 'actor_id', 'from_status', 'to_status', 'reason', 'metadata'];
    protected function casts(): array { return ['metadata' => 'array']; }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Member status history is immutable.'));
        static::deleting(fn () => throw new \LogicException('Member status history is immutable.'));
    }
}

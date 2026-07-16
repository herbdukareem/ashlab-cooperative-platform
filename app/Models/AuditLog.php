<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use BelongsToTenant, HasUlids;

    const UPDATED_AT = null;

    protected $fillable = ['cooperative_id', 'actor_id', 'action', 'subject_type', 'subject_id', 'before', 'after', 'metadata', 'ip_address', 'user_agent'];
    protected function casts(): array { return ['before' => 'array', 'after' => 'array', 'metadata' => 'array']; }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Audit records are immutable.'));
        static::deleting(fn () => throw new \LogicException('Audit records are immutable.'));
    }
}

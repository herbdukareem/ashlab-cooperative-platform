<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use BelongsToTenant, HasUlids;

    protected $fillable = [
        'cooperative_id', 'user_id', 'event_key', 'channel', 'title', 'message',
        'action_url', 'data', 'status', 'scheduled_at', 'sent_at', 'read_at',
        'attempts', 'last_error', 'deduplication_key',
    ];

    protected function casts(): array
    {
        return ['data' => 'array', 'scheduled_at' => 'datetime', 'sent_at' => 'datetime', 'read_at' => 'datetime'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

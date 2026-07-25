<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use BelongsToTenant, HasUlids;

    protected $fillable = ['cooperative_id', 'event_key', 'channel', 'subject', 'body', 'is_active', 'variables'];

    protected function casts(): array { return ['is_active' => 'boolean', 'variables' => 'array']; }
}

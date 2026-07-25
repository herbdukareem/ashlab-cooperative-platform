<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    use BelongsToTenant, HasUlids;

    protected $fillable = ['cooperative_id', 'user_id', 'endpoint', 'public_key', 'auth_token', 'device_name', 'last_used_at'];

    protected $hidden = ['public_key', 'auth_token'];

    protected function casts(): array { return ['last_used_at' => 'datetime']; }
}

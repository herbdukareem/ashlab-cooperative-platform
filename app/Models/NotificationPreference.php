<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use BelongsToTenant, HasUlids;

    protected $fillable = ['cooperative_id', 'user_id', 'in_app_enabled', 'email_enabled', 'sms_enabled', 'push_enabled', 'quiet_hours_start', 'quiet_hours_end'];

    protected function casts(): array
    {
        return ['in_app_enabled' => 'boolean', 'email_enabled' => 'boolean', 'sms_enabled' => 'boolean', 'push_enabled' => 'boolean'];
    }
}

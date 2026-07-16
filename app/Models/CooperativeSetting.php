<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class CooperativeSetting extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'group', 'key', 'value', 'is_encrypted'];
    protected function casts(): array { return ['value' => 'array', 'is_encrypted' => 'boolean']; }
}


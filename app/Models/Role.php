<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use BelongsToTenant, HasAuditTrail, HasUlids;

    protected $fillable = ['cooperative_id', 'name', 'slug', 'description', 'is_system'];
    protected function casts(): array { return ['is_system' => 'boolean']; }
    public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class)->withTimestamps(); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class)->withTimestamps(); }
}


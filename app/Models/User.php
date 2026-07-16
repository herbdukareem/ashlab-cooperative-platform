<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasAuditTrail, HasFactory, HasUlids, Notifiable;

    protected $fillable = [
        'cooperative_id', 'branch_id', 'first_name', 'last_name', 'email',
        'phone', 'password', 'status', 'is_platform_admin', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'is_platform_admin' => 'boolean',
        ];
    }

    public function cooperative(): BelongsTo { return $this->belongsTo(Cooperative::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function roles(): BelongsToMany { return $this->belongsToMany(Role::class)->withTimestamps(); }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_platform_admin) {
            return true;
        }

        return $this->roles()->whereHas('permissions', fn ($query) => $query->where('name', $permission))->exists();
    }
}


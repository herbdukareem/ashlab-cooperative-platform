<?php

namespace App\Models;

use App\Enums\CooperativeStatus;
use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cooperative extends Model
{
    use HasAuditTrail, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'registration_number', 'registration_date', 'type',
        'email', 'phone', 'address', 'state', 'local_government_area',
        'currency', 'financial_year_start_month', 'status', 'logo_path',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'status' => CooperativeStatus::class,
            'financial_year_start_month' => 'integer',
        ];
    }

    public function branches(): HasMany { return $this->hasMany(Branch::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function roles(): HasMany { return $this->hasMany(Role::class); }
    public function settings(): HasMany { return $this->hasMany(CooperativeSetting::class); }
}


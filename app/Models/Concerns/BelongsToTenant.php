<?php

namespace App\Models\Concerns;

use App\Models\Cooperative;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $context = app(TenantContext::class);

            if ($context->hasTenant()) {
                $builder->where($builder->qualifyColumn('cooperative_id'), $context->id());
            }
        });

        static::creating(function ($model): void {
            $context = app(TenantContext::class);

            if ($context->hasTenant()) {
                $model->cooperative_id = $context->id();
            }
        });
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }
}


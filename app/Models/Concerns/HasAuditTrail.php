<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAuditTrail
{
    public static function bootHasAuditTrail(): void
    {
        static::created(fn (Model $model) => self::recordAudit($model, 'created', null, $model->getAttributes()));
        static::updated(fn (Model $model) => self::recordAudit($model, 'updated', $model->getOriginal(), $model->getChanges()));
        static::deleted(fn (Model $model) => self::recordAudit($model, 'deleted', $model->getOriginal(), null));
    }

    private static function recordAudit(Model $model, string $action, ?array $before, ?array $after): void
    {
        $context = app(TenantContext::class);
        $request = app()->runningInConsole() ? null : request();

        $redact = function (?array $values) use ($model): ?array {
            if ($values === null) return null;
            foreach (array_unique([...$model->getHidden(), 'password', 'remember_token', 'token', 'secret']) as $key) {
                if (array_key_exists($key, $values)) $values[$key] = '[REDACTED]';
            }
            return $values;
        };

        AuditLog::query()->create([
            'cooperative_id' => $model->getAttribute('cooperative_id') ?? $context->id(),
            'actor_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $model->getMorphClass(),
            'subject_id' => (string) $model->getKey(),
            'before' => $redact($before),
            'after' => $redact($after),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => ['request_id' => $request?->header('X-Request-ID')],
        ]);
    }
}

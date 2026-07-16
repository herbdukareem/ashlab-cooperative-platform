<?php

namespace App\Http\Middleware;

use App\Enums\CooperativeStatus;
use App\Models\Cooperative;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user, 401, 'Authentication is required.');

        $requestedId = $request->header(config('platform.tenant_header'));
        $tenantId = $user->is_platform_admin ? $requestedId : $user->cooperative_id;

        abort_unless($tenantId, 400, 'A cooperative tenant must be selected.');
        abort_if(! $user->is_platform_admin && $requestedId && $requestedId !== $user->cooperative_id, 403, 'You cannot access another cooperative.');

        $cooperative = Cooperative::query()->findOrFail($tenantId);
        abort_unless($cooperative->status === CooperativeStatus::Active, 423, 'This cooperative is not active.');

        $this->context->set($cooperative);

        try {
            return $next($request);
        } finally {
            $this->context->clear();
        }
    }
}


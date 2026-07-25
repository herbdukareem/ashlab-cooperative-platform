<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Integrations\Contracts\IdentityGateway;
use App\Integrations\Contracts\TransferGateway;
use App\Integrations\Sandbox\SandboxIdentityGateway;
use App\Integrations\Sandbox\SandboxTransferGateway;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Support\Tenancy\TenantContext::class);
        $this->app->bind(TransferGateway::class, function () {
            $this->guardIntegrationMode('transfer');

            return match (config('integrations.transfer.driver')) {
                'sandbox' => new SandboxTransferGateway,
                default => throw new \RuntimeException('Unsupported transfer gateway. Install and configure an approved provider adapter.'),
            };
        });
        $this->app->bind(IdentityGateway::class, function () {
            $this->guardIntegrationMode('identity');

            return match (config('integrations.identity.driver')) {
                'sandbox' => new SandboxIdentityGateway,
                default => throw new \RuntimeException('Unsupported identity gateway. Install and configure an approved provider adapter.'),
            };
        });
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by(mb_strtolower((string) $request->input('email')).'|'.$request->ip()),
        ]);
        RateLimiter::for('webhooks', fn (Request $request) => [
            Limit::perMinute(120)->by($request->ip()),
        ]);
    }

    private function guardIntegrationMode(string $service): void
    {
        if (config('integrations.mode') === 'live' && ! config('integrations.allow_live')) {
            throw new \RuntimeException("Live {$service} integrations are disabled until launch approval is granted.");
        }
    }
}

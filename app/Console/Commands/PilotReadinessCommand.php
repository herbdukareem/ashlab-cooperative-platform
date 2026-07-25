<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PilotReadinessCommand extends Command
{
    protected $signature = 'pilot:readiness {--allow-sandbox : Allow sandbox integrations for staging sign-off}';
    protected $description = 'Verify release-critical configuration and infrastructure before pilot launch';

    public function handle(): int
    {
        $checks = [
            'Application key configured' => fn () => filled(config('app.key')),
            'Debug disabled' => fn () => ! config('app.debug'),
            'HTTPS application URL' => fn () => str_starts_with((string) config('app.url'), 'https://'),
            'Independent identifier hash key' => fn () => filled(config('platform.identifier_hash_key')) && config('platform.identifier_hash_key') !== config('app.key'),
            'MySQL reachable' => fn () => DB::select('select 1') !== [],
            'Cache writable' => function () {
                Cache::put('pilot-readiness', 'ok', 10);
                return Cache::get('pilot-readiness') === 'ok';
            },
            'Storage writable' => function () {
                Storage::put('health/pilot-readiness.txt', now()->toIso8601String());
                Storage::delete('health/pilot-readiness.txt');
                return true;
            },
            'Integration launch gate' => fn () => $this->option('allow-sandbox')
                ? config('integrations.mode') === 'sandbox'
                : config('integrations.mode') === 'live' && config('integrations.allow_live'),
        ];

        $failed = 0;
        foreach ($checks as $label => $check) {
            try {
                $passed = (bool) $check();
            } catch (Throwable $exception) {
                $passed = false;
                $this->line("<error>FAIL</error> {$label}: {$exception->getMessage()}");
                $failed++;
                continue;
            }
            $this->line(($passed ? '<info>PASS</info>' : '<error>FAIL</error>')." {$label}");
            $failed += $passed ? 0 : 1;
        }

        $this->newLine();
        $this->line($failed === 0 ? '<info>Pilot readiness checks passed.</info>' : "<error>{$failed} readiness check(s) failed.</error>");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}

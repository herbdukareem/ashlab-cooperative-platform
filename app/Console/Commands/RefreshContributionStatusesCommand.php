<?php
namespace App\Console\Commands;
use App\Domain\Contributions\Actions\RefreshObligationStatuses;
use App\Models\Cooperative;
use App\Support\Tenancy\TenantContext;
use Illuminate\Console\Command;
class RefreshContributionStatusesCommand extends Command
{
    protected $signature = 'contributions:refresh-statuses';
    protected $description = 'Mark contribution obligations as upcoming, due, partial, or overdue';
    public function handle(RefreshObligationStatuses $action, TenantContext $tenant): int
    {
        $updated = 0; Cooperative::query()->each(function (Cooperative $cooperative) use ($tenant, $action, &$updated): void { $tenant->set($cooperative); $updated += $action->execute(); $tenant->clear(); });
        $this->info("Updated {$updated} contribution obligations."); return self::SUCCESS;
    }
}

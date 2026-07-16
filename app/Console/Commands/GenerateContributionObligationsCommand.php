<?php
namespace App\Console\Commands;
use App\Domain\Contributions\Actions\GenerateContributionObligations;
use App\Models\Cooperative;
use App\Models\MemberContributionPlan;
use App\Support\Tenancy\TenantContext;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
class GenerateContributionObligationsCommand extends Command
{
    protected $signature = 'contributions:generate {--through=}';
    protected $description = 'Generate contribution obligations for every active cooperative enrollment';
    public function handle(GenerateContributionObligations $action, TenantContext $tenant): int
    {
        $through = CarbonImmutable::parse($this->option('through') ?: now()->addMonth()->toDateString()); $created = 0;
        Cooperative::query()->each(function (Cooperative $cooperative) use ($tenant, $action, $through, &$created): void {
            $tenant->set($cooperative);
            MemberContributionPlan::query()->where('status','active')->whereNotNull('next_due_date')->whereDate('next_due_date','<=',$through)->each(function ($enrollment) use ($action, $through, &$created): void { $created += $action->execute($enrollment, $through); });
            $tenant->clear();
        });
        $this->info("Generated {$created} contribution obligations."); return self::SUCCESS;
    }
}

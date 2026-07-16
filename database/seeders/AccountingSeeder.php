<?php
namespace Database\Seeders;
use App\Domain\Accounting\Actions\ProvisionAccounting;use App\Models\Cooperative;use App\Support\Tenancy\TenantContext;use Illuminate\Database\Seeder;
class AccountingSeeder extends Seeder{public function run():void{$tenant=app(TenantContext::class);$action=app(ProvisionAccounting::class);Cooperative::query()->each(function($cooperative)use($tenant,$action){$tenant->set($cooperative);$action->execute($cooperative->financial_year_start_month);$tenant->clear();});}}

<?php
namespace Tests\Feature\Finance;
use App\Domain\Savings\Actions\OpenSavingsAccount;
use App\Domain\Savings\Actions\ProcessSavingsWithdrawal;
use App\Enums\WithdrawalStatus;
use App\Models\Cooperative;
use App\Models\Member;
use App\Models\SavingsProduct;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class SavingsWithdrawalTest extends TestCase
{
    use RefreshDatabase;
    public function test_approval_reserves_funds_and_completion_posts_an_immutable_transaction(): void
    {
        $cooperative = Cooperative::factory()->create(); app(TenantContext::class)->set($cooperative);
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id]); $product = SavingsProduct::factory()->create(['cooperative_id' => $cooperative->id]);
        $account = app(OpenSavingsAccount::class)->execute($member, $product, []); $account->update(['balance_minor' => 200000, 'available_balance_minor' => 200000]);
        $processor = app(ProcessSavingsWithdrawal::class); $withdrawal = $processor->request($member, $account, ['amount_minor' => 50000]);
        $processor->approve($withdrawal); $this->assertSame(150000, $account->refresh()->available_balance_minor);
        $paid = $processor->complete($withdrawal); $this->assertSame(WithdrawalStatus::Paid, $paid->status); $this->assertSame(150000, $account->refresh()->balance_minor); $this->assertDatabaseCount('savings_transactions', 1);
    }
}

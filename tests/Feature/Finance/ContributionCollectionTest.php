<?php
namespace Tests\Feature\Finance;
use App\Domain\Contributions\Actions\EnrollMemberInContributionPlan;
use App\Domain\Payments\Actions\RecordMemberCollection;
use App\Enums\ContributionObligationStatus;
use App\Models\ContributionPlan;
use App\Models\Cooperative;
use App\Models\Member;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ContributionCollectionTest extends TestCase
{
    use RefreshDatabase;
    public function test_collection_is_idempotent_and_pays_oldest_obligation(): void
    {
        $cooperative = Cooperative::factory()->create(); app(TenantContext::class)->set($cooperative);
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id]);
        $plan = ContributionPlan::factory()->create(['cooperative_id' => $cooperative->id, 'fixed_amount_minor' => 100000]);
        $enrollment = app(EnrollMemberInContributionPlan::class)->execute($member, $plan, ['start_date' => today()->toDateString()]);
        $payload = ['idempotency_key' => 'collection-001', 'channel' => 'bank_transfer', 'currency' => 'NGN', 'amount_minor' => 100000, 'contribution_amount_minor' => 100000];
        $first = app(RecordMemberCollection::class)->execute($member, $payload); $second = app(RecordMemberCollection::class)->execute($member, $payload);
        $this->assertTrue($first->is($second)); $this->assertDatabaseCount('payments', 1);
        $this->assertSame(ContributionObligationStatus::Paid, $enrollment->obligations()->first()->refresh()->status);
    }
}

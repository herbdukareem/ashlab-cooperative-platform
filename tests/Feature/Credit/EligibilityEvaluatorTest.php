<?php
namespace Tests\Feature\Credit;
use App\Domain\Credit\Services\EligibilityEvaluator; use App\Models\Cooperative; use App\Models\LoanProduct; use App\Support\Tenancy\TenantContext; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class EligibilityEvaluatorTest extends TestCase
{
    use RefreshDatabase;
    public function test_hard_rule_blocks_ineligible_application():void{$cooperative=Cooperative::factory()->create();app(TenantContext::class)->set($cooperative);$product=LoanProduct::factory()->create(['cooperative_id'=>$cooperative->id]);$product->eligibilityRules()->create(['name'=>'Membership tenure','field'=>'membership_months','operator'=>'gte','comparison_value'=>['value'=>6],'failure_message'=>'Six months membership is required.','is_hard_rule'=>true,'sequence'=>1,'is_active'=>true]);$result=app(EligibilityEvaluator::class)->evaluate($product,['membership_months'=>3]);$this->assertFalse($result['eligible']);$this->assertSame('Six months membership is required.',$result['results'][0]['message']);}
}

<?php
namespace Tests\Feature\Credit;
use App\Domain\Credit\Services\ChargeCalculator; use App\Models\Charge; use App\Models\Cooperative; use App\Models\LoanProduct; use App\Support\Tenancy\TenantContext; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
class ChargeCalculatorTest extends TestCase
{
    use RefreshDatabase;
    public function test_percentage_charge_obeys_minimum_and_category_exemption():void{$cooperative=Cooperative::factory()->create();app(TenantContext::class)->set($cooperative);$product=LoanProduct::factory()->create(['cooperative_id'=>$cooperative->id]);$charge=Charge::factory()->create(['cooperative_id'=>$cooperative->id,'rate_basis_points'=>100,'minimum_amount_minor'=>5000,'exempt_member_category_ids'=>['01EXEMPTCATEGORY000000000000']]);$product->charges()->attach($charge,['sequence'=>1,'is_mandatory'=>true]);$calculator=app(ChargeCalculator::class);$this->assertSame(5000,$calculator->calculate($product,200000)[0]['amount_minor']);$this->assertSame([],$calculator->calculate($product->refresh(),'200000','01EXEMPTCATEGORY000000000000'));}
}

<?php

namespace Tests\Feature\Members;

use App\Enums\ConsentStatus;
use App\Enums\KycStatus;
use App\Models\Cooperative;
use App\Models\Member;
use App\Models\MemberCategory;
use App\Models\MemberGuarantor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithPermissions;
use Tests\TestCase;

class GuarantorRequirementTest extends TestCase
{
    use InteractsWithPermissions, RefreshDatabase;

    public function test_required_guarantor_must_accept_before_member_approval(): void
    {
        $cooperative = Cooperative::factory()->create();
        $category = MemberCategory::factory()->create([
            'cooperative_id' => $cooperative->id,
            'requires_kyc' => true,
            'requires_guarantor' => true,
            'required_guarantors' => 1,
        ]);
        $member = Member::factory()->create([
            'cooperative_id' => $cooperative->id,
            'member_category_id' => $category->id,
            'kyc_status' => KycStatus::Verified,
        ]);
        $guarantorMember = Member::factory()->create(['cooperative_id' => $cooperative->id]);
        $guarantor = MemberGuarantor::query()->create([
            'cooperative_id' => $cooperative->id,
            'member_id' => $member->id,
            'guarantor_member_id' => $guarantorMember->id,
            'relationship' => 'Colleague',
            'consent_status' => ConsentStatus::Pending,
        ]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $this->actingAsCooperativeUser($user, ['members.approve']);

        $this->patchJson("/api/v1/members/{$member->id}/status", ['status' => 'active'])->assertUnprocessable();
        $guarantor->update(['consent_status' => ConsentStatus::Accepted, 'consented_at' => now()]);
        $this->patchJson("/api/v1/members/{$member->id}/status", ['status' => 'active'])->assertOk();
    }
}


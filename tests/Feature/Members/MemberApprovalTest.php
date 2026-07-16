<?php

namespace Tests\Feature\Members;

use App\Enums\KycStatus;
use App\Models\Cooperative;
use App\Models\Member;
use App\Models\MemberCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithPermissions;
use Tests\TestCase;

class MemberApprovalTest extends TestCase
{
    use InteractsWithPermissions, RefreshDatabase;

    public function test_category_kyc_requirement_blocks_approval_until_verified(): void
    {
        $cooperative = Cooperative::factory()->create();
        $category = MemberCategory::factory()->create(['cooperative_id' => $cooperative->id, 'requires_kyc' => true]);
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id, 'member_category_id' => $category->id]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $this->actingAsCooperativeUser($user, ['members.approve']);

        $this->patchJson("/api/v1/members/{$member->id}/status", ['status' => 'active'])->assertUnprocessable();
        $member->update(['kyc_status' => KycStatus::Verified]);
        $this->patchJson("/api/v1/members/{$member->id}/status", ['status' => 'active'])->assertOk()->assertJsonPath('data.status', 'active');
        $this->assertNotNull($member->fresh()->approved_at);
    }
}


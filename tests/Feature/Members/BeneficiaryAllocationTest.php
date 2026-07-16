<?php

namespace Tests\Feature\Members;

use App\Models\Cooperative;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithPermissions;
use Tests\TestCase;

class BeneficiaryAllocationTest extends TestCase
{
    use InteractsWithPermissions, RefreshDatabase;

    public function test_total_beneficiary_allocation_cannot_exceed_one_hundred_percent(): void
    {
        $cooperative = Cooperative::factory()->create();
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $this->actingAsCooperativeUser($user, ['members.beneficiaries.manage']);
        $base = ['relationship' => 'Child', 'is_minor' => false];

        $this->postJson("/api/v1/members/{$member->id}/beneficiaries", [...$base, 'full_name' => 'First Beneficiary', 'entitlement_percentage' => 60])->assertCreated();
        $this->postJson("/api/v1/members/{$member->id}/beneficiaries", [...$base, 'full_name' => 'Second Beneficiary', 'entitlement_percentage' => 50])
            ->assertUnprocessable()->assertJsonValidationErrors('entitlement_percentage');
    }
}


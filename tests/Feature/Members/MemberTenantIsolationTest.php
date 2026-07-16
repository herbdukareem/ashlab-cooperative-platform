<?php

namespace Tests\Feature\Members;

use App\Models\Cooperative;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithPermissions;
use Tests\TestCase;

class MemberTenantIsolationTest extends TestCase
{
    use InteractsWithPermissions, RefreshDatabase;

    public function test_member_from_another_cooperative_is_not_route_bindable(): void
    {
        $cooperativeA = Cooperative::factory()->create();
        $cooperativeB = Cooperative::factory()->create();
        $user = User::factory()->create(['cooperative_id' => $cooperativeA->id]);
        $foreignMember = Member::factory()->create(['cooperative_id' => $cooperativeB->id]);
        $this->actingAsCooperativeUser($user, ['members.view']);

        $this->getJson('/api/v1/members/'.$foreignMember->id)->assertNotFound();
    }
}


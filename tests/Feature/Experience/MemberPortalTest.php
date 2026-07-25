<?php

namespace Tests\Feature\Experience;

use App\Models\Cooperative;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_only_open_linked_profile(): void
    {
        $cooperative = Cooperative::factory()->create();
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id]);
        $other = Member::factory()->create(['cooperative_id' => $cooperative->id]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id, 'member_id' => $member->id]);

        $this->actingAs($user)->withHeader('X-Cooperative-ID', $cooperative->id)
            ->getJson('/api/v1/portal/profile')
            ->assertOk()->assertJsonPath('id', $member->id)->assertJsonMissing(['id' => $other->id]);
    }

    public function test_unlinked_account_cannot_use_member_portal(): void
    {
        $cooperative = Cooperative::factory()->create();
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);

        $this->actingAs($user)->withHeader('X-Cooperative-ID', $cooperative->id)
            ->getJson('/api/v1/portal/dashboard')->assertForbidden();
    }
}

<?php

namespace Tests\Feature\Members;

use App\Models\Branch;
use App\Models\Cooperative;
use App\Models\MemberCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithPermissions;
use Tests\TestCase;

class MemberRegistrationTest extends TestCase
{
    use InteractsWithPermissions, RefreshDatabase;

    public function test_member_registration_generates_sequential_tenant_number_and_history(): void
    {
        $cooperative = Cooperative::factory()->create();
        $branch = Branch::factory()->create(['cooperative_id' => $cooperative->id]);
        $category = MemberCategory::factory()->create(['cooperative_id' => $cooperative->id]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id, 'branch_id' => $branch->id]);
        $this->actingAsCooperativeUser($user, ['members.create']);

        $payload = [
            'branch_id' => $branch->id, 'member_category_id' => $category->id,
            'first_name' => 'Zainab', 'last_name' => 'Musa', 'gender' => 'female',
            'phone' => '08012345678', 'email' => 'zainab@example.test', 'date_joined' => today()->toDateString(),
        ];

        $first = $this->postJson('/api/v1/members', $payload)->assertCreated()->json('data');
        $payload['phone'] = '08012345679'; $payload['email'] = 'zainab2@example.test';
        $second = $this->postJson('/api/v1/members', $payload)->assertCreated()->json('data');

        $this->assertSame('MBR-'.now()->year.'-000001', $first['membership_number']);
        $this->assertSame('MBR-'.now()->year.'-000002', $second['membership_number']);
        $this->assertSame('pending', $first['status']);
        $this->assertDatabaseHas('member_status_histories', ['member_id' => $first['id'], 'to_status' => 'pending']);
    }
}


<?php

namespace Tests\Feature\Members;

use App\Models\Cooperative;
use App\Models\Member;
use App\Models\MemberCategory;
use App\Models\MemberIdentification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithPermissions;
use Tests\TestCase;

class KycProtectionTest extends TestCase
{
    use InteractsWithPermissions, RefreshDatabase;

    public function test_identifier_is_encrypted_masked_and_duplicate_is_rejected(): void
    {
        $cooperative = Cooperative::factory()->create();
        $category = MemberCategory::factory()->create(['cooperative_id' => $cooperative->id]);
        $memberA = Member::factory()->create(['cooperative_id' => $cooperative->id, 'member_category_id' => $category->id]);
        $memberB = Member::factory()->create(['cooperative_id' => $cooperative->id, 'member_category_id' => $category->id]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $this->actingAsCooperativeUser($user, ['kyc.manage', 'kyc.verify']);

        $response = $this->postJson("/api/v1/members/{$memberA->id}/identifications", ['type' => 'nin', 'identifier' => '12345678901'])
            ->assertCreated()->assertJsonPath('data.masked_identifier', '******8901');

        $record = MemberIdentification::query()->findOrFail($response->json('data.id'));
        $this->assertNotSame('12345678901', $record->getRawOriginal('identifier_encrypted'));
        $this->assertStringNotContainsString('12345678901', json_encode($response->json()));

        $this->postJson("/api/v1/members/{$memberB->id}/identifications", ['type' => 'nin', 'identifier' => '1234 567 8901'])
            ->assertUnprocessable()->assertJsonValidationErrors('identifier');
    }

    public function test_verifying_only_identifier_completes_basic_kyc(): void
    {
        $cooperative = Cooperative::factory()->create();
        $category = MemberCategory::factory()->create(['cooperative_id' => $cooperative->id]);
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id, 'member_category_id' => $category->id]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $this->actingAsCooperativeUser($user, ['kyc.manage', 'kyc.verify']);
        $id = $this->postJson("/api/v1/members/{$member->id}/identifications", ['type' => 'nin', 'identifier' => '12345678901'])->json('data.id');

        $this->patchJson("/api/v1/members/{$member->id}/identifications/{$id}/verify", ['status' => 'verified'])->assertOk();
        $this->assertSame('verified', $member->fresh()->kyc_status->value);

        $this->postJson("/api/v1/members/{$member->id}/identifications", ['type' => 'international_passport', 'identifier' => 'A12345678'])->assertCreated();
        $this->assertSame('pending', $member->fresh()->kyc_status->value);
    }
}

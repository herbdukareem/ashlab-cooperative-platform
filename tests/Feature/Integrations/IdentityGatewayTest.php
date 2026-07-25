<?php

namespace Tests\Feature\Integrations;

use App\Domain\Members\Actions\StoreMemberIdentification;
use App\Domain\Members\Actions\VerifyMemberIdentification;
use App\Enums\VerificationStatus;
use App\Models\Cooperative;
use App\Models\Member;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentityGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_sandbox_verification_accepts_well_formed_nin_without_exposing_it(): void
    {
        $cooperative = Cooperative::factory()->create();
        app(TenantContext::class)->set($cooperative);
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id]);
        $identification = app(StoreMemberIdentification::class)->execute($member, [
            'type' => 'nin',
            'identifier' => '12345678901',
        ]);

        $verified = app(VerifyMemberIdentification::class)->execute($identification);

        $this->assertSame(VerificationStatus::Verified, $verified->verification_status);
        $this->assertStringStartsWith('sandbox_', $verified->metadata['provider_reference']);
        $this->assertArrayNotHasKey('identifier', $verified->metadata);
    }
}

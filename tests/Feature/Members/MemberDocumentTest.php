<?php

namespace Tests\Feature\Members;

use App\Models\Cooperative;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\InteractsWithPermissions;
use Tests\TestCase;

class MemberDocumentTest extends TestCase
{
    use InteractsWithPermissions, RefreshDatabase;

    public function test_document_is_stored_privately_and_path_is_not_exposed(): void
    {
        Storage::fake('local');
        $cooperative = Cooperative::factory()->create();
        $member = Member::factory()->create(['cooperative_id' => $cooperative->id]);
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $this->actingAsCooperativeUser($user, ['kyc.manage']);

        $response = $this->postJson("/api/v1/members/{$member->id}/documents", [
            'type' => 'nin_slip', 'file' => UploadedFile::fake()->image('nin.jpg'),
        ])->assertCreated();

        $this->assertArrayNotHasKey('path', $response->json('data'));
        $document = $member->documents()->firstOrFail();
        Storage::disk('local')->assertExists($document->path);
    }
}


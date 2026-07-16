<?php

namespace Tests\Feature\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditRedactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_hashes_are_redacted_from_audit_records(): void
    {
        $user = User::factory()->create(['password' => 'VerySecurePassword123!']);

        $audit = AuditLog::query()
            ->where('subject_type', $user->getMorphClass())
            ->where('subject_id', $user->id)
            ->where('action', 'created')
            ->firstOrFail();

        $this->assertSame('[REDACTED]', $audit->after['password']);
        $this->assertStringNotContainsString($user->password, json_encode($audit->after));
    }
}


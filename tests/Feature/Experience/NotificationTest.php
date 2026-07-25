<?php

namespace Tests\Feature\Experience;

use App\Domain\Notifications\Actions\SendNotification;
use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_read_another_users_notification(): void
    {
        $cooperative = Cooperative::factory()->create();
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $other = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $notification = app(SendNotification::class)->execute($other, 'loan.approved', 'Loan approved', 'Your loan was approved.');

        $this->actingAs($user)->withHeader('X-Cooperative-ID', $cooperative->id)
            ->patchJson("/api/v1/notifications/{$notification->id}/read")->assertNotFound();
    }

    public function test_notification_deduplication_is_idempotent(): void
    {
        $cooperative = Cooperative::factory()->create();
        $user = User::factory()->create(['cooperative_id' => $cooperative->id]);
        $action = app(SendNotification::class);
        $action->execute($user, 'payment.received', 'Payment received', 'Thank you.', ['deduplication_key' => 'payment:123']);
        $action->execute($user, 'payment.received', 'Payment received', 'Thank you.', ['deduplication_key' => 'payment:123']);

        $this->assertDatabaseCount('notifications', 1);
    }
}

<?php

namespace App\Domain\Notifications\Actions;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;

class SendNotification
{
    public function execute(User $user, string $eventKey, string $title, string $message, array $options = []): ?Notification
    {
        $preference = NotificationPreference::query()->where('user_id', $user->id)->first();

        if ($preference && ! $preference->in_app_enabled) {
            return null;
        }

        $attributes = [
            'user_id' => $user->id,
            'event_key' => $eventKey,
            'channel' => 'in_app',
            'title' => $title,
            'message' => $message,
            'action_url' => $options['action_url'] ?? null,
            'data' => $options['data'] ?? null,
            'status' => 'sent',
            'scheduled_at' => $options['scheduled_at'] ?? now(),
            'sent_at' => now(),
        ];

        if (! isset($options['deduplication_key'])) {
            return Notification::query()->create(['cooperative_id' => $user->cooperative_id, ...$attributes]);
        }

        return Notification::query()->firstOrCreate(
            [
                'cooperative_id' => $user->cooperative_id,
                'deduplication_key' => $options['deduplication_key'],
            ],
            $attributes,
        );
    }
}

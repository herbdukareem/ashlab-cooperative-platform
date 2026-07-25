<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()->where('user_id', $request->user()->id)
            ->when($request->boolean('unread'), fn ($query) => $query->whereNull('read_at'))
            ->latest()->paginate(min($request->integer('per_page', 20), 100));

        return response()->json($notifications);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json(['count' => Notification::query()->where('user_id', $request->user()->id)->whereNull('read_at')->count()]);
    }

    public function read(Request $request, Notification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 404);
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return response()->json($notification);
    }

    public function readAll(Request $request): JsonResponse
    {
        Notification::query()->where('user_id', $request->user()->id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function preferences(Request $request): JsonResponse
    {
        $preference = NotificationPreference::query()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['cooperative_id' => $request->user()->cooperative_id],
        );

        return response()->json($preference);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'in_app_enabled' => ['sometimes', 'boolean'], 'email_enabled' => ['sometimes', 'boolean'],
            'sms_enabled' => ['sometimes', 'boolean'], 'push_enabled' => ['sometimes', 'boolean'],
            'quiet_hours_start' => ['nullable', 'date_format:H:i'], 'quiet_hours_end' => ['nullable', 'date_format:H:i'],
        ]);
        $preference = NotificationPreference::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['cooperative_id' => $request->user()->cooperative_id, ...$data],
        );

        return response()->json($preference);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:2048'], 'public_key' => ['required', 'string', 'max:512'],
            'auth_token' => ['required', 'string', 'max:512'], 'device_name' => ['nullable', 'string', 'max:120'],
        ]);
        $subscription = PushSubscription::query()->updateOrCreate(
            ['user_id' => $request->user()->id, 'endpoint' => $data['endpoint']],
            ['cooperative_id' => $request->user()->cooperative_id, ...$data, 'last_used_at' => now()],
        );

        return response()->json($subscription, 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(NotificationTemplate::query()->orderBy('event_key')->paginate(50));
    }

    public function store(Request $request): JsonResponse
    {
        $template = NotificationTemplate::query()->create($this->validated($request));

        return response()->json($template, 201);
    }

    public function update(Request $request, NotificationTemplate $notificationTemplate): JsonResponse
    {
        $notificationTemplate->update($this->validated($request, true));

        return response()->json($notificationTemplate);
    }

    public function destroy(NotificationTemplate $notificationTemplate): JsonResponse
    {
        $notificationTemplate->delete();

        return response()->json(status: 204);
    }

    private function validated(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'event_key' => [$required, 'string', 'max:100'],
            'channel' => [$required, Rule::in(['in_app', 'email', 'sms', 'push'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => [$required, 'string', 'max:10000'],
            'is_active' => ['sometimes', 'boolean'],
            'variables' => ['nullable', 'array'],
        ]);
    }
}

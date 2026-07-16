<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __invoke(Request $request): mixed
    {
        $filters = $request->validate([
            'action' => ['nullable', 'string', 'max:80'],
            'actor_id' => ['nullable', 'string'],
            'subject_type' => ['nullable', 'string'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = AuditLog::query()
            ->when($filters['action'] ?? null, fn ($q, $value) => $q->where('action', $value))
            ->when($filters['actor_id'] ?? null, fn ($q, $value) => $q->where('actor_id', $value))
            ->when($filters['subject_type'] ?? null, fn ($q, $value) => $q->where('subject_type', $value))
            ->when($filters['from'] ?? null, fn ($q, $value) => $q->whereDate('created_at', '>=', $value))
            ->when($filters['to'] ?? null, fn ($q, $value) => $q->whereDate('created_at', '<=', $value))
            ->latest()->paginate();

        return response()->json($logs);
    }
}


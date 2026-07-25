<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthController extends Controller
{
    public function live(): JsonResponse
    {
        return response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]);
    }

    public function ready(): JsonResponse
    {
        $checks = ['database' => false, 'redis' => false];

        try { DB::select('SELECT 1'); $checks['database'] = true; } catch (Throwable) {}
        try { Redis::ping(); $checks['redis'] = true; } catch (Throwable) {}

        $ready = ! in_array(false, $checks, true);

        return response()->json(['status' => $ready ? 'ready' : 'unavailable', 'checks' => $checks], $ready ? 200 : 503);
    }
}

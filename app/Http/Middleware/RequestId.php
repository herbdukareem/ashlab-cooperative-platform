<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->headers->get('X-Request-ID', (string) Str::uuid());
        Context::add('request_id', $id);
        $response = $next($request);
        $response->headers->set('X-Request-ID', $id);

        return $response;
    }
}

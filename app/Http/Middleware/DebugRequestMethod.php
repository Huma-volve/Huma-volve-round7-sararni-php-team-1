<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DebugRequestMethod
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log request details for debugging
        if (str_contains($request->path(), 'register')) {
            Log::info('Register Request Debug', [
                'path' => $request->path(),
                'method' => $request->method(),
                'real_method' => $request->server('REQUEST_METHOD'),
                'http_method' => $request->header('X-HTTP-Method-Override'),
                'all_headers' => $request->headers->all(),
                'server_method' => $_SERVER['REQUEST_METHOD'] ?? 'NOT_SET',
                'input_method' => $request->input('_method'),
                'url' => $request->fullUrl(),
            ]);
        }

        return $next($request);
    }
}

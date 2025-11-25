<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class HandleCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        $allowedOrigin = $this->getAllowedOrigin($request);

        // Handle preflight requests (OPTIONS)
        // This is critical for CORS - must return 200 OK, not redirect
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 200);

            // Set CORS headers
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-XSRF-TOKEN');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');

            return $response;
        }

        $response = $next($request);

        // Add CORS headers to all responses
        // Handle both Illuminate\Http\Response and Symfony Response
        if (method_exists($response, 'header')) {
            return $response
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-XSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Vary', 'Origin');
        }

        // For Symfony Response, set headers directly
        if ($response instanceof BaseResponse) {
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-XSRF-TOKEN');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Vary', 'Origin');
        }

        return $response;
    }

    /**
     * Get allowed origin for CORS
     *
     * By default, allows all origins. If CORS_ALLOWED_ORIGINS is set,
     * only those origins will be allowed.
     */
    protected function getAllowedOrigin(Request $request): string
    {
        $origin = $request->header('Origin');
        $corsAllowed = env('CORS_ALLOWED_ORIGINS', '');

        // Default: Allow all origins (return the requesting origin)
        // Note: We return the specific origin instead of '*' because
        // we use 'Access-Control-Allow-Credentials: true'
        if (empty($corsAllowed) || $corsAllowed === '*') {
            // If origin is provided, return it (required when credentials: true)
            // Otherwise return '*' (for non-credential requests)
            return $origin ?: '*';
        }

        // Parse allowed origins (support comma-separated list)
        $allowedOrigins = array_map('trim', explode(',', $corsAllowed));

        // If * is in the list, allow all origins
        if (in_array('*', $allowedOrigins)) {
            return $origin ?: '*';
        }

        // Check if the origin is in the allowed list
        if ($origin && in_array($origin, $allowedOrigins)) {
            return $origin;
        }

        // If origin is not in allowed list but we have allowed origins,
        // return the first one (or * if list is empty)
        // This prevents CORS errors when origin header is missing
        return $allowedOrigins[0] ?? ($origin ?: '*');
    }
}

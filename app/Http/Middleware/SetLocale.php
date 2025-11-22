<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supportedLocales = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        // Check for locale in query parameter
        $locale = $request->query('locale');

        // If not in query, check Accept-Language header
        if (! $locale) {
            $locale = $request->getPreferredLanguage($this->supportedLocales);
        }

        // If still not found, check authenticated user's preference
        if (! $locale && $request->user()) {
            $locale = $request->user()->language ?? null;
        }

        // Fallback to default locale
        $locale = $locale && in_array($locale, $this->supportedLocales) ? $locale : config('app.locale', 'en');

        App::setLocale($locale);

        return $next($request);
    }
}

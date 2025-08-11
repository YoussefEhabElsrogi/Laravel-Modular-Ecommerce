<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the locale from the query string or the default locale
        $local = $request->query('lang', 'en');

        // If the locale is not valid, set it to the default locale
        if (!in_array($local, ['ar', 'en'])) {
            $local = 'en';
        }

        // Set the locale
        app()->setLocale($local);

        return $next($request);
    }
}

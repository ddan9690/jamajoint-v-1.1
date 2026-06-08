<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Define the cache key
            $key = 'user-is-online-' . Auth::id();
            
            // Mark user as online for 5 minutes
            Cache::put($key, true, now()->addMinutes(5));
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to profile routes for all users
        if (Str::startsWith($request->path(), 'profile')) {
            return $next($request);
        }

        if ($request->user() && Str::contains(Str::lower($request->user()->email), 'admin')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !Str::contains(Str::lower($request->user()->email), 'admin')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
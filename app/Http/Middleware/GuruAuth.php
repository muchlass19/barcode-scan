<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class GuruAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('guru')->check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}

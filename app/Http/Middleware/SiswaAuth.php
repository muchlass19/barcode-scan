<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SiswaAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('siswa')->check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}

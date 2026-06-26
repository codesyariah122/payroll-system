<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $allowed = explode('|', $roles);

        if (! in_array(Auth::user()->role, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}

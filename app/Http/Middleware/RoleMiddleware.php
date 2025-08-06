<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Bisa dipakai seperti:
     *   ->middleware('role:regu_mekanik')
     *   ->middleware('role:regu_mekanik,koordinator_mekanik')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $userRole = Auth::user()->role;

        // cek apakah role user ada di daftar roles yang diijinkan
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

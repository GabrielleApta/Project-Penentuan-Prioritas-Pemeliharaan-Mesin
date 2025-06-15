<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReadOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'user') {
            // Hanya izinkan akses ke metode "index" dan "show" (lihat data)
            if (!in_array($request->route()->getActionMethod(), ['index', 'show'])) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk melakukan aksi ini.');
            }
        }

        return $next($request);
    }
}

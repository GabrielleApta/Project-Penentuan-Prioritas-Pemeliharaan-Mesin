<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan file ini ada di resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Cek peran (role) dan redirect sesuai role
            if ($user->role === 'regu_mekanik') {
                return redirect()->route('regu_mekanik.dashboard')->with('success', 'Login berhasil sebagai Regu Mekanik!');
            } elseif ($user->role === 'koordinator_mekanik') {
                return redirect()->route('koordinator_mekanik.dashboard')->with('success', 'Login berhasil sebagai Koordinator Mekanik!');
            }

            return redirect()->route('login')->withErrors(['email' => 'Role tidak valid!']);
        }

        return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}

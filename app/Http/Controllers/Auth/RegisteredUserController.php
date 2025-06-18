<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan form registrasi untuk user biasa.
     */
    public function create()
    {
        return view('auth.register'); // Pastikan ada file auth/register.blade.php
    }

    /**
     * Proses registrasi untuk user biasa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default user biasa
        ]);


    }

    /**
     * Menampilkan form registrasi untuk admin.
     */
    public function createAdmin()
    {
        return view('auth.register-admin'); // Pastikan ada file auth/register-admin.blade.php
    }

    /**
     * Proses registrasi untuk admin.
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Admin
        ]);

        
    }
}

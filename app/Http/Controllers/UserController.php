<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user' // default role
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Anda telah login.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function profile()
    {
        return view('profile');
    }

    public function index()
    {
        $users = User::all();
        return view('pages.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', Rule::in(['koordinator_mekanik', 'regu_mekanik'])],
            'password' => 'nullable|string|min:6'
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        if ($user->role === 'koordinator_mekanik') {
            return redirect()->route('users.index')->with('error', 'Tidak bisa menghapus sesama Koordinator Mekanik!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

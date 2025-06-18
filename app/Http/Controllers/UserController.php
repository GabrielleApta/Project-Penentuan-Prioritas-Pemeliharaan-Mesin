<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Menampilkan form registrasi
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Menyimpan data registrasi ke database
    public function register(Request $request)
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
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Anda telah login.');
    }

    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses autentikasi login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function profile()
{
    return view('profile');
}

 // Tampilkan daftar user
    public function index()
{
    $users = User::all(); // tampilkan semua, termasuk akun yang sedang login
    return view('pages.users.index', compact('users'));
}



    // Tampilkan form edit role
    public function edit(User $user)
    {
        return view('pages.users.edit', compact('user'));
    }

    // Update role user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Role pengguna berhasil diperbarui.');
    }

    // Hapus user
    public function destroy(User $user)
{
    // Cegah admin menghapus dirinya sendiri atau sesama admin
    if ($user->id === auth()->id()) {
        return redirect()->route('users.index')->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
    }

    if ($user->role === 'admin') {
        return redirect()->route('users.index')->with('error', 'Tidak bisa menghapus sesama admin!');
    }

    $user->delete();
    return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
}

}

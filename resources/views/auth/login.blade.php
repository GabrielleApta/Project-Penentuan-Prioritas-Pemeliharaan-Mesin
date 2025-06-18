@extends('layouts.auth')

@section('auth-title', 'Selamat Datang Kembali!')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required autofocus placeholder="Masukkan email">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Kata Sandi</label>
        <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan kata sandi">
    </div>

    <button type="submit" class="btn btn-primary w-100">Login</button>

    

    <div class="text-center mt-2">
        <span>Belum punya akun? <a href="{{ route('register') }}" class="text-primary">Daftar</a></span>
    </div>
</form>
@endsection

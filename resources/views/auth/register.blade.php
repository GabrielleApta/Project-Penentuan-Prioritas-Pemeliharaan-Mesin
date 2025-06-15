@extends('layouts.auth')

@section('title', 'Register')

@section('auth-title', 'Register')

@section('content')
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn auth-button">Register</button>
    </form>
    <div class="mt-3">
        <span class="small-text">Sudah punya akun? <a href="{{ route('login') }}">Login</a></span>
    </div>
@endsection

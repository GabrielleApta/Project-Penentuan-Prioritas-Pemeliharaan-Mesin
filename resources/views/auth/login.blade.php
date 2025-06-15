@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 d-flex">
            <!-- Form Login -->
            <div class="login-container">
                <h3 class="login-header">Login</h3>
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn login-button w-100">Login</button>
                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}" class="text-primary">Forgot your password?</a>
                    </div>
                    <div class="text-center mt-2">
                        <span>Belum punya akun? <a href="{{ route('register') }}" class="text-primary">Register</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

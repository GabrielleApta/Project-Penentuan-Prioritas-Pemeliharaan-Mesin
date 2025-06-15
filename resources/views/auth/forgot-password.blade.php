@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('auth-title', 'Forgot Password')

@section('content')
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <button type="submit" class="btn auth-button">Send Password Reset Link</button>
    </form>
    <div class="mt-3">
        <a href="{{ route('login') }}" class="small-text">Back to Login</a>
    </div>
@endsection

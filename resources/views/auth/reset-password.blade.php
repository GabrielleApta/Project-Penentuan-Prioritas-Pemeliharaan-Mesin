@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-blue-200">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Reset Password</h2>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded mt-1" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Send Reset Link</button>
        </form>

        <p class="text-center text-sm mt-4">
            <a href="{{ route('login') }}" class="text-blue-600">Back to Login</a>
        </p>
    </div>
</div>
@endsection

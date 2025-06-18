@extends('layouts.app')

@section('title', 'Edit Role User')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Role: {{ $user->name }}</h1>

    @if($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST" class="mt-3">
        @csrf
        @method('PATCH')

        <div class="form-group mb-3">
            <label for="role">Pilih Role:</label>
            <select name="role" id="role" class="form-select">
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

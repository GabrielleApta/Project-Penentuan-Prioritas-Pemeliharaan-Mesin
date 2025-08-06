@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Pengguna: {{ $user->name }}</h1>

    @if($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="mt-3">
        @csrf
        @method('PATCH')

        <div class="form-group mb-3">
            <label for="name">Nama:</label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control"
                value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="password">Password Baru (Opsional):</label>
            <input type="password" name="password" id="password" class="form-control"
                placeholder="Biarkan kosong jika tidak ingin mengubah password">
        </div>

        <div class="form-group mb-3">
            <label for="role">Role:</label>
            <select name="role" id="role" class="form-select">
                <option value="koordinator_mekanik" {{ $user->role === 'koordinator_mekanik' ? 'selected' : '' }}>Koordinator Mekanik</option>
                <option value="regu_mekanik" {{ $user->role === 'regu_mekanik' ? 'selected' : '' }}>Regu Mekanik</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

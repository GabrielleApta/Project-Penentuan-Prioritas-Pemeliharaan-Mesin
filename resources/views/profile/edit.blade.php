@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container mt-4">
    <h4>Edit Profil</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="photo" class="form-label">Foto Profil</label><br>
            @if ($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto" width="100" class="mb-2 rounded-circle">
            @else
                <p class="text-muted">Belum ada foto</p>
            @endif
            <input type="file" class="form-control" name="photo">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection

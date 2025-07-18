@extends('layouts.app')

@section('title', 'Riwayat Perhitungan Straight Line')

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-4">Riwayat Perhitungan Straight Line</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Riwayat Perhitungan</li>
    </ol>

    <div class="mb-3">
        <a href="#" class="btn btn-sm btn-success disabled">Cetak Semua Perhitungan</a>
    </div>

    <div class="card mb-4">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>Kode Perhitungan</th>
                        <th>Tanggal Generate</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                    <tr class="text-center align-middle">
                        <td>{{ $item->kode_perhitungan }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_generate)->format('d-m-Y H:i') }}</td>
                        <td>{{ optional($item->user)->name ?? 'Unknown' }}</td>
                        <td>
                            <a href="{{ route('riwayat-straight-line.show', $item->kode_perhitungan) }}" class="btn btn-sm btn-primary">Detail</a>
                            <form action="{{ route('riwayat-straight-line.destroy', $item->kode_perhitungan) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada riwayat perhitungan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

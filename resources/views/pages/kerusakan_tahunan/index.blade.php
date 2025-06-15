@extends('layouts.app')

@section('title', 'Data Kerusakan Tahunan')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Data Kerusakan Tahunan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('kerusakan-tahunan.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
                <!-- Tombol Import Data -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import"></i> Import Data
                </button>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Tahun</th>
                            <th>Kerusakan Ringan</th>
                            <th>Downtime Ringan (jam)</th>
                            <th>Kerusakan Parah</th>
                            <th>Downtime Parah (jam)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->mesin->nama_mesin }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>{{ $item->kerusakan_ringan }}</td>
                                <td>{{ $item->downtime_ringan }} jam</td>
                                <td>{{ $item->kerusakan_parah }}</td>
                                <td>{{ $item->downtime_parah }} jam</td>
                                <td>
                                    <a href="{{ route('kerusakan-tahunan.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('kerusakan-tahunan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Import Data --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formImport" method="POST" action="{{ route('kerusakan.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_mesin" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" name="file" id="file_mesin" accept=".xlsx, .xls, .csv" required>
                    </div>
                    <small class="text-muted">Format yang didukung: .xls, .xlsx, .csv</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('import_success'))
<script>
    Swal.fire('Sukses!', '{{ session("import_success") }}', 'success');
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal Import',
        html: `{!! implode('<br>', $errors->all()) !!}`
    });
</script>
@endif
@endpush

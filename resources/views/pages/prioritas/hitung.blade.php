@extends('layouts.app')

@section('title', 'Hasil Perhitungan SAW')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Hasil Perhitungan SAW</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-3">Normalisasi Nilai</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nama Mesin</th>
                            @foreach ($kriteria as $k)
                                <th>{{ ucwords(str_replace('_', ' ', $k->nama_kriteria)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mesin as $m)
                            <tr>
                                <td>{{ $m->nama_mesin }}</td>
                                @foreach ($kriteria as $k)
                                    <td>{{ number_format($normalisasi[$m->id][$k->nama_kriteria] ?? 0, 4) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h4 class="mt-5">Hasil Akhir Perhitungan</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Rangking</th>
                            <th>Nama Mesin</th>
                            <th>Skor Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rank = 1; @endphp
                        @foreach ($skorAkhir as $mesin_id => $skor)
                            <tr>
                                <td>{{ $rank++ }}</td>
                                <td>{{ $mesin->find($mesin_id)?->nama_mesin ?? 'Tidak ditemukan' }}</td>
                                <td>{{ number_format($skor, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Hasil Perhitungan SAW')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Hasil Perhitungan SAW</h1>
    <div class="card mb-4">
        <div class="card-body">
            <h4>Normalisasi</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Mesin</th>
                        @foreach ($kriteria as $k)
                            <th>{{ $k->nama_kriteria }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mesin as $m)
                        <tr>
                            <td>{{ $m->nama_mesin }}</td>
                            @foreach ($kriteria as $k)
                                <td>{{ number_format($normalisasi[$m->id][$k->nama_kriteria], 3) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4>Hasil Akhir</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Nama Mesin</th>
                        <th>Skor Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rank = 1; @endphp
                    @foreach ($skorAkhir as $mesin_id => $skor)
                        <tr>
                            <td>{{ $rank++ }}</td>
                            <td>{{ $mesin->find($mesin_id)->nama_mesin }}</td>
                            <td>{{ number_format($skor, 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

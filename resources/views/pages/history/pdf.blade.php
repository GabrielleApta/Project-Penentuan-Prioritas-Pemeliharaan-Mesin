<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Histori Pemeliharaan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 30px;
        }

        .kop-table {
            width: 100%;
            border: none;
            margin-bottom: 5px;
        }

        .kop-table td {
            border: none;
            vertical-align: middle;
        }

        .kop-table img {
            height: 80px;
        }

        .kop-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .kop-address {
            font-size: 10pt;
            margin: 2px 0;
        }

        hr.double-line {
            border-top: 2px solid black;
            border-bottom: 4px double black;
            margin: 5px 0 10px 0;
        }

        h2, h4 {
            text-align: center;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #444;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td {
            padding: 4px;
        }

        .summary {
            margin-top: 25px;
            font-size: 12px;
            line-height: 1.6;
            text-align: justify;
        }

        .ttd-container {
            border: none;
            margin-top: 50px;
            width: 100%;
            font-size: 9pt;
        }

        .ttd-container td {
            border: none;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td style="width: 20%;">
                <img src="{{ public_path('images/logo_arida.png') }}" alt="Logo ARIDA">
            </td>
            <td style="width: 80%; text-align: center; padding-right: 80px;">
                <h1 class="kop-title">PT. ARTERIA DAYA MULIA</h1>
                <p class="kop-address">Jalan Dukuh Duwur No. 46, Telp. (0231) 206507 Fax. (0231) 206478 - 206842</p>
                <p class="kop-address">Cirebon 45113 - JAWA BARAT - INDONESIA</p>
            </td>
        </tr>
    </table>

    <hr class="double-line">

    {{-- JUDUL --}}
    <h2>Laporan Histori Pemeliharaan Mesin</h2>
    <br>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mesin</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Deskripsi</th>
                <th>Durasi (jam)</th>
                <th>Teknisi</th>
                <th>Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $i => $h)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td>{{ $h->mesin->nama_mesin ?? '-' }}</td>
                    <td style="text-align:center">{{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d-m-Y') }}</td>
                    <td style="text-align:center">{{ ucfirst($h->jenis_pemeliharaan) }}</td>
                    <td>{{ $h->deskripsi }}</td>
                    <td style="text-align:center">{{ number_format($h->durasi_jam, 0, ',', '.') }}</td>
                    <td style="text-align:center">{{ $h->teknisi }}</td>
                    <td style="text-align:center">
                        {{ $h->verifikasi ? 'Terverifikasi' : 'Belum' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center">Tidak ada data histori pemeliharaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- RINGKASAN --}}
    @if((request('tahun') || request('mesin_id')) && $histories->count() > 0)
        <div class="summary">
            @php
                $total = $histories->count();
                $totalJam = number_format($histories->sum('durasi_jam'), 0, ',', '.');
                $mesinNama = request('mesin_id') && $histories->first()
                    ? $histories->first()->mesin->nama_mesin
                    : 'semua mesin';
                $periode = $tahun ?? 'semua periode';

                $topMesin = $histories
                    ->groupBy('mesin_id')
                    ->map->count()
                    ->sortDesc()
                    ->keys()
                    ->first();
                $topMesinNama = $topMesin ? \App\Models\Mesin::find($topMesin)->nama_mesin : null;
            @endphp

            <p>
                Laporan ini menampilkan histori pemeliharaan
                <strong>{{ $mesinNama }}</strong>
                pada <strong>{{ $periode }}</strong>.
                Selama periode tersebut tercatat sebanyak <strong>{{ $total }}</strong> aktivitas pemeliharaan
                dengan total durasi pengerjaan <strong>{{ $totalJam }} jam</strong>. Data ini mencakup informasi tanggal, jenis pemeliharaan, teknisi pelaksana,
                serta status verifikasi dari koordinator mekanik.
            </p>

            @if($topMesinNama)
                <p>
                    <strong>Rekomendasi:</strong> Pertahankan pola pemeliharaan ini agar kinerja mesin tetap optimal.
                    Selain itu, perhatian khusus disarankan untuk mesin
                    <strong>{{ $topMesinNama }}</strong> karena berdasarkan data, mesin ini memiliki jumlah aktivitas
                    pemeliharaan paling tinggi dibanding mesin lainnya. Hal ini menunjukkan bahwa mesin tersebut
                    lebih sering mengalami gangguan dan perlu dilakukan evaluasi kondisi menyeluruh, penjadwalan preventive
                    yang lebih ketat, atau bahkan perbaikan besar jika diperlukan.
                </p>
            @endif
        </div>
    @endif

    {{-- TANDA TANGAN --}}
    <table class="ttd-container">
        <tr>
            <td style="width: 50%;">
                <br>
                <strong>Regu Mekanik</strong><br><br><br><br>
                <div style="height: 60px;"></div>
                <u><strong>(...................................)</strong></u>
            </td>
            <td style="width: 50%;">
                Cirebon, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                <strong>Koordinator Mekanik</strong><br><br><br><br>
                <div style="height: 60px;"></div>
                <u><strong>(...................................)</strong></u>
            </td>
        </tr>
    </table>

    {{-- NOMOR HALAMAN --}}
    @if (isset($pdf))
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $size = 10;
            $pdf->page_text(500, 820, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, $size, [0, 0, 0]);
        }
    </script>
    @endif

</body>
</html>

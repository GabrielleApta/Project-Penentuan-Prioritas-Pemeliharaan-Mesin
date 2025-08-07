<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kerusakan Mesin</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
            margin: 30px;
        }

        /* KOP SURAT */
        .kop-table {
            width: 100%;
            border: none;
            margin-bottom: 10px;
        }

        .kop-table td {
            border: none;
            vertical-align: middle; /* Ini penting */
        }

        .kop-table img {
            height: 80px;
        }

        .kop-title {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            margin-bottom: 10px;
        }

        .kop-address {
            font-size: 11pt;
            margin: 2px 0;
            margin-bottom: 10px;
        }

        hr.double-line {
            border-top: 2px solid black;
            border-bottom: 4px double black;
            margin: 5px 0 15px 0;
        }

        /* JUDUL */
        .judul-laporan {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        /* TABEL */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12pt;
        }

        th, td {
            border: 1px solid black;
            padding: 6px 4px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-start {
            text-align: center;
        }

        /* TANDA TANGAN */
        .ttd-container {
    margin-top: 60px;
    width: 100%;
    display: flex;
    justify-content: space-between;
    font-size: 11pt; /* sedikit lebih kecil */
}

.ttd {
    width: 35%; /* dari 40% → 35% */
    text-align: center;
}

        .date {
            font-size: 11pt;
            text-align: left;
            margin-top: 25px;
        }
    </style>
</head>
<body>

    {{-- KOP --}}
    <table class="kop-table">
        <tr>
            <td style="width: 20%; text-align: left; vertical-align: middle;">
                <img src="{{ public_path('images/logo_arida.png') }}" alt="Logo ARIDA">
            </td>
            <td style="width: 80%; text-align: center; padding-right: 100px;">
                <h1 class="kop-title">PT. ARTERIA DAYA MULIA</h1>
                <p class="kop-address">Jalan Dukuh Duwur No. 46, Telp. (0231) 206507 Fax. (0231) 206478 - 206842</p>
                <p class="kop-address">Cirebon 45113 - JAWA BARAT - INDONESIA</p>
            </td>
        </tr>
    </table>

    <hr class="double-line">

    {{-- JUDUL --}}
    <div class="judul-laporan">
        LAPORAN DATA KERUSAKAN MESIN
        @if (request('tahun'))
            TAHUN {{ request('tahun') }}
        @endif
        @if (request('mesin_id') && $data->count() && $data->first()->mesin)
            - {{ strtoupper($data->first()->mesin->nama_mesin) }}
        @endif
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mesin</th>
                <th>Tahun</th>
                <th>Kerusakan Ringan</th>
                <th>Downtime Ringan (jam)</th>
                <th>Kerusakan Parah</th>
                <th>Downtime Parah (jam)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-start">{{ $item->mesin->nama_mesin }}</td>
                    <td>{{ $item->tahun }}</td>
                    <td>{{ $item->kerusakan_ringan }}</td>
                    <td>{{ $item->downtime_ringan }}</td>
                    <td>{{ $item->kerusakan_parah }}</td>
                    <td>{{ $item->downtime_parah }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <table style="width: 100%; margin-top: 60px; font-size: 11pt; text-align: center; border: none;">
    <tr>
        <!-- Regu Mekanik -->
            <td style="width: 50%; border: none;">
            &nbsp;<br>
            <strong>Regu Mekanik</strong><br><br><br><br>
            {{-- Area tanda tangan --}}
            <div style="height: 60px;"></div>
            <u><strong>(...................................)</strong></u><br>
        </td>

        <!-- Koordinator Mekanik -->
        <td style="width: 50%; margin-bottom: 35px; border: none;">
            Cirebon, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            <strong>Koordinator Mekanik</strong><br><br><br><br>
            {{-- Area tanda tangan --}}
            <div style="height: 60px;"></div>
            <u><strong>(...................................)</strong></u><br>
        </td>
    </tr>
</table>


    {{-- NOMOR HALAMAN --}}
    @if (isset($pdf))
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Times-Roman", "normal");
            $size = 10;
            $pdf->page_text(500, 820, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, $size, [0, 0, 0]);
        }
    </script>
    @endif

</body>
</html>

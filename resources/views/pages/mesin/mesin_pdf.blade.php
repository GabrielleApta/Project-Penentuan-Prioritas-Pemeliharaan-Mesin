<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Mesin</title>
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
            margin-bottom: 15px;
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

    <div class="judul-laporan">
        LAPORAN DATA MESIN PRODUKSI BENANG DI PT. ARTERIA DAYA MULIA<br>
        TAHUN 2025
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mesin</th>
                <th>Kode Mesin</th>
                <th>Tahun Pembelian</th>
                <th>Spesifikasi Mesin</th>
                <th>Daya Motor</th>
                <th>Lokasi Mesin</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDayaMotor = 0; @endphp
            @foreach ($mesin as $index => $m)
                @php $totalDayaMotor += $m->daya_motor; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $m->nama_mesin }}</td>
                    <td>{{ $m->kode_mesin }}</td>
                    <td>{{ $m->tahun_pembelian }}</td>
                    <td>{{ $m->spesifikasi_mesin }}</td>
                    <td>{{ (fmod($m->daya_motor, 1) != 0) ? str_replace('.', ',', rtrim($m->daya_motor, '0')) : (int) $m->daya_motor }}</td>
                    <td>{{ $m->lokasi_mesin }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Jumlah</td>
                <td>{{ (fmod($totalDayaMotor, 1) != 0) ? str_replace('.', ',', rtrim($totalDayaMotor, '0')) : (int) $totalDayaMotor }}</td>
                <td></td>
            </tr>
        </tfoot>
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

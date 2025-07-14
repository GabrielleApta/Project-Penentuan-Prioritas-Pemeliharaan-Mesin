<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Perhitungan SAW</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
            margin: 30px;
        }

        .kop-table {
            width: 100%;
            border: none;
            margin-bottom: 10px;
        }

        .kop-table td {
            border: none;
            vertical-align: middle;
        }

        .kop-table img {
            height: 70px;
        }

        .kop-title {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
             margin-bottom: 8px;
        }

        .kop-address {
            font-size: 11pt;
            margin: 0;
            margin-bottom: 8px;
        }

        hr.double-line {
            border-top: 2px solid black;
            border-bottom: 4px double black;
            margin: 8px 0 15px 0;
        }

        .judul-laporan {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

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

        .ttd-container {
            margin-top: 60px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            font-size: 11pt;
        }

        .ttd {
            width: 35%;
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
        <td style="width: 15%; text-align: center;">
            <img src="{{ public_path('images/logo_arida.png') }}" alt="Logo ARIDA">
        </td>
        <td style="width: 85%; text-align: center;">
            <h1 class="kop-title">PT. ARTERIA DAYA MULIA</h1>
            <p class="kop-address">Jalan Dukuh Duwur No. 46, Telp. (0231) 206507, <br>Fax. (0231) 206478 - 206842</p>
            <p class="kop-address">Cirebon 45113 - JAWA BARAT - INDONESIA</p>
        </td>
    </tr>
</table>

<hr class="double-line">

    <div class="judul-laporan">
    LAPORAN PERHITUNGAN SAW
</div>

    <table>
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Nama Mesin</th>
                <th>Skor Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hasil_saw as $data)
            <tr>
                <td>{{ $data->rangking }}</td>
                <td>{{ $data->mesin->nama_mesin }}</td>
                <td>{{ number_format($data->skor_akhir, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{-- TANDA TANGAN --}}
<table style="width: 100%; margin-top: 60px; font-size: 11pt; text-align: center; border: none;">
    <tr>
        <td style="width: 50%; border: none;">
            <strong>Regu Mekanik</strong><br><br><br><br>
            <u><strong>(...................................)</strong></u><br>
        </td>
        <td style="width: 50%; border: none;">
            Cirebon, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            <strong>Koordinator Mekanik</strong><br><br><br><br>
            <u><strong>(...................................)</strong></u><br>
        </td>
    </tr>
</table>

</body>
</html>

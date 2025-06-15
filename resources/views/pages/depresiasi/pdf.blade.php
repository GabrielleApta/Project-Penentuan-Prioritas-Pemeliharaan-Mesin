<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Depresiasi Mesin</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header, .footer {
            text-align: center;
            width: 100%;
            position: fixed;
        }
        .header {
            top: -10px;
        }
        .footer {
            bottom: 0px;
            font-size: 10px;
            color: #888;
        }
        .logo {
            float: left;
            width: 80px;
        }
        .kop {
            text-align: center;
            margin-left: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 120px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .ttd {
            margin-top: 40px;
            width: 100%;
            text-align: right;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <img src="{{ public_path('images/logo_arida.png') }}" class="logo">
        <div class="kop">
            <h4 style="text-align: center;">LAPORAN DEPRESIASI MESIN (METODE STRAIGHT LINE)</h4>
        </div>
        <hr>
    </div>



    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mesin</th>
                <th>Kode Mesin</th>
                <th>Harga Beli</th>
                <th>Nilai Sisa</th>
                <th>Umur Ekonomis</th>
                <th>Depresiasi Tahunan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mesins as $index => $m)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $m->nama_mesin }}</td>
                <td>{{ $m->kode_mesin }}</td>
                <td>Rp {{ number_format($m->harga_beli, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($m->nilai_sisa, 0, ',', '.') }}</td>
                <td>{{ $m->umur_ekonomis }} tahun</td>
                <td>Rp {{ number_format($m->depresiasi_tahunan ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <div class="ttd">
        <p>Cirebon, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br>
        <p><strong>Koordinator Mekanik</strong></p>
        <br><br>
        <p><u>______________________</u></p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Laporan ini dicetak secara otomatis melalui sistem | {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>

</body>
</html>

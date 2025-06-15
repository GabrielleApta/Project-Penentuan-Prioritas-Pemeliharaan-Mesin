<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mesin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; } /* Judul di tengah */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; } /* Isi tabel rata tengah */
        th { background-color: #f2f2f2; }
        .signature { margin-top: 50px; text-align: center; }
        .signature div { display: inline-block; width: 40%; text-align: center; }
        .underline { margin-top: 80px; border-top: 1px solid black; width: 70%; display: inline-block; }
        .date { text-align: left; margin-top: 20px; font-size: 14px; } /* Tanggal cetak di kiri */
    </style>
</head>
<body>
    <h2>DATA SPESIFIKASI MESIN BAGIAN BENANG</h2>

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
                <td colspan="5" style="text-align: center;"><strong>Jumlah</strong></td>
                <td><strong>{{ (fmod($totalDayaMotor, 1) != 0) ? str_replace('.', ',', rtrim($totalDayaMotor, '0')) : (int) $totalDayaMotor }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align: left; font-weight: bold;">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
    </p>

    <div class="signature">
        <div>

            <p class="underline"></p>
            <p><strong>(Nama 1)</strong></p>
            <p>Assman Benang</p>
        </div>
        <div>
            <p>Mengetahui,</p>
            <p class="underline"></p>
            <p><strong>(Nama 2)</strong></p>
            <p>Pjs Manager Benang</p>
        </div>
    </div>
</body>
</html>

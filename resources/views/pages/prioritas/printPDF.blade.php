<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan SAW</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 align="center">Hasil Perhitungan SAW</h2>
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

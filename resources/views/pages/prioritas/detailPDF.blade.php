<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail SAW - {{ $mesin->nama_mesin }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Detail Perhitungan SAW</h3>
    <p><strong>Nama Mesin:</strong> {{ $mesin->nama_mesin }}</p>

    <h4>Data Penilaian</h4>
    <table>
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Nilai</th>
                <th>Bobot</th>
                <th>Normalisasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penilaian as $p)
                <tr>
                    <td>{{ $p->nama_kriteria }}</td>
                    <td>{{ rtrim(rtrim(number_format($p->nilai, 4), '0'), '.') }}</td>
                    <td>{{ rtrim(rtrim(number_format($p->bobot, 4), '0'), '.') }}</td>
                    <td>{{ rtrim(rtrim(number_format($normalisasi[$p->kriteria_id] ?? 0, 4), '0'), '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Skor Akhir: {{ number_format($skor_akhir, 4) }}</h4>
</body>
</html>

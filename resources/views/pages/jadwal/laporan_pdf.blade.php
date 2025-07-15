<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Pemeliharaan</title>
    <<style>
    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 13px;
        line-height: 1.5;
        margin: 20px;
    }

    /* KOP SURAT */
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
            height: 80px;
        }
        .kop-title {
            font-size: 18pt;
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
            margin: 5px 0 15px 0;
        }
    p {
        margin: 5px 0;
    }

    h2, h3 {
        text-align: center;
        margin: 5px 0;
    }

    .cover {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .executive-summary {
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        border: 1px solid #000;
        padding: 6px 4px;
        text-align: center;
    }
    th {
        background-color: #f0f0f0;
    }
    .text-left {
        text-align: left;
    }
    .mt-2 { margin-top: 10px; }
    .mb-2 { margin-bottom: 10px; }

    .signature {
        margin-top: 50px;
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .high { background-color: #f8d7da; }   /* Merah muda untuk Tinggi */
    .medium { background-color: #fff3cd; } /* Kuning muda untuk Sedang */
    .low { background-color: #d4edda; }    /* Hijau muda untuk Rendah */

</style>
</head>
<body>

{{-- KOP --}}
    <table class="kop-table">
        <tr>
            <td style="width: 20%; text-align: left;">
                <img src="{{ public_path('images/logo_arida.png') }}" alt="Logo ARIDA">
            </td>
            <td style="width: 80%; text-align: center;">
                <h1 class="kop-title">PT. ARTERIA DAYA MULIA</h1>
                <p class="kop-address">Jalan Dukuh Duwur No. 46, Telp. (0231) 206507 Fax. (0231) 206478 - 206842</p>
                <p class="kop-address">Cirebon 45113 - JAWA BARAT - INDONESIA</p>
            </td>
        </tr>
    </table>
    <hr class="double-line">

    {{-- JUDUL --}}
    <h2>LAPORAN JADWAL PEMELIHARAAN MESIN</h2>
    <p style="text-align: center;">Periode: <strong>{{ $periode }}</strong></p>



{{-- 2. RINGKASAN EKSEKUTIF --}}
<div class="executive-summary">
    <p>
        Laporan ini berisi jadwal pemeliharaan untuk <strong>{{ count($jadwals) }}</strong> mesin produksi benang
        pada periode <strong>{{ $periode }}</strong>. Evaluasi dilakukan dengan metode SAW
        berdasarkan akumulasi penyusutan, usia mesin, frekuensi kerusakan, dan waktu downtime.
    </p>

    <p>
        Dari hasil evaluasi, terdapat:
    </p>

    <ul>
        <li>{{ $statistik['tinggi'] }} mesin dengan prioritas <strong>tinggi</strong> – perlu tindakan segera</li>
        <li>{{ $statistik['sedang'] }} mesin dengan prioritas <strong>sedang</strong> – direncanakan minggu kedua/ketiga</li>
        <li>{{ $statistik['rendah'] }} mesin dengan prioritas <strong>rendah</strong> – monitoring lanjutan</li>
    </ul>
</div>

{{-- 3. TABEL UTAMA --}}
<h3>Tabel Jadwal Pemeliharaan Mesin</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Mesin</th>
            <th>Lokasi</th>
            <th>Tanggal Jadwal</th>
            <th>Prioritas</th>
            <th>Status</th>
            <th>Petugas</th>
            <th>Rencana Tindakan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jadwals as $i => $j)
        @php
                $rowClass = '';
                if ($j->prioritas === 'tinggi') $rowClass = 'high';
                elseif ($j->prioritas === 'sedang') $rowClass = 'medium';
                elseif ($j->prioritas === 'rendah') $rowClass = 'low';
            @endphp
        <tr class="{{ $rowClass }}">
            <td>{{ $i + 1 }}</td>
            <td class="left">{{ $j->mesin->nama_mesin }}</td>
            <td>{{ $j->mesin->lokasi_mesin }}</td>
            <td>{{ \Carbon\Carbon::parse($j->tanggal_jadwal)->translatedFormat('d F Y') }}</td>
            <td>{{ ucfirst($j->prioritas) }}</td>
            <td>{{ ucfirst($j->status) }}</td>
            <td>-</td>
            <td>{{ $j->catatan ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


<h3 class="mt-2">Ringkasan Statistik</h3>
<div style="display: flex; justify-content: space-between; align-items: flex-start;">
    <!-- Tabel di Kiri -->
    <table style="width: 50%; margin-bottom: 15px;">
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Mesin</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Prioritas Tinggi</td><td>{{ $statistik['tinggi'] ?? 0 }}</td></tr>
            <tr><td>Prioritas Sedang</td><td>{{ $statistik['sedang'] ?? 0 }}</td></tr>
            <tr><td>Prioritas Rendah</td><td>{{ $statistik['rendah'] ?? 0 }}</td></tr>
        </tbody>
    </table>

    <!-- Pie Chart di Kanan -->
    @if($chartImage)
    <div style="width: 50%; text-align: center;">
        <img src="data:image/png;base64,{{ $chartImage }}" alt="Pie Chart" style="width: 100%; max-width: 250px;">
    </div>
    @endif
</div>

{{-- 5. CATATAN --}}


{{-- 6. TANDA TANGAN --}}
<div class="signature">
    <table style="width: 100%; font-size: 11pt; text-align: center; border: none;">
            <tr>
                <td style="width: 50%; border: none;">
                    <br><br><strong>Regu Mekanik</strong><br><br><br><br>
                    <u><strong>(...................................)</strong></u><br>
                </td>
                <td style="width: 50%; border: none;">
                    Cirebon, {{ $tanggalCetak }}<br>
                    <strong>Koordinator Mekanik</strong><br><br><br><br>
                    <u><strong>(...................................)</strong></u><br>
                </td>
            </tr>
        </table>
</div>

</body>
</html>

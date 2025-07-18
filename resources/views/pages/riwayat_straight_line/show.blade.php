@extends('layouts.app')

@section('title', 'Detail Riwayat Perhitungan')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Riwayat Perhitungan</h2>
        <p class="text-sm text-gray-500">Kode Perhitungan: <span class="font-semibold">{{ $kode }}</span></p>
        <p class="text-sm text-gray-500">Tanggal Generate: <span class="font-semibold">{{ \Carbon\Carbon::parse($tanggalGenerate)->format('d-m-Y H:i') }}</span></p>
        <p class="text-sm text-gray-500">Dibuat Oleh: <span class="font-semibold">{{ $dibuatOleh }}</span></p>
    </div>

    <div class="overflow-x-auto mt-4">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700 font-semibold text-left">
                <tr>
                    <th class="px-4 py-2 border">No</th>
                    <th class="px-4 py-2 border">Nama Mesin</th>
                    <th class="px-4 py-2 border">Harga Beli</th>
                    <th class="px-4 py-2 border">Umur Ekonomis</th>
                    <th class="px-4 py-2 border">Nilai Sisa</th>
                    <th class="px-4 py-2 border">Penyusutan per Tahun</th>
                    <th class="px-4 py-2 border">Akumulasi Penyusutan</th>
                    <th class="px-4 py-2 border">Nilai Buku</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($riwayat as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                    <td class="px-4 py-2 border">{{ $item->nama_mesin ?? '-' }}</td>
<td class="px-4 py-2 border">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
<td class="px-4 py-2 border">{{ $item->umur_ekonomis }} tahun</td>
<td class="px-4 py-2 border">Rp {{ number_format($item->nilai_sisa, 0, ',', '.') }}</td>
<td class="px-4 py-2 border">Rp {{ number_format($item->penyusutan, 0, ',', '.') }}</td>
<td class="px-4 py-2 border">Rp {{ number_format($item->akumulasi_penyusutan, 0, ',', '.') }}</td>
<td class="px-4 py-2 border">Rp {{ number_format($item->nilai_buku, 0, ',', '.') }}</td>

                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center px-4 py-4 border text-gray-500">Tidak ada data perhitungan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('riwayat-straight-line.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm text-gray-700 rounded-md">
            ← Kembali ke Riwayat
        </a>
    </div>
</div>
@endsection

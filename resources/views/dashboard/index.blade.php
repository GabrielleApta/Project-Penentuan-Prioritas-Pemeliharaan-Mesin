
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <!-- Card Statistik Mesin -->
    <div class="row">
        @php
            $cards = [
                ['color' => 'primary', 'label' => 'Total Mesin', 'value' => $totalMesin, 'route' => 'mesin.index'],
                ['color' => 'success', 'label' => 'Mesin Samjin', 'value' => $mesinSamjin, 'route' => 'mesin.samjin'],
                ['color' => 'info', 'label' => 'Mesin Twisting', 'value' => $mesinTwisting, 'route' => 'mesin.twisting'],
                ['color' => 'danger', 'label' => 'Mesin Tidak Aktif', 'value' => $mesinTidakAktif, 'route' => 'mesin.tidakaktif'],
                ['color' => 'warning', 'label' => 'Mesin Aktif', 'value' => $mesinAktif, 'route' => 'mesin.aktif']
            ];
        @endphp

        @foreach ($cards as $card)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-{{ $card['color'] }} text-white shadow">
                <div class="card-body">{{ $card['label'] }}: {{ $card['value'] }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route($card['route']) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Grafik Depresiasi dan SAW Berdampingan -->
    <div class="row">
        <!-- Grafik Depresiasi -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line"></i> Nilai Buku Mesin per Tahun
                    </h6>
                    <select id="filterTahun" class="form-control form-control-sm w-auto">
                        @foreach ($listTahun as $th)
                            <option value="{{ $th }}">{{ $th }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        Menampilkan 5 mesin dengan nilai buku tertinggi untuk tahun tertentu.
                    </p>
                    <canvas id="depresiasiChart" height="230"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik SAW -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-bar"></i> 5 Mesin Prioritas Berdasarkan SAW
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        Skor hasil akhir dari perhitungan metode SAW.
                    </p>
                    <canvas id="hasilSawChart" height="230"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Data dari Controller
    const tahunData = @json($nilaiBukuPerTahun ?? []);
    const tahunSelect = document.getElementById("filterTahun");
    const ctx = document.getElementById("depresiasiChart").getContext("2d");

    let depresiasiChart;

    function renderChart(tahun) {
        const datasets = tahunData[tahun] || [];

        if (depresiasiChart) depresiasiChart.destroy();

        depresiasiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: datasets.map(ds => ds.label),
                datasets: [{
                    label: `Nilai Buku Mesin - ${tahun}`,
                    data: datasets.map(ds => ds.data[0]),
                    backgroundColor: datasets.map(ds => ds.backgroundColor),
                    borderColor: datasets.map(ds => ds.borderColor),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: `Top 5 Nilai Buku Mesin Tahun ${tahun}`
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => `Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                        }
                    },
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai Buku (Rp)'
                        },
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    }

    // Inisialisasi grafik pertama
    renderChart(tahunSelect.value);

    // Saat dropdown berubah
    tahunSelect.addEventListener('change', function () {
        renderChart(this.value);
    });

    // Grafik SAW (tidak berubah)
    const ctxSaw = document.getElementById("hasilSawChart").getContext("2d");
    const labelsSaw = @json($labelsSaw ?? []);
    const dataSaw = @json($dataSaw ?? []);

    new Chart(ctxSaw, {
        type: 'bar',
        data: {
            labels: labelsSaw,
            datasets: [{
                label: 'Skor Akhir SAW',
                data: dataSaw.map(Number),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ranking Mesin Berdasarkan Skor SAW'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `Skor SAW: ${ctx.parsed.x.toFixed(2)}`
                    }
                },
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: { display: true, text: 'Skor SAW' }
                }
            }
        }
    });
});
</script>
@endsection

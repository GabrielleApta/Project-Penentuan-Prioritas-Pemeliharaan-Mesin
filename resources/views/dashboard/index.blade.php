@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <!-- Statistik Mesin -->
    <div class="row">
        @php
            $cards = [
                ['color' => 'primary', 'label' => 'Total Mesin', 'value' => $totalMesin, 'route' => 'mesin.index'],
                ['color' => 'dark', 'label' => 'Total Kerusakan Ringan 2024', 'value' => $frekuensiRingan, 'route' => 'kerusakan-tahunan.index'],
                ['color' => 'secondary', 'label' => 'Total Kerusakan Parah 2024', 'value' => $frekuensiParah, 'route' => 'kerusakan-tahunan.index'],
                ['color' => 'info', 'label' => 'Total Rekapan Histori 2024', 'value' => $totalPemeliharaan, 'route' => 'kerusakan-tahunan.index'],
            ];
        @endphp

        @foreach ($cards as $card)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-{{ $card['color'] }} text-white shadow h-100 d-flex flex-column">
                <div class="card-body">{{ $card['label'] }}: {{ $card['value'] }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between mt-auto">
                    <a class="small text-white stretched-link" href="{{ route($card['route']) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Grafik Depresiasi & SAW -->
    <div class="row">
        <!-- Grafik Penyusutan -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line"></i> Top 5 Mesin – Nilai Buku (Line Chart)
                    </h6>
                    <form id="filterForm" class="d-flex align-items-center">
                        <label for="tahunFilter" class="mb-0 mr-2 text-dark">Tahun: </label>
                        <select id="tahunFilter" class="form-control form-control-sm w-auto">
                            @foreach ($listTahun as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <canvas id="lineChartDepresiasi" height="230"></canvas>
                    <p class="text-muted small mb-2">Menampilkan nilai buku mesin dengan penyusutan tertinggi dari tahun terpilih ke sekarang.</p>
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
                    <canvas id="hasilSawChart" height="230"></canvas>
                    <p class="mt-2 text-muted small">Skor SAW (Simple Additive Weighting) dihitung dari beberapa kriteria seperti usia mesin, frekuensi kerusakan, downtime, dan nilai ekonomis. Semakin tinggi skor, semakin tinggi prioritas pemeliharaan mesin.</p>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const tahunSelect = document.getElementById("tahunFilter");
    const ctxLine = document.getElementById("lineChartDepresiasi").getContext("2d");
    let lineChart;

    function fetchLineChartData(tahun) {
        fetch(`/dashboard/top-depresiasi?tahun_awal=${tahun}`)
            .then(response => response.json())
            .then(data => {
                if (lineChart) lineChart.destroy();

                lineChart = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: data.labelsTahun,
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Top 5 Mesin dengan Penyusutan Tertinggi'
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const val = ctx.raw;
                                        return `${ctx.dataset.label}: Rp ${val?.toLocaleString('id-ID')}`;
                                    }
                                }
                            },
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: { boxWidth: 15 }
                            }
                        },
                        scales: {
                            x: {
                                title: { display: true, text: 'Tahun' }
                            },
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Nilai Buku (Rp)' },
                                ticks: {
                                    callback: value => 'Rp ' + value.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            });
    }

    // Trigger saat select berubah
    tahunSelect.addEventListener("change", () => {
        const tahun = tahunSelect.value;
        fetchLineChartData(tahun);
    });

    // Initial load
    fetchLineChartData(tahunSelect.value);

    // ========= SAW CHART =========
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
                backgroundColor: dataSaw.map((val, idx) =>
    idx === 0 ? 'rgba(255, 99, 132, 0.6)' : 'rgba(75, 192, 192, 0.5)'
),
borderColor: dataSaw.map((val, idx) =>
    idx === 0 ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)'
),

                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'x',
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ranking Mesin Berdasarkan Skor SAW'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `Skor SAW: ${ctx.parsed.x.toFixed(3)}`
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

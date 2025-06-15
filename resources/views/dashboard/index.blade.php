@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <!-- Card Statistik Mesin -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">Total Mesin: {{ $totalMesin }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mesin.index') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">Mesin Samjin: {{ $mesinSamjin }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mesin.samjin') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">Mesin Twisting: {{ $mesinTwisting }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mesin.twisting') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">Mesin Tidak Aktif: {{ $mesinTidakAktif }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mesin.tidakaktif') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mesin Aktif -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">Mesin Aktif: {{ $mesinAktif }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mesin.aktif') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Depresiasi -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line"></i> Grafik Nilai Buku Mesin per Tahun</h6>
                </div>
                <div class="card-body">
                    <canvas id="depresiasiChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik SAW -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-chart-bar"></i> 5 Mesin Prioritas Berdasarkan SAW</h6>
                </div>
                <div class="card-body">
                    <canvas id="hasilSawChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Grafik Depresiasi
    const ctx = document.getElementById("depresiasiChart").getContext("2d");
    const tahun = @json($tahun ?? []);
    const datasets = @json($nilaiBuku ?? []);
    const colors = [
        'rgba(255, 99, 132, 0.5)',
        'rgba(54, 162, 235, 0.5)',
        'rgba(255, 206, 86, 0.5)',
        'rgba(75, 192, 192, 0.5)',
        'rgba(153, 102, 255, 0.5)',
        'rgba(255, 159, 64, 0.5)'
    ];

    if (tahun.length && datasets.length) {
        const lineDatasets = datasets.map((dataset, index) => ({
            label: dataset.label,
            data: dataset.data.map(Number),
            fill: false,
            tension: 0.2,
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length],
            pointBorderWidth: 2,
            pointRadius: 3
        }));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: tahun.map(String),
                datasets: lineDatasets
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Perbandingan Nilai Buku Mesin per Tahun'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tahun'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nilai Buku (Rp)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Grafik SAW
    const ctxSaw = document.getElementById("hasilSawChart").getContext("2d");
    const labelsSaw = @json($labelsSaw ?? []);
    const dataSaw = @json($dataSaw ?? []);

    if (labelsSaw.length && dataSaw.length) {
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
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Ranking Mesin Berdasarkan Skor SAW'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: { display: true, text: 'Skor SAW' }
                    }
                }
            }
        });
    }
});
</script>
@endsection
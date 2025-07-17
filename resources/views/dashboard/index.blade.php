@extends('layouts.app')

@section('content')
<div class="container-fluid">
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

    <!-- Grafik Bubble Depresiasi dan SAW -->
    <div class="row">
        <!-- Grafik Bubble Depresiasi -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bubble"></i> Grafik Bubble Penyusutan Mesin
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        Visualisasi nilai buku mesin berdasarkan tahun dan penyusutan tahunan.
                    </p>
                    <canvas id="bubbleChartDepresiasi" height="230"></canvas>
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
    // ========= BUBBLE CHART =========
    const bubbleCtx = document.getElementById("bubbleChartDepresiasi").getContext("2d");
    const bubbleRawData = @json($grafikBubbleDepresiasi);

    const bubbleDatasets = bubbleRawData.map(row => ({
        label: row.label,
        data: [{
            x: row.x,
            y: row.y,
            r: row.r
        }],
        backgroundColor: row.backgroundColor,
        borderColor: row.backgroundColor,
        borderWidth: 1
    }));

    new Chart(bubbleCtx, {
        type: 'bubble',
        data: {
            datasets: bubbleDatasets
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Bubble Chart - Nilai Buku dan Penyusutan Mesin'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const x = ctx.raw.x;
                            const y = ctx.raw.y.toLocaleString('id-ID');
                            const r = (ctx.raw.r * 1000000).toLocaleString('id-ID');
                            return `${ctx.dataset.label}\nTahun: ${x}, Nilai Buku: Rp ${y}, Penyusutan: Rp ${r}`;
                        }
                    }
                },
                legend: { display: false }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Tahun' },
                    ticks: { stepSize: 1 },
                    beginAtZero: false
                },
                y: {
                    title: { display: true, text: 'Nilai Buku (Rp)' },
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Rp ' + value.toLocaleString('id-ID')
                    }
                }
            }
        }
    });

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

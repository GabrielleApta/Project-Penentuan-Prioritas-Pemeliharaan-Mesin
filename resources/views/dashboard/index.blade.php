@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="mt-4">Dashboard</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>

<!-- Statistics Cards Row -->
<div class="row">
    @php
        $cards = [
            ['color' => 'primary', 'icon' => 'fas fa-industry', 'label' => 'Total Mesin', 'value' => $totalMesin ?? '12', 'route' => 'mesin.index'],
            ['color' => 'warning', 'icon' => 'fas fa-tools', 'label' => 'Kerusakan Ringan 2024', 'value' => $frekuensiRingan ?? '25', 'route' => 'kerusakan-tahunan.index'],
            ['color' => 'danger', 'icon' => 'fas fa-exclamation-triangle', 'label' => 'Kerusakan Parah 2024', 'value' => $frekuensiParah ?? '8', 'route' => 'kerusakan-tahunan.index'],
            ['color' => 'info', 'icon' => 'fas fa-clipboard-list', 'label' => 'Total Histori 2024', 'value' => $totalPemeliharaan ?? '45', 'route' => 'kerusakan-tahunan.index'],
        ];
    @endphp

    @foreach ($cards as $card)
    <div class="col-xl-3 col-md-6">
        <div class="card bg-{{ $card['color'] }} text-white mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="{{ $card['icon'] }} fa-2x me-3"></i>
                    <div>
                        <div class="fs-4 fw-bold">{{ $card['value'] }}</div>
                        <div class="small">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="{{ route($card['route']) }}">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Alert untuk maintenance reminder -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-bell me-2"></i>
            <strong>Reminder:</strong> Ada {{ $maintenanceDue ?? '3' }} mesin yang perlu maintenance dalam 7 hari ke depan.
            <a href="{{ route('jadwal.index') }}" class="alert-link">Lihat jadwal</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Grafik Penyusutan Line Chart -->
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-chart-line me-1"></i>
                    Top 5 Mesin – Nilai Buku (Trend Analysis)
                </div>
                <div class="d-flex align-items-center">
                    <label for="tahunFilter" class="form-label me-2 mb-0">Tahun:</label>
                    <select id="tahunFilter" class="form-select form-select-sm" style="width: 100px;">
                        @for($i = 2020; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="lineChartDepresiasi" width="100%" height="40"></canvas>
                <small class="text-muted">Menampilkan tren penyusutan nilai buku mesin dari tahun terpilih hingga sekarang</small>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Progress -->
    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-tachometer-alt me-1"></i>
                Status Mesin
            </div>
            <div class="card-body">
                <h6 class="small font-weight-bold">Kondisi Baik <span class="float-end">80%</span></h6>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 80%"></div>
                </div>

                <h6 class="small font-weight-bold">Perlu Maintenance <span class="float-end">15%</span></h6>
                <div class="progress mb-4">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 15%"></div>
                </div>

                <h6 class="small font-weight-bold">Rusak/Down <span class="float-end">5%</span></h6>
                <div class="progress mb-4">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 5%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAW Chart & Recent Activities -->
<div class="row">
    <!-- Grafik SAW -->
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i>
                Top 5 Prioritas Pemeliharaan (SAW Method)
            </div>
            <div class="card-body">
                <canvas id="hasilSawChart" width="100%" height="50"></canvas>
                <small class="text-muted mt-2 d-block">
                    Skor SAW berdasarkan kriteria: usia, frekuensi kerusakan, downtime, dan nilai ekonomis
                </small>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clock me-1"></i>
                Aktivitas Terbaru
            </div>
            <div class="card-body">
                <div class="timeline">
                    @php
                        $activities = [
                            ['time' => '2 jam lalu', 'text' => 'Maintenance mesin MC-001 selesai', 'icon' => 'check', 'color' => 'success'],
                            ['time' => '5 jam lalu', 'text' => 'Kerusakan ringan mesin MC-005 dilaporkan', 'icon' => 'exclamation', 'color' => 'warning'],
                            ['time' => '1 hari lalu', 'text' => 'Penambahan mesin baru MC-012', 'icon' => 'plus', 'color' => 'info'],
                            ['time' => '2 hari lalu', 'text' => 'Update kriteria SAW', 'icon' => 'edit', 'color' => 'primary'],
                        ];
                    @endphp

                    @foreach($activities as $activity)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $activity['color'] }}">
                            <i class="fas fa-{{ $activity['icon'] }}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">{{ $activity['text'] }}</div>
                            <div class="timeline-time">{{ $activity['time'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Maintenance Schedule -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-calendar-alt me-1"></i>
        Jadwal Maintenance Minggu Ini
        <div class="float-end">
            <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Jadwal
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="maintenanceTable">
                <thead>
                    <tr>
                        <th>Mesin</th>
                        <th>Jenis Maintenance</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maintenanceSchedule = [
                            ['mesin' => 'MC-001', 'jenis' => 'Preventive', 'tanggal' => '2025-08-08', 'status' => 'Pending', 'prioritas' => 'High'],
                            ['mesin' => 'MC-003', 'jenis' => 'Corrective', 'tanggal' => '2025-08-09', 'status' => 'In Progress', 'prioritas' => 'Critical'],
                            ['mesin' => 'MC-007', 'jenis' => 'Preventive', 'tanggal' => '2025-08-10', 'status' => 'Scheduled', 'prioritas' => 'Medium'],
                        ];
                    @endphp

                    @foreach($maintenanceSchedule as $schedule)
                    <tr>
                        <td><strong>{{ $schedule['mesin'] }}</strong></td>
                        <td>{{ $schedule['jenis'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule['tanggal'])->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $schedule['status'] == 'Pending' ? 'warning' : ($schedule['status'] == 'In Progress' ? 'info' : 'secondary') }}">
                                {{ $schedule['status'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $schedule['prioritas'] == 'Critical' ? 'danger' : ($schedule['prioritas'] == 'High' ? 'warning' : 'success') }}">
                                {{ $schedule['prioritas'] }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const tahunSelect = document.getElementById("tahunFilter");
    const ctxLine = document.getElementById("lineChartDepresiasi").getContext("2d");
    let lineChart;

    function fetchLineChartData(tahun) {
        // Simulasi data - ganti dengan fetch ke backend
        const sampleData = {
            labelsTahun: ['2022', '2023', '2024', '2025'],
            datasets: [
                {
                    label: 'MC-001',
                    data: [100000000, 80000000, 60000000, 40000000],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.1
                },
                {
                    label: 'MC-003',
                    data: [80000000, 64000000, 48000000, 32000000],
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.1
                }
            ]
        };

        if (lineChart) lineChart.destroy();

        lineChart = new Chart(ctxLine, {
            type: 'line',
            data: sampleData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Trend Penyusutan Nilai Buku Mesin'
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const val = ctx.raw;
                                return `${ctx.dataset.label}: Rp ${val?.toLocaleString('id-ID')}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + (value/1000000).toFixed(0) + 'M'
                        }
                    }
                }
            }
        });
    }

    // Event listener untuk filter tahun
    tahunSelect.addEventListener("change", () => {
        fetchLineChartData(tahunSelect.value);
    });

    // Initial load
    fetchLineChartData(tahunSelect.value);

    // SAW Chart
    const ctxSaw = document.getElementById("hasilSawChart").getContext("2d");
    const sampleSawData = {
        labels: ['MC-001', 'MC-005', 'MC-003', 'MC-008', 'MC-002'],
        datasets: [{
            label: 'Skor SAW',
            data: [0.85, 0.78, 0.72, 0.68, 0.65],
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 205, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ]
        }]
    };

    new Chart(ctxSaw, {
        type: 'bar',
        data: sampleSawData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1
                }
            }
        }
    });
});
</script>

<style>
/* Timeline styles */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 10px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.timeline-header {
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-time {
    font-size: 12px;
    color: #6c757d;
}

/* Progress bars animation */
.progress-bar {
    animation: progressAnimation 2s ease-in-out;
}

@keyframes progressAnimation {
    0% { width: 0%; }
    100% { width: var(--progress-width); }
}
</style>
@endpush

<!-- Sidebar -->
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- Dashboard -->
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <!-- Data Mesin -->
                <a class="nav-link {{ request()->routeIs('mesin.index') ? 'active' : '' }}" href="{{ route('mesin.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-industry"></i></div>
                    Data Mesin
                </a>

                <!-- Data Historis Kerusakan Tahunan -->
                <a class="nav-link {{ request()->routeIs('kerusakan-tahunan.index') ? 'active' : '' }}"
                   href="{{ route('kerusakan-tahunan.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                    Data Historis Kerusakan Tahunan
                </a>

                <!-- Perhitungan Penyusutan Mesin -->
                <a class="nav-link {{ request()->routeIs('depresiasi.index') ? 'active' : '' }}"
                   href="{{ route('depresiasi.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                    Perhitungan Penyusutan Mesin
                </a>

                <!-- Ranking Prioritas Pemeliharaan -->
                <a class="nav-link {{ request()->routeIs('prioritas.index') ? 'active' : '' }}"
                   href="{{ route('prioritas.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                    Ranking Prioritas Pemeliharaan Mesin
                </a>

                <!-- Data Kriteria dan Bobot -->
                <a class="nav-link {{ request()->routeIs('kriteria.index') ? 'active' : '' }}"
                   href="{{ route('kriteria.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-balance-scale"></i></div>
                    Data Kriteria & Bobot
                </a>

                <!-- Jadwal Pemeliharaan -->
                <a class="nav-link {{ request()->routeIs('jadwal.index') ? 'active' : '' }}"
                   href="{{ route('jadwal.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Jadwal Pemeliharaan
                </a>

                <!-- Kelola User (Admin Only) -->
                @if(Auth::check() && Auth::user()->role === 'regu_mekanik')
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                        Kelola User
                    </a>
                @endif
            </div>
        </div>
    </nav>
</div>

<style>
    /* Perbaikan untuk sidebar yang lebih rapi tanpa mengubah struktur utama */
    .sb-sidenav .nav-link {
        padding: 0.75rem 1rem !important;
        font-size: 0.975rem !important;
        line-height: 1.2 !important;
        word-wrap: break-word !important;
        white-space: normal !important;
    }

    .sb-sidenav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
        transition: background-color 0.2s ease-in-out !important;
    }

    .sb-sidenav .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-left: 3px solid #007bff !important;
    }

    .sb-nav-link-icon {
        margin-right: 0.5rem !important;
        width: 16px !important;
        display: inline-block !important;
        text-align: center !important;
    }

    .sb-nav-link-icon i {
        font-size: 0.875rem !important;
        width: 16px !important;
    }

    /* Untuk teks yang panjang agar tidak terpotong */
    .sb-sidenav .nav-link[href*="kerusakan-tahunan"],
    .sb-sidenav .nav-link[href*="depresiasi"],
    .sb-sidenav .nav-link[href*="prioritas"] {
        min-height: 60px !important;
        display: flex !important;
        align-items: center !important;
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }

    /* Responsive untuk layar yang lebih kecil */
    @media (max-width: 992px) {
        .sb-sidenav .nav-link {
            font-size: 0.8rem !important;
            padding: 0.6rem 0.8rem !important;
        }

        .sb-nav-link-icon {
            margin-right: 0.4rem !important;
        }
    }
</style>

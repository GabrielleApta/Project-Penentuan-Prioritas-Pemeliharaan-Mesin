<!-- Sidebar -->
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- Dashboard -->
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-fw fa-tachometer-alt me-2"></i></div>
                    Dashboard
                </a>

                <!-- Data Master -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePrioritas" aria-expanded="false" aria-controls="collapsePrioritas">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns me-2"></i></div>
                    Data Master
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePrioritas">
                    <nav class="sb-sidenav-menu-nested nav">

                <!-- Data Mesin -->
                <a class="nav-link {{ request()->routeIs('mesin.index') ? 'active' : '' }}" href="{{ route('mesin.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-fw fa-industry me-2"></i></div>
                    Data Mesin
                </a>

                <!-- Data Historis Kerusakan Tahunan -->
                <a class="nav-link {{ request()->routeIs('kerusakan-tahunan.index') ? 'active' : '' }}"
                           href="{{ route('kerusakan-tahunan.index') }}" title="Data Historis Kerusakan">
                            <i class="fas fa-history me-2"></i> Data Historis Kerusakan Tahunan
                        </a>


                    </nav>
                </div>

                <!-- Perhitungan -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePrioritas" aria-expanded="false" aria-controls="collapsePrioritas">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns me-2"></i></div>
                    Data Perhitungan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePrioritas">
                    <nav class="sb-sidenav-menu-nested nav">

                <!-- Perhitungan Penyusutan Mesin -->
                <a class="nav-link {{ request()->routeIs('depresiasi.index') ? 'active' : '' }}" href="{{ route('depresiasi.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-fw fa-calculator me-2"></i></div>
                    Perhitungan Penyusutan Mesin
                </a>

                <a class="nav-link {{ request()->routeIs('prioritas.index') ? 'active' : '' }}"
                           href="{{ route('prioritas.index') }}" title="Hasil SAW">
                            <i class="fas fa-chart-line me-2"></i> Hasil SAW
                        </a>

                <!-- Data Kriteria dan Bobot -->
                <a class="nav-link {{ request()->routeIs('kriteria.index') ? 'active' : '' }}"
                           href="{{ route('kriteria.index') }}" title="Kriteria dan Bobot">
                            <i class="fas fa-balance-scale me-2"></i> Data Kriteria & Bobot
                        </a>
                    </nav>
                </div>

                <!-- Penentuan Prioritas Pemeliharaan -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePrioritas" aria-expanded="false" aria-controls="collapsePrioritas">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns me-2"></i></div>
                    Penentuan Prioritas Pemeliharaan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="collapsePrioritas">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ request()->routeIs('penilaian.index') ? 'active' : '' }}"
                           href="{{ route('penilaian.index') }}" title="Penilaian Mesin">
                            <i class="fas fa-table me-2"></i> Data Penilaian Mesin
                        </a>
                    </nav>
                </div>
                <a class="nav-link {{ request()->routeIs('jadwal.index') ? 'active' : '' }}"
                           href="{{ route('jadwal.index') }}" title="Jadwal Pemeliharaan">
                            <i class="fas fa-table me-2"></i> Jadwal Pemeliharaan
                        </a>

                <!-- Kelola User (Admin Only) -->
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-users-cog me-2"></i></div>
                        Kelola User
                    </a>
                @endif
            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{ ucfirst(Auth::user()->role ?? 'Guest') }}
        </div>
    </nav>
</div>


<style>
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.05);
        transition: background-color 0.2s ease-in-out;
    }
</style>

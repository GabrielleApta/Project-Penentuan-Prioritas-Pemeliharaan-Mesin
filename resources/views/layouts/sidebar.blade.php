<!-- Sidebar -->
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- Dashboard -->
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-fw fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <!-- Data Mesin -->
                <a class="nav-link {{ request()->routeIs('mesin.index') ? 'active' : '' }}" href="{{ route('mesin.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-fw fa-industry"></i></div>
                    Data Mesin
                </a>

                <!-- Depresiasi -->
                <a class="nav-link {{ request()->routeIs('depresiasi.index') ? 'active' : '' }}" href="{{ route('depresiasi.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-fw fa-calculator"></i></div>
                    Perhitungan Depresiasi
                </a>

                <!-- Penentuan Prioritas -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Penentuan Prioritas Pemeliharaan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                 <div class="collapse" id="collapseLayouts">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('prioritas.index') }}">
                            <i class="fas fa-chart-line me-1"></i> Hasil SAW
                        </a>

                        <a class="nav-link" href="{{ route('kerusakan-tahunan.index') }}">
                            <i class="fas fa-history me-1"></i> History Kerusakan
                        </a>

                        <a class="nav-link" href="{{ route('kriteria.index') }}">
                            <i class="fas fa-balance-scale me-1"></i> Data Kriteria & Bobot
                        </a>

                        <a class="nav-link" href="{{ route('penilaian.index') }}">
                            <i class="fas fa-table me-1"></i> Data Penilaian Mesin
                        </a>
                    </nav>
                </div>
           <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{ Auth::user()->role ?? 'Admin' }}
        </div>
    </nav>
</div>

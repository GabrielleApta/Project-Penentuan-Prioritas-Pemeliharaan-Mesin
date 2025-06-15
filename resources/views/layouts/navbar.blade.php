<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    {{-- Kiri: Tanggal Real-Time --}}
    <a class="navbar-brand ps-3" href="{{ url('/') }}">
        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
    </a>

    {{-- Toggle Sidebar --}}
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Spacer --}}
    <div class="d-none d-md-block flex-grow-1"></div>

    {{-- Kanan: Info Pengguna + Dropdown --}}
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
                <span class="text-white">
                    Hi, {{ auth()->check() ? ucfirst(auth()->user()->role) : 'Admin' }}!
                </span>
                <img src="{{ auth()->user()->photo ?? asset('images/default-avatar.png') }}"
                     alt="Profile"
                     class="rounded-circle"
                     width="32"
                     height="32">
            </a>

            {{-- Dropdown Menu --}}
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" href="{{ route('login') }}">
                        <i class="fas fa-right-left me-2"></i> Ganti Akun
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item text-danger" href="#" id="btnLogout">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

{{-- SweetAlert Logout Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoutBtn = document.getElementById('btnLogout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin logout?',
                    text: "Sesi Anda akan diakhiri.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, logout',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            });
        }
    });
</script>

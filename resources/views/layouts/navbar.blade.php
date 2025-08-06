<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Real-time Date di Kiri -->
    <div class="navbar-text text-white ms-3" style="font-size: 1.3rem;">
        <span id="currentDate"></span>
    </div>

    <!-- Navbar Brand-->
    <!-- <a class="navbar-brand ps-3" href="{{ route('dashboard') }}">Your App Name</a> -->

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- User Info di Kanan -->
    <div class="navbar-text text-white ms-auto me-4" style="font-size: 1.2rem;">
        Hi, {{ auth()->check() ? ucfirst(auth()->user()->name) : 'Regu Mekanik' }}!
    </div>
</nav>

<script>
// Update tanggal realtime
function updateDate() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Asia/Jakarta'
    };

    const dateStr = now.toLocaleDateString('id-ID', options);
    const timeStr = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    document.getElementById('currentDate').textContent = `${dateStr}, ${timeStr}`;
}

// Update setiap detik
setInterval(updateDate, 1000);
updateDate(); // Initial call
</script>

/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
*/

// Menunggu seluruh DOM selesai dimuat
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Pastikan localStorage tersedia dan tombol toggle ada
    if (sidebarToggle && typeof localStorage !== 'undefined') {
        // Ambil status toggle dari localStorage
        const isToggled = localStorage.getItem('sb|sidebar-toggle') === 'true';

        if (isToggled) {
            document.body.classList.add('sb-sidenav-toggled');
        }

        // Toggle saat klik
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');

            // Simpan status terbaru
            localStorage.setItem(
                'sb|sidebar-toggle',
                document.body.classList.contains('sb-sidenav-toggled')
            );
        });
    }

    // Tutup sidebar jika link di klik (untuk layar kecil)
    const navLinks = document.querySelectorAll('.nav-link');
    if (navLinks.length > 0) {
        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 768) {
                    document.body.classList.remove('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', 'false');
                }
            });
        });
    }

    // Optional: Aktifkan semua dropdown (jika belum aktif)
    const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdownToggles.forEach(dropdown => {
        dropdown.addEventListener('click', function (e) {
            // Bootstrap sudah meng-handle ini secara otomatis via data-bs-toggle
            // Tapi kalau kamu ingin menambahkan logging/debugging, bisa disini
        });
    });
});

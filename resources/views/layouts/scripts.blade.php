<script src="{{ asset('sb-admin/js/scripts.js') }}"></script>

{{-- Chart.js --}}
@if (request()->routeIs('dashboard'))
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="{{ asset('sb-admin/assets/demo/chart-area-demo.js') }}"></script>
  <script src="{{ asset('sb-admin/assets/demo/chart-bar-demo.js') }}"></script>
@endif

{{-- DataTables --}}
@if (request()->is('mesin*') || request()->is('depresiasi*'))
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
  <script src="{{ asset('sb-admin/js/datatables-simple-demo.js') }}"></script>
@endif

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

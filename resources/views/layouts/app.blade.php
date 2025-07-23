<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>@yield('title', 'Dashboard')</title>

    {{-- Styles --}}
    <link href="{{ asset('sb-admin/css/styles.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    {{-- Optional custom styles --}}
    @stack('styles')
</head>

<body class="sb-nav-fixed">
    @include('layouts.navbar')

    <div id="layoutSidenav">
        @include('layouts.sidebar')

        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            @include('layouts.footer')
        </div>
    </div>

    {{-- JavaScript: HANYA sekali dan terstruktur --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('sb-admin/js/scripts.js') }}"></script>

    {{-- Untuk file script tambahan halaman tertentu --}}
    @stack('scripts')
</body>
</html>

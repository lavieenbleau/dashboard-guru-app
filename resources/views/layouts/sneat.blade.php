<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "Dashboard" }}</title>

    <!-- Sneat CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}">
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar --}}
            @include('layouts.sidebar')

            {{-- PAGE CONTENT --}}
            <div class="layout-page">
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>

        </div>
    </div>

    <!-- Sneat JS -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>

    <script>
    // Set initial layout class based on sidebar state
    const html = document.documentElement;
    const sidebar = document.getElementById('sidebar');

    if (sidebar && sidebar.classList.contains('collapsed')) {
        html.classList.add('layout-menu-collapsed');
    } else {
        html.classList.add('layout-menu-expanded');
    }
    </script>
</body>

</html>
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
    
    <style>
        /* Prevent horizontal scroll */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        
        .layout-wrapper {
            overflow-x: hidden;
        }
        
        .layout-page {
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .content-wrapper {
            padding: 1.5rem;
            max-width: 100%;
            overflow-x: hidden;
            margin: 0;
        }
        
        /* Ensure content doesn't overflow */
        .container-xxl, .container-fluid {
            max-width: 100%;
            overflow-x: hidden;
        }
    </style>
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
    // Sidebar is now always expanded
    </script>
</body>

</html>
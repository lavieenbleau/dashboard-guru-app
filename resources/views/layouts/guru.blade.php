<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        background: #f6f5fc;
    }

    .sidebar {
        height: 100vh;
        background: #6a1b9a;
        color: white;
        padding: 20px;
    }

    .sidebar a {
        display: block;
        font-size: 18px;
        padding: 12px;
        text-decoration: none;
        color: white;
        margin-bottom: 8px;
        border-radius: 8px;
    }

    .sidebar a:hover {
        background: #8e24aa;
    }

    .menu-card {
        text-align: center;
        padding: 25px;
        border-radius: 16px;
        background: white;
        transition: 0.3s;
    }

    .menu-card:hover {
        background: #f3e5f5;
    }

    .menu-icon {
        font-size: 48px;
    }
    </style>
</head>

<body>

    <div class="d-flex">

        @include('partials.sidebar')

        <div class="container p-4">
            @yield('content')
        </div>

    </div>

</body>

</html>
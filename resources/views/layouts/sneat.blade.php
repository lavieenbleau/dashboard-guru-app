<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? "Dashboard" }}</title>

    <!-- Sneat CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        /* Modern Dashboard Styling Override - Premium SaaS 2026 */
        :root {
            --primary: #696CFF;
            --primary-hover: #5f61e6;
            --primary-soft: rgba(105, 108, 255, 0.08);
            --bg-body: #F4F7FB;
            --bg-card: rgba(255, 255, 255, 0.65);
            --border-color: rgba(255, 255, 255, 0.7);
            --text-main: #0F172A;
            --text-sub: #64748B;
            --shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.03);
            --shadow-hover: 0 12px 30px rgba(0, 0, 0, 0.06);
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
            --glass-blur: blur(24px);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
            background-color: var(--bg-body) !important;
            color: var(--text-main);
            letter-spacing: -0.01em;
            background-image: 
                radial-gradient(at 0% 0%, rgba(105, 108, 255, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
        }
        
        .layout-page, .content-wrapper, .layout-container {
            background-color: transparent !important;
        }

        /* Sidebar Override */
        .layout-menu {
            width: 260px !important;
            border-right: 1px solid rgba(255, 255, 255, 0.8) !important;
            background-color: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            box-shadow: 4px 0 24px rgba(0,0,0,0.01) !important;
            z-index: 1000;
        }
        .app-brand {
            padding: 2rem 1.5rem 1rem 1.5rem !important;
            border-bottom: none !important;
        }
        .bg-menu-theme {
            background-color: transparent !important;
        }
        .menu-inner {
            padding-top: 1rem !important;
            padding-inline: 1rem !important;
        }
        .menu-item {
            margin-bottom: 6px;
        }
        .menu-inner > .menu-item > .menu-link {
            border-radius: var(--radius-md) !important;
            padding: 0.75rem 1rem !important;
            color: var(--text-sub) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            font-weight: 500 !important;
            margin: 0 !important;
        }
        .menu-inner > .menu-item:hover > .menu-link {
            background-color: rgba(255,255,255,0.8) !important;
            color: var(--text-main) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transform: translateX(4px);
        }
        .menu-inner > .menu-item.active > .menu-link {
            background-color: var(--primary-soft) !important;
            color: var(--primary) !important;
            font-weight: 600 !important;
            box-shadow: inset 0 0 0 1px rgba(105, 108, 255, 0.15), 0 4px 12px rgba(105, 108, 255, 0.08) !important;
            transform: translateX(4px);
        }
        .menu-icon {
            margin-right: 0.85rem !important;
            display: flex;
            align-items: center;
        }
        .menu-inner > .menu-item > .menu-link .lucide,
        .menu-inner > .menu-item > .menu-link i {
            color: var(--text-sub) !important;
            width: 22px;
            height: 22px;
            transition: color 0.3s ease;
        }
        .menu-inner > .menu-item.active > .menu-link .lucide,
        .menu-inner > .menu-item.active > .menu-link i,
        .menu-inner > .menu-item:hover > .menu-link .lucide,
        .menu-inner > .menu-item:hover > .menu-link i {
            color: var(--primary) !important;
        }

        /* Topnav */
        .modern-topnav {
            height: 76px;
            background: rgba(244, 247, 251, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 99;
            box-shadow: 0 4px 20px rgba(0,0,0,0.01);
        }
        .search-bar {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 99px;
            padding: 0.6rem 1.25rem;
            display: flex;
            align-items: center;
            width: 360px;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.01), 0 2px 8px rgba(0,0,0,0.02);
        }
        .search-bar:focus-within {
            border-color: rgba(105, 108, 255, 0.4);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.1), 0 4px 12px rgba(0,0,0,0.03);
            width: 400px;
        }
        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            margin-left: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-main);
            font-weight: 500;
        }
        .search-bar input::placeholder {
            color: #94A3B8;
        }

        /* Nav Actions */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .action-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-sub);
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.4);
        }
        .action-icon:hover {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        /* Cards - Premium Glass */
        .card, .edu-card {
            background-color: var(--bg-card) !important;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-sm) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: visible !important; /* Allow dropdowns to overflow */
            position: relative;
            z-index: 1;
        }
        .card:hover, .edu-card:hover,
        .card:focus-within, .edu-card:focus-within {
            z-index: 1050;
        }
        .card-header:first-child, .edu-card-header:first-child {
            border-top-left-radius: var(--radius-lg) !important;
            border-top-right-radius: var(--radius-lg) !important;
        }
        .card:hover, .edu-card:hover {
            box-shadow: var(--shadow-hover) !important;
            transform: translateY(-2px);
            background-color: rgba(255, 255, 255, 0.8) !important;
        }
        .card-header, .edu-card-header {
            border-bottom: 1px solid rgba(0,0,0,0.03) !important;
            background-color: transparent !important;
            padding: 1.5rem 1.75rem !important;
            margin-bottom: 0 !important;
        }
        .card-body, .edu-card-body {
            padding: 1.75rem !important;
        }
        
        /* Ensure distinct gap between vertically stacked cards */
        .card + .card,
        .card + .row,
        .row + .card {
            margin-top: 2rem !important;
        }
        
        /* Uniform Grid Spacing */
        .row {
            --bs-gutter-x: 1.75rem;
            --bs-gutter-y: 1.75rem;
        }
        
        .btn {
            border-radius: 12px !important;
            font-weight: 600 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            padding: 0.6rem 1.25rem;
        }
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3) !important;
            color: #FFFFFF !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            box-shadow: 0 6px 16px rgba(105, 108, 255, 0.4) !important;
            transform: translateY(-2px) !important;
        }
        
        .btn-outline-primary {
            color: var(--primary) !important;
            border-color: rgba(105, 108, 255, 0.3) !important;
            background-color: rgba(255, 255, 255, 0.5) !important;
            backdrop-filter: blur(4px);
        }
        .btn-outline-primary:hover {
            background-color: rgba(105, 108, 255, 0.05) !important;
            color: var(--primary-hover) !important;
            border-color: var(--primary) !important;
        }

        /* Badges */
        .bg-primary, .bg-label-primary {
            background-color: var(--primary-soft) !important;
            color: var(--primary) !important;
            border: 1px solid rgba(105, 108, 255, 0.1);
        }
        .bg-info, .bg-label-info {
            background-color: rgba(6, 182, 212, 0.1) !important;
            color: #06B6D4 !important;
            border: 1px solid rgba(6, 182, 212, 0.1);
        }
        .badge {
            border-radius: 9999px !important;
            font-weight: 600 !important;
            padding: 0.4em 0.8em !important;
            letter-spacing: 0.02em;
        }

        /* Text colors */
        .text-primary { color: var(--primary) !important; }
        .text-muted { color: var(--text-sub) !important; }
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-main) !important;
            font-weight: 700 !important;
            letter-spacing: -0.02em;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 12px !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
            background-color: rgba(255,255,255,0.7) !important;
            backdrop-filter: blur(8px);
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px var(--primary-soft) !important;
            background-color: #FFFFFF !important;
        }

        /* Tables */
        .table > :not(caption) > * > * {
            border-bottom-color: rgba(0,0,0,0.05) !important;
            padding: 1rem !important;
        }
        thead th {
            background-color: transparent !important;
            color: var(--text-sub) !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.7rem !important;
            letter-spacing: 0.08em !important;
            border-bottom: 2px solid rgba(0,0,0,0.05) !important;
        }

        /* General Dashboard Premium Utils */
        .edu-wrapper {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .edu-page-bg {
            background-color: #F8FAFC !important;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
        }
        .edu-card {
            background-color: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            overflow: visible !important; /* Allow dropdowns to overflow */
            position: relative;
            z-index: 1;
        }
        .edu-card-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            border-bottom: 1px solid #E5E7EB;
            background-color: #FFFFFF;
            padding: 1.25rem 1.5rem;
        }
        .edu-card-body {
            padding: 1.5rem;
        }
        
        .text-main { color: #111827 !important; }
        .text-sub { color: #6B7280 !important; }
        .text-indigo { color: #4F46E5 !important; }
        .text-cyan { color: #06B6D4 !important; }
        
        .btn-edu {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .btn-edu-primary {
            background-color: #4F46E5;
            color: #FFFFFF;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-edu-primary:hover {
            background-color: #4338ca;
            color: #FFFFFF;
            transform: translateY(-1px);
        }
        .btn-edu-outline {
            background-color: #FFFFFF;
            border-color: #E5E7EB;
            color: #374151;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-edu-outline:hover {
            background-color: #F9FAFB;
            border-color: #D1D5DB;
            color: #111827;
        }
        
        .edu-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
        }
        .badge-indigo { background-color: #EEF2FF; color: #4F46E5; }
        .badge-cyan { background-color: #ECFEFF; color: #06B6D4; }
        .badge-gray { background-color: #F3F4F6; color: #4B5563; }
        
        .item-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background-color: #EEF2FF;
            color: #4F46E5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        /* Premium SaaS Dropdown Override */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(24px) !important;
            -webkit-backdrop-filter: blur(24px) !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.05) !important;
            padding: 8px !important;
            min-width: 210px !important;
            width: max-content;
            margin-top: 8px !important;
            animation: dropdownFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transform-origin: top right;
            z-index: 9999 !important;
        }
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: scale(0.96) translateY(-8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .dropdown-item {
            display: flex !important;
            align-items: center !important;
            min-height: 44px !important;
            border-radius: 10px !important;
            padding: 8px 14px !important;
            color: var(--text-main) !important;
            font-weight: 500 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            gap: 14px !important;
        }
        .dropdown-item i {
            margin: 0 !important; /* override me-1/me-2 from template */
            font-size: 1.15rem !important;
            color: var(--text-sub) !important;
            transition: color 0.2s ease !important;
            width: 20px; /* fixed width for alignment */
            display: flex;
            justify-content: center;
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: var(--primary-soft) !important;
            color: var(--primary) !important;
            transform: translateX(4px);
        }
        .dropdown-item:hover i, .dropdown-item:focus i {
            color: var(--primary) !important;
        }
        .dropdown-item.text-danger {
            color: #EF4444 !important;
        }
        .dropdown-item.text-danger i {
            color: #EF4444 !important;
        }
        .dropdown-item.text-danger:hover {
            background-color: rgba(239, 68, 68, 0.08) !important;
            color: #DC2626 !important;
        }
        .dropdown-item.text-danger:hover i {
            color: #DC2626 !important;
        }
        .dropdown-divider {
            border-top: 1px solid rgba(0,0,0,0.06) !important;
            margin: 6px 0 !important;
        }

        /* Fix SweetAlert2 z-index so it appears above Bootstrap Modals */
        .swal2-container {
            z-index: 99999 !important;
        }

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
            display: flex;
            flex-direction: column;
        }
        
        .content-wrapper {
            padding: 1.5rem;
            padding-bottom: 5rem !important; /* Distance between content and footer */
            max-width: 100%;
            overflow-x: hidden;
            margin: 0;
            flex-grow: 1;
        }
        
        /* Ensure content doesn't overflow */
        .container-xxl, .container-fluid {
            max-width: 1600px !important;
            padding-left: 2rem !important;
            padding-right: 2rem !important;
            overflow-x: hidden;
        }
        /* Minimalist Footer */
        .content-footer {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            margin-top: auto;
            padding-top: 8rem !important; /* Huge gap between content and footer */
            padding-bottom: 1.5rem !important; /* Footer sits right at the bottom */
        }
    </style>

    @yield('styles')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar --}}
            @include('layouts.sidebar')

            {{-- PAGE CONTENT --}}
            <div class="layout-page">
                {{-- MODERN TOP NAV --}}
                <nav class="modern-topnav" style="justify-content: flex-end;">
                    <div class="nav-actions">
                        <div class="d-flex align-items-center ms-2" style="cursor:pointer;" onclick="window.location='{{ route('guru.pengaturan', request()->segment(2) ?? 1) }}'">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=EEF2FF&color=4F46E5"
                                class="rounded-circle border" width="36" height="36">
                        </div>
                    </div>
                </nav>

                <div class="content-wrapper">
                    @yield('content')
                </div>
                
                <!-- Minimalist Centered Footer -->
                <footer class="content-footer footer">
                    <div class="container-xxl text-center">
                        <span class="text-muted" style="font-weight: 500; letter-spacing: 0.5px;">&copy; {{ date('Y') }} &mdash; SCI MEDIA LMS</span>
                    </div>
                </footer>
            </div>

        </div>
    </div>

    <!-- Sneat JS -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>

    <script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Global Image Fallback
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('img').forEach(function(img) {
            img.onerror = function() {
                this.src = '/assets/img/no-image.png';
            };
        });
    });

    // Global Upload Image Helper for Summernote
    function uploadImage(file, folder, editor) {
        let data = new FormData();
        data.append('image', file);
        data.append('folder', folder);

        if (typeof $ !== 'undefined') {
            $.ajax({
                url: '/upload/image',
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function(response) {
                    if (response.success) {
                        $(editor).summernote('insertImage', response.url);
                    } else {
                        alert(response.message || 'Upload gagal');
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan saat upload';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    alert(msg);
                }
            });
        }
    }
    
    // Global SweetAlert2 Helpers
    function showSuccess(message) {
        if (typeof Swal === 'undefined') return alert(message);
        return Swal.fire({
            title: 'Berhasil',
            text: message,
            icon: 'success',
            confirmButtonText: 'Tutup',
            customClass: { confirmButton: 'btn btn-primary' },
            buttonsStyling: false
        });
    }

    function showError(message) {
        if (typeof Swal === 'undefined') return alert(message);
        return Swal.fire({
            title: 'Gagal',
            text: message,
            icon: 'error',
            confirmButtonText: 'Tutup',
            customClass: { confirmButton: 'btn btn-primary' },
            buttonsStyling: false
        });
    }

    function showConfirm(title, text, confirmBtnText = 'Ya, Lanjutkan', isDanger = false) {
        if (typeof Swal === 'undefined') {
            return Promise.resolve({ isConfirmed: confirm(title + '\n' + text) });
        }
        return Swal.fire({
            title: title || 'Konfirmasi Tindakan',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: isDanger ? 'btn btn-danger me-2' : 'btn btn-primary me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });
    }

    function confirmSubmit(event, title, text, confirmBtnText = 'Ya, Lanjutkan', isDanger = false) {
        event.preventDefault();
        const form = event.target;
        showConfirm(title, text, confirmBtnText, isDanger).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    function confirmClick(event, title, text, confirmBtnText = 'Ya, Lanjutkan', isDanger = false) {
        event.preventDefault();
        const element = event.currentTarget;
        showConfirm(title, text, confirmBtnText, isDanger).then((result) => {
            if (result.isConfirmed) {
                if (element.tagName === 'A') {
                    window.location.href = element.href;
                } else if (element.closest('form')) {
                    element.closest('form').submit();
                }
            }
        });
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @if(session('swal_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                showError("{{ session('swal_error') }}");
            }, 500);
        });
    </script>
    @endif

    @yield('scripts')
</body>

</html>
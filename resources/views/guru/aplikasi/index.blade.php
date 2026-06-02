@extends('layouts.sneat')

@section('content')

<style>
    #sidebar, .layout-navbar, .content-footer, .modern-topnav {
        display: none !important;
    }
    
    body {
        background-color: #f4f6ff !important;
        position: relative;
        overflow-x: hidden;
    }
    
    .layout-page {
        margin-left: 0 !important;
        background-color: transparent !important;
        min-height: 100vh;
    }

    /* Completely reset content-wrapper for this page to prevent pushing */
    .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    /* Background Blur Blobs */
    .bg-blobs::before {
        content: '';
        position: fixed;
        width: 800px;
        height: 800px;
        background: radial-gradient(circle, rgba(230,232,255,0.9) 0%, rgba(255,255,255,0) 65%);
        top: -200px;
        right: -200px;
        z-index: -1;
        border-radius: 50%;
    }
    .bg-blobs::after {
        content: '';
        position: fixed;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(230,232,255,0.7) 0%, rgba(255,255,255,0) 65%);
        bottom: -200px;
        left: -100px;
        z-index: -1;
        border-radius: 50%;
    }
    
    .app-card-wrapper {
        max-width: 380px;
        margin: 0 auto;
    }

    .app-card {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border-radius: 24px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.9);
        box-shadow: 0 8px 32px rgba(0,0,0,0.03);
    }
    
    .app-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: #5c60f5;
        box-shadow: 0 16px 48px rgba(92, 96, 245, 0.1);
    }
    
    .btn-premium-outline {
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.9);
        color: #0F172A;
        border-radius: 99px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    
    .btn-premium-outline:hover {
        background: #ffffff;
        color: #5c60f5;
        box-shadow: 0 8px 24px rgba(92, 96, 245, 0.1);
        transform: translateY(-2px);
    }
    
    .btn-premium-danger {
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.9);
        color: #e3342f;
        border-radius: 99px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    
    .btn-premium-danger:hover {
        background: #fff5f5;
        color: #cc1f1a;
        border-color: #ffcccc;
        box-shadow: 0 8px 24px rgba(227, 52, 47, 0.1);
        transform: translateY(-2px);
    }
</style>

<div class="bg-blobs"></div>
<div class="container py-5" style="z-index: 1; position: relative;">
    <!-- Header -->
    <div class="text-center mb-5">
        <div class="d-inline-block mb-4 p-3 rounded-circle" style="background: rgba(255,255,255,0.8); box-shadow: 0 8px 32px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.9);">
            <img src="{{ asset('images/logo-sci.png') }}" alt="SCI Media" height="60">
        </div>
        <h2 class="fw-bold mb-2" style="color: #0F172A; letter-spacing: -0.02em;">Halo, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
        <p class="text-muted" style="font-size: 1.1rem;">Silakan pilih aplikasi kurikulum yang ingin dikelola</p>
    </div>

    <!-- Applications -->
    <div class="row justify-content-center g-4 mb-5">
        @forelse ($serials as $item)
        <div class="col-md-6 col-lg-4">
            <div class="app-card-wrapper">
                <a href="{{ url('/guru/aplikasi/'.$item->id) }}" class="text-decoration-none">
                    <div class="app-card p-4 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                        <div class="mb-3 p-3 rounded-circle" style="background: #f4f6ff; color: #5c60f5;">
                            <i class='bx bx-book-open' style="font-size: 40px;"></i>
                        </div>
                        <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.2rem;">{{ $item->product->name }}</h5>
                        <div class="d-flex gap-2 justify-content-center mt-2">
                            <span class="badge bg-label-primary rounded-pill px-3">Grade: {{ $item->product->grade }}</span>
                            <span class="badge bg-label-info rounded-pill px-3">Semester: {{ $item->product->semester }}</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="p-4 d-inline-block rounded-circle mb-3" style="background: rgba(255,255,255,0.6);">
                <i class='bx bx-book' style="font-size: 48px; color: #cbd5e1;"></i>
            </div>
            <p class="text-muted mt-2" style="font-size: 1.1rem;">Belum ada aplikasi kurikulum yang terdaftar</p>
        </div>
        @endforelse
    </div>

    <!-- Bottom Action -->
    <div class="text-center mt-4">
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn-premium-danger d-inline-flex align-items-center">
                <i class='bx bx-log-out me-2'></i>Keluar Aplikasi
            </button>
        </form>
    </div>
</div>

@endsection
@extends('layouts.sneat')

@section('content')

<style>
    #sidebar {
        display: none !important;
    }
    
    .layout-page {
        margin-left: 0 !important;
        background-color: #f8f9fa;
    }
    
    .app-card {
        background: white;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #e0e0e0;
    }
    
    .app-card:hover {
        border-color: #696cff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
</style>

<div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <img src="{{ asset('images/logo-sci.png') }}" alt="SCI Media" height="80" class="mb-4">
        <h3 class="fw-bold mb-2">Halo, {{ auth()->user()->name }}</h3>
        <p class="text-muted">Pilih aplikasi kurikulum</p>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center gap-2 mb-5">
        @php
        $pengaturanSerial = \App\Models\Serial::where('user_id', auth()->id())->first()
            ?? \App\Models\Serial::first();
        @endphp
        <a href="{{ route('guru.pengaturan', $pengaturanSerial->id ?? 1) }}" class="btn btn-outline-primary">
            <i class='bx bx-cog me-1'></i>Pengaturan
        </a>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-danger">
                <i class='bx bx-log-out me-1'></i>Logout
            </button>
        </form>
    </div>

    <!-- Applications -->
    <div class="row justify-content-center g-3">
        @forelse ($serials as $item)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <a href="{{ url('/guru/aplikasi/'.$item->id) }}" class="text-decoration-none">
                <div class="app-card p-4 text-center">
                    <i class='bx bx-book-open mb-3' style="font-size: 48px; color: #696cff;"></i>
                    <h5 class="fw-bold mb-2 text-dark">{{ $item->product->name }}</h5>
                    <p class="text-muted small mb-0">
                        Grade: {{ $item->product->grade }} | Semester: {{ $item->product->semester }}
                    </p>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class='bx bx-book' style="font-size: 48px; color: #ccc;"></i>
            <p class="text-muted mt-3">Tidak ada aplikasi kurikulum</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-1">Materi – {{ $serial->product->name }}</h4>
            <p class="text-muted mb-0">Pilih mata pelajaran untuk mulai mengelola materi</p>
        </div>
    </div>

    <!-- Theme (Mata Pelajaran) Cards -->
    <div class="row g-3">
        @forelse ($themes as $theme)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('guru.materi.tema', [$serial->id, $theme->id]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class='bx bx-book-open'></i>
                                </span>
                            </div>
                            <h5 class="mb-0">{{ $theme->name }}</h5>
                        </div>
                        <p class="text-muted small mb-0">Klik untuk melihat sub tema dan materi</p>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class='bx bx-info-circle me-2'></i>
                Belum ada mata pelajaran tersedia untuk aplikasi ini.
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
.hover-shadow-lg {
    transition: all 0.3s ease;
}

.hover-shadow-lg:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}
</style>
@endsection
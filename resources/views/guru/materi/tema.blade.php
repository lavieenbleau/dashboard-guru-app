@extends('layouts.sneat')

@section('content')
<div class="container-xxl">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item active">{{ $mapel->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-1">Tema - {{ $mapel->name }}</h4>
            <p class="text-muted mb-0">Pilih tema untuk melihat sub tema dan materi</p>
        </div>
    </div>

    <!-- Tema Cards -->
    <div class="row g-3">
        @forelse ($themes as $theme)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('guru.materi.subtema', [$serial->id, $mapel->id, $theme->id]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class='bx bx-book-bookmark'></i>
                                </span>
                            </div>
                            <h5 class="mb-0">Tema {{ $theme->theme }}</h5>
                        </div>
                        <h6 class="text-dark">{{ $theme->name }}</h6>
                        <small class="text-muted">Klik untuk melihat sub tema</small>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class='bx bx-info-circle me-2'></i>
                Belum ada tema untuk mata pelajaran ini.
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
@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item active">{{ $tema->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-1">{{ $tema->name }}</h4>
            <p class="text-muted mb-0">Pilih untuk melihat dan mengelola materi</p>
        </div>
    </div>

    <!-- Sub Tema Cards -->
    <div class="row g-3">
        @forelse ($subthemes as $subtheme)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('guru.materi.list', [$serial->id, $tema->id, $subtheme->id]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class='bx bx-book-content'></i>
                                </span>
                            </div>
                            <h5 class="mb-0">{{ $subtheme->name }}</h5>
                        </div>
                        <small class="text-muted">Klik untuk melihat materi</small>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class='bx bx-info-circle me-2'></i>
                Belum ada sub tema untuk tema ini.
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
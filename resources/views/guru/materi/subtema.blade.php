@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route($type === 'admin' ? 'guru.materi.admin' : 'guru.materi.custom', $serial->id) }}">{{ $type === 'admin' ? 'Materi dari Admin' : 'Materi Tambahan' }}</a>
            </li>
            <li class="breadcrumb-item active">{{ $tema->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">{{ $tema->name }}</h4>
                <p class="text-muted mb-0">
                    {{ $type === 'admin' ? 'Pilih subtema untuk melihat materi dari admin' : 'Pilih subtema untuk melihat dan mengelola materi' }}
                </p>
            </div>
            @if($type === 'custom')
            <a href="{{ route('guru.materi.subtheme.create', [$serial->id, $tema->id]) }}" class="btn btn-success">
                <i class='bx bx-plus me-1'></i>Tambah Subtema
            </a>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Sub Tema Cards -->
    <div class="row g-3">
        @forelse ($subthemes as $subtheme)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('guru.materi.list', [$serial->id, $tema->id, $subtheme->id, $type]) }}"
                class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <span
                                    class="avatar-initial rounded {{ $type === 'admin' ? 'bg-label-primary' : 'bg-label-success' }}">
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
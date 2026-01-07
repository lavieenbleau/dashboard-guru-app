@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Materi Tambahan</h4>
                <p class="text-muted mb-0">Pilih mata pelajaran untuk mengelola materi</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.materi', $serial->id) }}" class="btn btn-outline-secondary">
                    <i class='bx bx-arrow-back me-1'></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Mapel Cards -->
    <div class="row g-3">
        @forelse ($mapels as $mapel)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('guru.materi.mapel', [$serial->id, $mapel->id]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class='bx bx-book-add'></i>
                                </span>
                            </div>
                            <h5 class="mb-0">{{ $mapel->name }}</h5>
                        </div>
                        <p class="text-muted small mb-0">Kelola materi tambahan</p>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class='bx bx-info-circle me-2'></i>
                Belum ada mata pelajaran tersedia.
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

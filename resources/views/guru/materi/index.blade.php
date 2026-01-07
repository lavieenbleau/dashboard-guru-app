@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-1">Materi – {{ $serial->product->name }}</h4>
            <p class="text-muted mb-0">Pilih kategori materi yang ingin diakses</p>
        </div>
    </div>

    <!-- Category Cards -->
    <div class="row g-4 mb-4">
        <!-- Materi dari Admin -->
        <div class="col-md-6">
            <a href="{{ route('guru.materi.admin', $serial->id) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition border-primary" style="border-width: 2px;">
                    <div class="card-body text-center py-5">
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            <span class="avatar-initial rounded bg-label-primary" style="width: 80px; height: 80px;">
                                <i class='bx bx-book-bookmark' style="font-size: 48px;"></i>
                            </span>
                        </div>
                        <h4 class="mb-2">Materi</h4>
                        <p class="text-muted mb-0">Materi yang disediakan oleh admin</p>
                        <span class="badge bg-label-primary mt-2">Dari Admin</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Materi Tambahan (Buatan Guru) -->
        <div class="col-md-6">
            <a href="{{ route('guru.materi.custom', $serial->id) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition border-success" style="border-width: 2px;">
                    <div class="card-body text-center py-5">
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            <span class="avatar-initial rounded bg-label-success" style="width: 80px; height: 80px;">
                                <i class='bx bx-book-add' style="font-size: 48px;"></i>
                            </span>
                        </div>
                        <h4 class="mb-2">Materi Tambahan</h4>
                        <p class="text-muted mb-0">Materi yang Anda buat sendiri</p>
                        <span class="badge bg-label-success mt-2">Buatan Guru</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.hover-shadow-lg {
    transition: all 0.3s ease;
}

.hover-shadow-lg:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-5px);
}
</style>
@endsection
@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.mapel', [$serial->id, $lesson->mapel_id ?? 0]) }}">{{ $lesson->mapel->name ?? 'Mata Pelajaran' }}</a></li>
            <li class="breadcrumb-item active">{{ $lesson->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            Bank Soal / {{ $lesson->mapel->name ?? 'Mata Pelajaran' }} / {{ $lesson->name }} / Pilih Kategori
        </h4>
        <a href="{{ route('guru.monitoring-quiz') }}" class="btn btn-primary">
            <i class="bx bx-desktop me-1"></i> Monitoring Kuis
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($categories as $category)
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card h-100 hover-card border-{{ $category['color'] }}">
                    <div class="card-body text-center">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-{{ $category['color'] }}">
                                <i class='bx {{ $category['icon'] }} fs-1'></i>
                            </span>
                        </div>
                        <h5 class="card-title mb-3">{{ $category['name'] }}</h5>
                        <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category['id']]) }}" class="btn btn-{{ $category['color'] }} btn-sm w-100">
                            <i class='bx bx-right-arrow-alt me-1'></i>Lihat Soal
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class='bx bx-info-circle fs-1 mb-3'></i>
                    <h5>Belum ada Tipe Soal</h5>
                    <p class="mb-0">Administrator belum menambahkan master tipe soal. Silakan hubungi Administrator.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endsection

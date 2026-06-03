@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item active">{{ $mapel->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            Bank Soal / {{ $mapel->name }} / Pilih Modul Pembelajaran
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
        @forelse($lessons as $lesson)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class='bx bx-book-open fs-4'></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $lesson->name }}</h5>
                                <small class="text-muted">Mapel: {{ $mapel->name }}</small>
                            </div>
                        </div>
                        <a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}" class="btn btn-info btn-sm w-100">
                            <i class='bx bx-right-arrow-alt me-1'></i>Lihat Kategori Soal
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada paket pembelajaran yang tersedia. Hubungi administrator untuk mendapatkan akses paket pembelajaran.
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

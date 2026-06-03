@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.rekapnilai', $serial->id) }}">Rekap Nilai</a></li>
            <li class="breadcrumb-item active">{{ $classroom->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class='bx bx-book-content text-primary me-2'></i>Pilih Mata Pelajaran (Modul)</h3>
            <p class="text-muted mb-0">Pilih mata pelajaran untuk melihat rekap nilai siswa di kelas <strong>{{ $classroom->name }}</strong>.</p>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        @forelse($lessons as $lesson)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 hover-elevate">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class='bx bx-book-content me-2'></i>{{ $lesson->name }}
                    </h5>
                </div>
                
                <div class="card-footer bg-transparent border-top p-3 mt-auto">
                    <a href="{{ route('guru.rekapnilai.lesson', [$serial->id, $classroom->id, $lesson->id]) }}" class="btn btn-primary w-100">
                        <i class='bx bx-bar-chart-alt-2 me-1'></i>Lihat Rekap Nilai
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-book-content text-muted' style="font-size: 64px; opacity: 0.5;"></i>
                    <h5 class="mt-4 mb-2">Belum Ada Mata Pelajaran</h5>
                    <p class="text-muted mb-0">Kelas ini tidak terkait dengan mata pelajaran manapun.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    .hover-elevate {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .hover-elevate:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection

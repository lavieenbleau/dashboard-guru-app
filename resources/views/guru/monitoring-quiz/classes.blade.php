@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class='bx bx-desktop text-primary me-2'></i>Monitoring Kuis</h3>
            <p class="text-muted mb-0">Pilih kelas untuk melihat progres pengerjaan kuis siswa.</p>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="row g-4">
        @forelse($classStats as $stat)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 hover-elevate">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class='bx bx-group me-2'></i>{{ $stat['name'] }}
                    </h5>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $stat['total_students'] }} Siswa</h6>
                            <small class="text-muted">Total terdaftar</small>
                        </div>
                    </div>


                </div>
                <div class="card-footer bg-transparent border-top p-3">
                    <a href="{{ route('guru.monitoring-quiz.products', $stat['name']) }}" class="btn btn-primary w-100">
                        <i class='bx bx-right-arrow-alt me-1'></i>Lihat Mata Pelajaran (Modul)
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-folder-open text-muted' style="font-size: 64px; opacity: 0.5;"></i>
                    <h5 class="mt-4 mb-2">Belum Ada Kelas</h5>
                    <p class="text-muted mb-0">Anda belum membuat atau mengelola kelas manapun.</p>
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

@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.monitoring-quiz') }}">Monitoring Kuis</a></li>
            <li class="breadcrumb-item active">{{ $kelasName }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class='bx bx-book-content text-primary me-2'></i>Pilih Mata Pelajaran (Modul)</h3>
            <p class="text-muted mb-0">Pilih mata pelajaran untuk melihat detail monitoring siswa di kelas <strong>{{ $kelasName }}</strong>.</p>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        @forelse($productStats as $stat)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 hover-elevate">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class='bx bx-book-content me-2'></i>{{ $stat['name'] }}
                    </h5>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $stat['total_students'] }} Siswa</h6>
                            <small class="text-muted">Dalam mata pelajaran ini</small>
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <h4 class="mb-1 text-primary">{{ $stat['total_quiz'] ?? 0 }}</h4>
                        <p class="mb-0 text-muted small">Kuis Tersedia</p>
                        @if(isset($stat['last_update']) && $stat['last_update'])
                            <p class="mb-0 mt-2 text-muted small"><i class='bx bx-calendar-edit me-1'></i>Terakhir Update: {{ \Carbon\Carbon::parse($stat['last_update'])->format('d M Y') }}</p>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top p-3">
                    <a href="{{ route('guru.monitoring-quiz.students', [$kelasName, $stat['serial_id']]) }}{{ !empty($stat['lesson_id']) ? '?lesson_id=' . $stat['lesson_id'] : '' }}" class="btn btn-primary w-100">
                        <i class='bx bx-search-alt me-1'></i>Monitoring Siswa
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

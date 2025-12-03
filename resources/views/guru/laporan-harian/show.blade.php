@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('guru.dashboard', $serial->id) }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.laporanharian', $serial->id) }}">Laporan Harian</a></li>
            <li class="breadcrumb-item active">{{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM YYYY') }}</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h4 class="mb-1"><i class='bx bx-calendar text-primary me-2'></i>{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}</h4>
        <p class="text-muted mb-0">{{ $activities->count() }} pengumpulan tugas</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @forelse($activities as $activity)
                <div class="d-flex mb-3 pb-3 border-bottom">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            {{ strtoupper(substr($activity->student_name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0">{{ $activity->student_name }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->isoFormat('HH:mm') }}</small>
                            </div>
                            <span class="badge bg-label-success">Mengumpulkan Tugas</span>
                        </div>
                        <div class="mb-2">
                            <strong class="text-primary">{{ $activity->task_title }}</strong>
                        </div>
                        @if($activity->submission_description)
                            <p class="mb-1 small">{{ $activity->submission_description }}</p>
                        @endif
                        @if($activity->attachment)
                            <a href="{{ $activity->attachment }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external me-1'></i>Lihat Lampiran
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class='bx bx-folder-open fs-1 text-muted mb-3 d-block'></i>
                    <p class="text-muted">Belum ada pengumpulan tugas pada tanggal ini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

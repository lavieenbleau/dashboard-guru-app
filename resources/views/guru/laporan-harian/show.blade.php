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
        <p class="text-muted mb-0">{{ $activities->count() }} aktivitas siswa (tugas & soal)</p>
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
                @php
                    if ($activity->source_type === 'task') {
                        $url = route('guru.laporanharian.review', [$serial->id, $activity->id]);
                    } else {
                        $url = route('guru.soal.student-answer-detail', [$activity->lesson_id ?? 0, $activity->exercise_id ?? 0, $activity->student_id]);
                    }
                @endphp
                <a href="{{ $url }}" class="d-flex mb-3 pb-3 border-bottom text-decoration-none text-dark" style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-{{ $activity->badge_color ?? 'success' }}">
                            {{ strtoupper(substr($activity->student_name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0 text-dark">{{ $activity->student_name }}</h6>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->isoFormat('HH:mm') }}
                                    @if($activity->student_nis)
                                        · NIS: {{ $activity->student_nis }}
                                    @endif
                                    @if($activity->classroom_name)
                                        · {{ $activity->classroom_name }}
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge bg-{{ $activity->badge_color }}">{{ $activity->activity_type }}</span>
                                @if($activity->point === null)
                                    <span class="badge bg-label-warning">Belum Dinilai</span>
                                @else
                                    <span class="badge bg-label-success">Sudah Dinilai : {{ $activity->point }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2">
                            <strong class="text-primary">{{ $activity->task_title }}</strong>
                            @if($activity->lesson_name)
                                <br><small class="text-muted">{{ $activity->lesson_name }}</small>
                            @endif
                        </div>
                        @if($activity->submission_description)
                            <p class="mb-1 small text-dark">{{ Str::limit($activity->submission_description, 150) }}</p>
                        @endif
                        @if($activity->attachment)
                            <span class="btn btn-sm btn-outline-primary me-2 mt-2">
                                <i class='bx bx-link-external me-1'></i>Ada Lampiran
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-center py-5">
                    <i class='bx bx-folder-open fs-1 text-muted mb-3 d-block'></i>
                    <p class="text-muted">Belum ada aktivitas siswa pada tanggal ini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

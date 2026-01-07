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
                <div class="d-flex mb-3 pb-3 border-bottom">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-{{ $activity->badge_color ?? 'success' }}">
                            {{ strtoupper(substr($activity->student_name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0">{{ $activity->student_name }}</h6>
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
                                @if($activity->point)
                                    <span class="badge bg-label-success">Nilai: {{ $activity->point }}</span>
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
                            <p class="mb-1 small">{{ Str::limit($activity->submission_description, 150) }}</p>
                        @endif
                        @if($activity->attachment)
                            <a href="{{ $activity->attachment }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                <i class='bx bx-link-external me-1'></i>Lihat Lampiran
                            </a>
                        @endif
                        @if($activity->lesson_category == 3 || $activity->lesson_category == 2)
                            <button type="button" class="btn btn-sm btn-outline-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#gradeModal{{ $activity->id }}">
                                <i class='bx bx-edit me-1'></i>{{ $activity->point ? 'Ubah Nilai' : 'Beri Nilai' }}
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Grade Modal -->
                @if($activity->lesson_category == 3 || $activity->lesson_category == 2)
                    <div class="modal fade" id="gradeModal{{ $activity->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Beri Nilai - {{ $activity->student_name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('guru.laporan.grade', [$serial->id, $activity->id]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="source_type" value="{{ $activity->source_type }}">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ $activity->activity_type }}:</label>
                                            <p>{{ $activity->task_title }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Jawaban:</label>
                                            <div class="border rounded p-3" style="background: #f8f9fa; white-space: pre-wrap; max-height: 200px; overflow-y: auto;">{{ $activity->submission_description }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="point{{ $activity->id }}" class="form-label fw-bold">Nilai (0-100):</label>
                                            <input type="number" class="form-control" id="point{{ $activity->id }}" 
                                                   name="point" value="{{ $activity->point }}" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">
                                            <i class='bx bx-save'></i> Simpan Nilai
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
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

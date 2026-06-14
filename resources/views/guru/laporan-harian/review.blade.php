@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('guru.dashboard', $serialModel->id) }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.laporanharian', $serialModel->id) }}?date={{ $date }}">Laporan Harian</a></li>
            <li class="breadcrumb-item active">Review Tugas</li>
        </ol>
    </nav>

    <!-- Header & Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1"><i class='bx bx-check-shield text-primary me-2'></i>Review Tugas Siswa</h4>
            <p class="text-muted mb-0">Periksa jawaban dan berikan nilai untuk aktivitas ini.</p>
        </div>
        <div class="btn-group">
            @if($prevTaskId)
                <a href="{{ route('guru.laporanharian.review', [$serialModel->id, $prevTaskId]) }}" class="btn btn-outline-primary">
                    <i class='bx bx-chevron-left me-1'></i> Sebelumnya
                </a>
            @else
                <button class="btn btn-outline-secondary" disabled>
                    <i class='bx bx-chevron-left me-1'></i> Sebelumnya
                </button>
            @endif
            
            @if($nextTaskId)
                <a href="{{ route('guru.laporanharian.review', [$serialModel->id, $nextTaskId]) }}" class="btn btn-outline-primary">
                    Berikutnya <i class='bx bx-chevron-right ms-1'></i>
                </a>
            @else
                <button class="btn btn-outline-secondary" disabled>
                    Berikutnya <i class='bx bx-chevron-right ms-1'></i>
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Column -->
        <div class="col-md-4 mb-4">
            <div class="card mb-4 shadow-none border">
                <div class="card-header border-bottom">
                    <h6 class="mb-0">Informasi Siswa</h6>
                </div>
                <div class="card-body pt-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-2">
                            <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($task->student_name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $task->student_name }}</h6>
                            <small class="text-muted">NIS: {{ $task->student_nis ?? '-' }}</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class='bx bx-home-circle text-muted me-2'></i> Kelas: <span class="fw-medium">{{ $task->classroom_name ?? '-' }}</span></li>
                        <li class="mb-2"><i class='bx bx-calendar text-muted me-2'></i> Tgl Submit: <span class="fw-medium">{{ \Carbon\Carbon::parse($task->created_at)->isoFormat('D MMMM YYYY') }}</span></li>
                        <li><i class='bx bx-time-five text-muted me-2'></i> Jam Submit: <span class="fw-medium">{{ \Carbon\Carbon::parse($task->created_at)->isoFormat('HH:mm') }} WIB</span></li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-none border">
                <div class="card-header border-bottom">
                    <h6 class="mb-0">Informasi Tugas</h6>
                </div>
                <div class="card-body pt-3">
                    <h6 class="text-primary mb-2">{{ $task->task_title }}</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class='bx bx-book-open text-muted me-2'></i> Mapel: <span class="fw-medium">{{ $task->lesson_name ?? '-' }}</span></li>
                        <li class="mb-2"><i class='bx bx-user text-muted me-2'></i> Guru: <span class="fw-medium">{{ $teacher_name }}</span></li>
                        <li><i class='bx bx-calendar-plus text-muted me-2'></i> Tgl Dibuat: <span class="fw-medium">{{ \Carbon\Carbon::parse($task->post_created_at)->isoFormat('D MMMM YYYY') }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Jawaban & Penilaian Column -->
        <div class="col-md-8 mb-4">
            <!-- Jawaban Siswa -->
            <div class="card mb-4 shadow-none border">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Jawaban Siswa</h6>
                    @if($task->point === null)
                        <span class="badge bg-label-warning">Belum Dinilai</span>
                    @else
                        <span class="badge bg-label-success">Sudah Dinilai : {{ $task->point }}</span>
                    @endif
                </div>
                <div class="card-body pt-3">
                    <div class="p-3 bg-lighter rounded" style="white-space: pre-wrap; font-size: 14px; color: #334155; min-height: 150px;">{{ $task->description ?? 'Tidak ada teks jawaban yang dilampirkan.' }}</div>
                    
                    @if($task->attachment)
                        <div class="mt-4 border-top pt-3">
                            <h6 class="mb-3">Lampiran:</h6>
                            @php
                                $ext = strtolower(pathinfo($task->attachment, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isPdf = $ext === 'pdf';
                            @endphp
                            
                            @if($isImage)
                                <img src="{{ $task->attachment }}" class="img-fluid rounded" alt="Lampiran Tugas" style="max-height: 400px;">
                            @elseif($isPdf)
                                <iframe src="{{ $task->attachment }}" width="100%" height="500px" class="border rounded"></iframe>
                            @else
                                <div class="d-flex align-items-center p-3 border rounded bg-lighter">
                                    <i class='bx bxs-file-archive fs-1 text-primary me-3'></i>
                                    <div>
                                        <h6 class="mb-1">File Terlampir</h6>
                                        <a href="{{ $task->attachment }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class='bx bx-download me-1'></i> Unduh Lampiran
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Penilaian -->
            <form action="{{ route('guru.laporan.grade', [$serialModel->id, $task->id]) }}" method="POST" id="formPenilaian">
                @csrf
                <input type="hidden" name="source_type" value="task">
                
                <div class="card shadow-none border border-primary">
                    <div class="card-header bg-label-primary border-bottom">
                        <h6 class="mb-0 text-primary">Penilaian</h6>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="point" class="form-label fw-bold text-dark">Nilai (0 - 100) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-lg" id="point" name="point" value="{{ $task->point }}" min="0" max="100" placeholder="Masukkan nilai..." required autofocus>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="catatan" class="form-label fw-bold text-dark">Catatan Guru (Opsional)</label>
                                <textarea class="form-control" id="catatan" rows="3" placeholder="Fitur catatan guru sedang dalam pengembangan..." disabled></textarea>
                                <small class="text-muted mt-1 d-block"><i class='bx bx-info-circle'></i> Saat ini sistem hanya menyimpan Nilai utama.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top bg-lighter d-flex justify-content-end gap-2">
                        <a href="{{ route('guru.laporanharian', $serialModel->id) }}?date={{ $date }}" class="btn btn-secondary">
                            Batal & Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">
                            <i class='bx bx-save me-1'></i> Simpan Nilai
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('formPenilaian').addEventListener('submit', function() {
        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = "<i class='bx bx-loader bx-spin me-1'></i> Menyimpan...";
    });
</script>
@endsection

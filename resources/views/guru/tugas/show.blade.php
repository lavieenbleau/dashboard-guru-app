@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}">{{ $mapel->name }}</a></li>
            <li class="breadcrumb-item active">{{ $task->title }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-edit text-primary me-2'></i>{{ $task->title }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.tugas.edit', [$serial->id, $mapel->id, $task->id]) }}" class="btn btn-primary">
                <i class='bx bx-edit me-1'></i>Edit Tugas
            </a>
            <a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i>Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Tugas -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informasi Tugas</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Mata Pelajaran</strong>
                            {{ $mapel->name }}
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Serial</strong>
                            {{ $serial->product->name }}
                        </li>
                        @if($task->deadline)
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Deadline Pengumpulan</strong>
                            <div class="d-flex align-items-center">
                                <i class='bx bx-time-five me-2 text-warning'></i>
                                <span>{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y, H:i') }}</span>
                            </div>
                            @php
                                $now = \Carbon\Carbon::now();
                                $deadline = \Carbon\Carbon::parse($task->deadline);
                                $diff = $now->diffInHours($deadline, false);
                            @endphp
                            @if($diff < 0)
                                <small class="text-danger">Sudah lewat {{ abs($diff) }} jam</small>
                            @elseif($diff < 24)
                                <small class="text-warning">Tersisa {{ $diff }} jam lagi</small>
                            @else
                                <small class="text-muted">Tersisa {{ floor($diff/24) }} hari lagi</small>
                            @endif
                        </li>
                        @endif
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Dibuat</strong>
                            {{ $task->created_at->format('d M Y H:i') }}
                        </li>
                        <li class="mb-0">
                            <strong class="d-block text-muted small">Terakhir Diupdate</strong>
                            {{ $task->updated_at->format('d M Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Statistik -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Statistik</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total Siswa Mengumpulkan</span>
                        <strong>0</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Rata-rata Nilai</span>
                        <strong>-</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Nilai Tertinggi</span>
                        <strong>-</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Belum Mengumpulkan</span>
                        <strong class="text-danger">0</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Konten Tugas -->
        <div class="col-md-8">
            <!-- Deskripsi & File -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Deskripsi Tugas</h5>
                </div>
                <div class="card-body">
                    @if($task->description)
                        <div class="mb-3">{!! nl2br(e($task->description)) !!}</div>
                    @else
                        <p class="text-muted">Tidak ada deskripsi untuk tugas ini.</p>
                    @endif
                    
                    @if($task->attachment)
                        <div class="alert alert-light d-flex align-items-center mb-3">
                            <i class='bx bx-file text-primary me-3' style="font-size: 2rem;"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Lampiran File</h6>
                                <small class="text-muted">{{ basename($task->attachment) }}</small>
                            </div>
                            <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="btn btn-sm btn-primary" download>
                                <i class='bx bx-download me-1'></i>Download
                            </a>
                        </div>
                    @endif
                    
                    @if($task->link)
                        <div class="mt-3">
                            <a href="{{ $task->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external me-1'></i>Buka Link Materi
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shared to Classes -->
            @if($task->shared_to_classes && count($task->shared_to_classes) > 0)
                @php
                    $sharedClasses = \App\Models\Classroom::whereIn('id', $task->shared_to_classes)->get();
                @endphp
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Dibagikan ke Kelas</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($sharedClasses as $classroom)
                                <span class="badge bg-label-primary">{{ $classroom->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

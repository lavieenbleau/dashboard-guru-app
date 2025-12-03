@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.tema', [$serial->id, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.list', [$serial->id, $tema->id, $subtema->id]) }}">{{ $subtema->name }}</a></li>
            <li class="breadcrumb-item active">{{ $lesson->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-edit text-primary me-2'></i>{{ $lesson->name }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.tugas.edit', [$serial->id, $tema->id, $subtema->id, $lesson->id]) }}" class="btn btn-primary">
                <i class='bx bx-edit me-1'></i>Edit Tugas
            </a>
            <a href="{{ route('guru.tugas.list', [$serial->id, $tema->id, $subtema->id]) }}" class="btn btn-outline-secondary">
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
                            {{ $tema->name }}
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Sub Tema</strong>
                            {{ $subtema->name }}
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Semester</strong>
                            <span class="badge bg-label-primary">Semester {{ $lesson->semester ?? 1 }}</span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Dibuat</strong>
                            {{ $lesson->created_at->format('d M Y H:i') }}
                        </li>
                        <li class="mb-0">
                            <strong class="d-block text-muted small">Terakhir Diupdate</strong>
                            {{ $lesson->updated_at->format('d M Y H:i') }}
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
            @php
                $descItem = $items->where('title', 'Deskripsi Tugas')->first();
            @endphp
            
            @if($descItem)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Deskripsi Tugas</h5>
                </div>
                <div class="card-body">
                    @if($descItem->description)
                        <p class="mb-3">{{ $descItem->description }}</p>
                    @endif
                    
                    @if($descItem->link)
                        <div class="mt-3">
                            <a href="{{ $descItem->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external me-1'></i>Buka Link Materi
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Deskripsi Tugas</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Belum ada deskripsi untuk tugas ini. Klik tombol "Edit Tugas" untuk menambahkan deskripsi.
                    </p>
                </div>
            </div>
            @endif

            <!-- Soal Tugas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Soal Tugas</h5>
                    <span class="badge bg-label-primary">{{ $items->where('title', '!=', 'Deskripsi Tugas')->count() }} Soal</span>
                </div>
                <div class="card-body">
                    @php
                        $questions = $items->where('title', '!=', 'Deskripsi Tugas');
                    @endphp
                    
                    @if($questions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($questions as $index => $question)
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-label-warning me-3">{{ $index + 1 }}</span>
                                    <div class="flex-grow-1">
                                        {{ $question->description }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class='bx bx-clipboard display-1 text-muted'></i>
                            <p class="text-muted mt-3">Belum ada soal untuk tugas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

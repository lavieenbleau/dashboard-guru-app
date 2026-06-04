@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item active">{{ $lesson->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-task text-warning me-2'></i>Daftar Tugas - {{ $lesson->name }}
        </h4>
        <a href="{{ route('guru.tugas.create', [$serial->id, $lesson->id]) }}" class="btn btn-warning">
            <i class='bx bx-plus me-1'></i>Tambah Tugas
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($tugas as $task)
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class='bx bx-task'></i>
                                    </span>
                                </div>
                                <div>
                                    <a href="{{ route('guru.tugas.show', [$serial->id, $lesson->id, $task->id]) }}" class="text-decoration-none">
                                        <h6 class="mb-0">{{ $task->title }}</h6>
                                    </a>
                                    
                                    <div class="mt-2 mb-1">
                                        <strong class="text-dark d-block">Kelas:</strong>
                                        @if(count($task->shared_classrooms ?? []) > 0)
                                            @php
                                                $classNames = collect($task->shared_classrooms ?? [])->pluck('name')->implode(', ');
                                            @endphp
                                            <span class="badge bg-label-success" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $classNames }}">
                                                Dibagikan ke {{ count($task->shared_classrooms ?? []) }} Kelas
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">Belum Ditentukan</span>
                                        @endif
                                    </div>
                                    
                                    <small class="text-muted d-block mt-2">
                                        <strong class="text-dark">Deadline:</strong><br>
                                        @if($task->deadline)
                                            <i class='bx bx-time-five'></i> {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y H:i') }}
                                        @else
                                            Tidak ada deadline
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <x-action-dropdown>
                                        <small class="text-muted">
                                            <strong class="text-dark">Deadline:</strong>
                                            @if($task->deadline)
                                                {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y H:i') }}
                                            @else
                                                Tidak ada deadline
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-2 mb-1">
                                    <strong class="text-dark d-block">Kelas:</strong>
                                    @if(count($task->shared_to_classes ?? []) > 0)
                                        @php
                                            $classNames = collect($task->shared_to_classes ?? [])->map(function($id) use ($classrooms) {
                                                return $classrooms->firstWhere('id', $id)?->name;
                                            })->filter()->implode(', ');
                                        @endphp
                                        <span class="badge bg-label-success" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $classNames }}">
                                            Dibagikan ke {{ count($task->shared_to_classes ?? []) }} Kelas
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning">Belum Dibagikan</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <div class="d-flex flex-column gap-2 h-100 justify-content-center">
                                    <a href="{{ route('guru.tugas.show', [$serial->id, $lesson->id, $task->id]) }}" class="btn btn-primary btn-sm">
                                        <i class='bx bx-show me-1'></i> Detail
                                    </a>
                                    <a href="{{ route('guru.tugas.edit', [$serial->id, $lesson->id, $task->id]) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class='bx bx-edit me-1'></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#distributeModal{{ $task->id }}">
                                        <i class='bx bx-share-alt me-1'></i> {{ count($task->shared_to_classes ?? []) > 0 ? 'Kelola Distribusi' : 'Kelola Distribusi' }}
                                    </button>
                                    <form method="POST" action="{{ route('guru.tugas.destroy', [$serial->id, $lesson->id, $task->id]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini? (Akan terhapus dari semua kelas yang dibagikan)');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                            <i class='bx bx-trash me-1'></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Distribusi Tugas -->
            <div class="modal fade" id="distributeModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('guru.tugas.updateClassroom', [$serial->id, $lesson->id, $task->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Distribusi Tugas: {{ $task->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label d-block text-muted small">Tugas</label>
                                    <h6>{{ $task->title }}</h6>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pilih Kelas <span class="text-danger">*</span></label>
                                    <div class="list-group" style="max-height: 200px; overflow-y: auto;">
                                        @php
                                            $taskClassroomIds = $task->shared_to_classes ?? [];
                                        @endphp
                                        @forelse($classrooms as $cls)
                                            <label class="list-group-item">
                                                <input class="form-check-input me-1" type="checkbox" name="classroom_ids[]" value="{{ $cls->id }}" {{ in_array($cls->id, $taskClassroomIds) ? 'checked' : '' }}>
                                                {{ $cls->name }}
                                            </label>
                                            <div class="alert alert-warning mb-0">
                                                <i class='bx bx-info-circle'></i> Belum ada kelas. Silakan buat kelas terlebih dahulu.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class='bx bx-task display-1 text-muted mb-2'></i>
                        <p class="text-muted m-0">Belum ada tugas.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

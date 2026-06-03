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
                                        @if(count($task->classrooms) > 0)
                                            @php
                                                $classNames = $task->classrooms->pluck('name')->implode(', ');
                                            @endphp
                                            <span class="badge bg-label-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $classNames }}">
                                                Dibagikan ke {{ count($task->classrooms) }} Kelas
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
                                <li>
                                    <a class="dropdown-item" href="{{ route('guru.tugas.show', [$serial->id, $lesson->id, $task->id]) }}">
                                        <i class='bx bx-show me-1'></i> Lihat
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('guru.tugas.edit', [$serial->id, $lesson->id, $task->id]) }}">
                                        <i class='bx bx-edit me-1'></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalShare{{ $task->id }}">
                                        <i class='bx bx-share-alt me-1'></i> {{ count($task->classrooms) > 0 ? 'Kelola Distribusi' : 'Kelola Distribusi' }}
                                    </button>
                                </li>

                                <li>
                                    <form action="{{ route('guru.tugas.destroy', [$serial->id, $lesson->id, $task->id]) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class='bx bx-trash me-1'></i> Hapus
                                        </button>
                                    </form>
                                </li>
                            </x-action-dropdown>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Ubah Kelas -->
            <div class="modal fade" id="modalShare{{ $task->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('guru.tugas.update-classroom', [$serial->id, $lesson->id, $task->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Kelola Distribusi</h5>
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
                                            $taskClassroomIds = $task->classrooms->pluck('id')->toArray();
                                        @endphp
                                        @forelse($classrooms as $cls)
                                            <label class="list-group-item">
                                                <input class="form-check-input me-1" type="checkbox" name="classroom_ids[]" value="{{ $cls->id }}" {{ in_array($cls->id, $taskClassroomIds) ? 'checked' : '' }}>
                                                {{ $cls->name }}
                                            </label>
                                        @empty
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

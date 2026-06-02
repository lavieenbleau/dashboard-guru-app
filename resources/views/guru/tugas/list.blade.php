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
                                    <small class="text-muted">
                                        {{ $task->created_at->format('d M Y') }}
                                        @if($task->deadline)
                                            • <i class='bx bx-time-five'></i> Deadline: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y H:i') }}
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

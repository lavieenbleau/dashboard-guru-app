@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item active">{{ $mapel->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-task text-warning me-2'></i>Daftar Tugas - {{ $mapel->name }}
        </h4>
        <a href="{{ route('guru.tugas.create', [$serial->id, $mapel->id]) }}" class="btn btn-warning">
            <i class='bx bx-plus me-1'></i>Tambah Tugas
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @forelse($tugas as $task)
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class='bx bx-task'></i>
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('guru.tugas.show', [$serial->id, $mapel->id, $task->id]) }}" class="text-decoration-none">
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
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                            <i class='bx bx-dots-vertical-rounded'></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('guru.tugas.show', [$serial->id, $mapel->id, $task->id]) }}">
                                    <i class='bx bx-show me-1'></i> Lihat
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('guru.tugas.edit', [$serial->id, $mapel->id, $task->id]) }}">
                                    <i class='bx bx-edit me-1'></i> Edit
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('guru.tugas.destroy', [$serial->id, $mapel->id, $task->id]) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class='bx bx-trash me-1'></i> Hapus
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class='bx bx-task display-1 text-muted'></i>
                    <p class="text-muted mt-2">Belum ada tugas.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

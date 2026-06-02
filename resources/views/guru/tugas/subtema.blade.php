@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item active">{{ $tema->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-task text-warning me-2'></i>Daftar Tugas - {{ $tema->name }}
        </h4>
    </div>

    @forelse($subthemes as $subtheme)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $subtheme->name }}</h5>
                </div>
                <a href="{{ route('guru.tugas.create', [$serial->id, $tema->id, $subtheme->id]) }}" class="btn btn-sm btn-warning">
                    <i class='bx bx-plus me-1'></i>Tambah Tugas
                </a>
            </div>
            <div class="card-body">
                @php
                    $lessons = $tugasData[$subtheme->id] ?? collect();
                @endphp
                
                @if($lessons->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($lessons as $lesson)
                            <div class="list-group-item px-0 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class='bx bx-task'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ route('guru.tugas.show', [$serial->id, $tema->id, $subtheme->id, $lesson->id]) }}" class="text-decoration-none">
                                                <h6 class="mb-0">{{ $lesson->name }}</h6>
                                            </a>
                                            <small class="text-muted">
                                                Semester {{ $lesson->semester }} • 
                                                {{ $lesson->created_at->format('d M Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <x-action-dropdown>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('guru.tugas.show', [$serial->id, $tema->id, $subtheme->id, $lesson->id]) }}">
                                                    <i class='bx bx-show me-1'></i> Lihat
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('guru.tugas.edit', [$serial->id, $tema->id, $subtheme->id, $lesson->id]) }}">
                                                    <i class='bx bx-edit me-1'></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('guru.tugas.destroy', [$serial->id, $tema->id, $subtheme->id, $lesson->id]) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
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
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class='bx bx-task display-4 text-muted'></i>
                        <p class="text-muted mt-2 mb-3">Belum ada tugas untuk sub tema ini.</p>
                        <a href="{{ route('guru.tugas.create', [$serial->id, $tema->id, $subtheme->id]) }}" class="btn btn-sm btn-warning">
                            <i class='bx bx-plus me-1'></i>Tambah Tugas Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class='bx bx-book-content display-1 text-muted'></i>
                <h5 class="mt-3">Belum Ada Sub Tema</h5>
                <p class="text-muted">Belum ada sub tema untuk {{ $tema->name }}</p>
            </div>
        </div>
    @endforelse
</div>
@endsection

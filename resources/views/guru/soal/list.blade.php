@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.tema', [$serial->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
            <li class="breadcrumb-item active">{{ $tema->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-file-blank text-{{ $categoryInfo['color'] }} me-2'></i>{{ $categoryInfo['name'] }} - {{ $tema->name }}
        </h4>
        <a href="{{ route('guru.soal.create', [$serial->id, $category, $tema->id]) }}" class="btn btn-{{ $categoryInfo['color'] }}">
            <i class='bx bx-plus me-1'></i>Tambah Soal
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
            @forelse($lessons as $lesson)
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-{{ $categoryInfo['color'] }}">
                                <i class='bx bx-file-blank'></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">
                                <a href="{{ route('guru.soal.show', [$serial->id, $category, $tema->id, $lesson->id]) }}" class="text-decoration-none text-dark">
                                    {{ $lesson->name }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                {{ $lesson->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                    <x-action-dropdown>
                            <li>
                                <a class="dropdown-item" href="{{ route('guru.soal.show', [$serial->id, $category, $tema->id, $lesson->id]) }}">
                                    <i class='bx bx-show me-1'></i> Lihat
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('guru.soal.edit', [$serial->id, $category, $tema->id, $lesson->id]) }}">
                                    <i class='bx bx-edit me-1'></i> Edit
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('guru.soal.destroy', [$serial->id, $category, $tema->id, $lesson->id]) }}" method="POST" onsubmit="confirmSubmit(event, 'Konfirmasi Hapus', 'Hapus soal ini?', 'Ya, Hapus', true)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class='bx bx-trash me-1'></i> Hapus
                                    </button>
                                </form>
                            </li>
                        </x-action-dropdown>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class='bx bx-file-blank display-1 text-muted'></i>
                    <p class="text-muted mt-2">Belum ada soal.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.laporanharian', $serial->id) }}">Laporan Harian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.laporanharian.tema', [$serial->id, $mapel->id]) }}">{{ $mapel->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.laporanharian.subtema', [$serial->id, $mapel->id, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item active">{{ $subtema->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-file-find text-dark me-2'></i>Daftar Laporan Harian
        </h4>
        <a href="{{ route('guru.laporanharian.create', [$serial->id, $mapel->id, $tema->id, $subtema->id]) }}" class="btn btn-dark">
            <i class='bx bx-plus me-1'></i>Tambah Laporan Harian
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
                            <span class="avatar-initial rounded bg-label-dark">
                                <i class='bx bx-file-find'></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $lesson->name }}</h6>
                            <small class="text-muted">
                                Semester {{ $lesson->semester }} • 
                                {{ $lesson->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                    <x-action-dropdown>
                            <li>
                                <a class="dropdown-item" href="{{ route('guru.laporanharian.edit', [$serial->id, $mapel->id, $tema->id, $subtema->id, $lesson->id]) }}">
                                    <i class='bx bx-edit me-1'></i> Edit
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('guru.laporanharian.destroy', [$serial->id, $mapel->id, $tema->id, $subtema->id, $lesson->id]) }}" method="POST" onsubmit="confirmSubmit(event, 'Konfirmasi Hapus', 'Hapus laporan harian ini?', 'Ya, Hapus', true)">
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
                    <i class='bx bx-file-find display-1 text-muted'></i>
                    <p class="text-muted mt-2">Belum ada laporan harian.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

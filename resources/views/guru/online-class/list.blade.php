@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.onlineclass', $serial->id) }}">Online Class</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.onlineclass.tema', [$serial->id, $mapel->id]) }}">{{ $mapel->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.onlineclass.subtema', [$serial->id, $mapel->id, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item active">{{ $subtema->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-laptop text-success me-2'></i>Daftar Online Class
        </h4>
        <a href="{{ route('guru.onlineclass.create', [$serial->id, $mapel->id, $tema->id, $subtema->id]) }}" class="btn btn-success">
            <i class='bx bx-plus me-1'></i>Tambah Online Class
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
                            <span class="avatar-initial rounded bg-label-success">
                                <i class='bx bx-laptop'></i>
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
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                            <i class='bx bx-dots-vertical-rounded'></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('guru.onlineclass.edit', [$serial->id, $mapel->id, $tema->id, $subtema->id, $lesson->id]) }}">
                                    <i class='bx bx-edit me-1'></i> Edit
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('guru.onlineclass.destroy', [$serial->id, $mapel->id, $tema->id, $subtema->id, $lesson->id]) }}" method="POST" onsubmit="return confirm('Hapus online class ini?')">
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
                    <i class='bx bx-laptop display-1 text-muted'></i>
                    <p class="text-muted mt-2">Belum ada online class.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

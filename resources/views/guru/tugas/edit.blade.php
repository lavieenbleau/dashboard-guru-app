@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.tema', [$serial->id, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.list', [$serial->id, $tema->id, $subtema->id]) }}">{{ $subtema->name }}</a></li>
            <li class="breadcrumb-item active">Edit Tugas</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-edit text-warning me-2'></i>Edit Tugas</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.tugas.update', [$serial->id, $tema->id, $subtema->id, $lesson->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $lesson->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select @error('semester') is-invalid @enderror">
                                <option value="1" {{ old('semester', $lesson->semester) == 1 ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ old('semester', $lesson->semester) == 2 ? 'selected' : '' }}>Semester 2</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class='bx bx-save me-1'></i>Update Tugas
                            </button>
                            <a href="{{ route('guru.tugas.list', [$serial->id, $tema->id, $subtema->id]) }}" class="btn btn-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Informasi</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Tema:</strong><br>
                            {{ $tema->name }}
                        </li>
                        <li class="mb-2">
                            <strong>Sub Tema:</strong><br>
                            {{ $subtema->name }}
                        </li>
                        <li class="mb-2">
                            <strong>Dibuat:</strong><br>
                            {{ $lesson->created_at->format('d M Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

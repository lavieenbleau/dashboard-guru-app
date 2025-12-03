@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Online Class /</span> Tambah Meeting
    </h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('guru.onlineclass.store', $serial->id) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" required placeholder="Contoh: Matematika - Bab 1">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Deskripsi singkat tentang topik pembelajaran...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Platform <span class="text-danger">*</span></label>
                        <select name="platform" class="form-select @error('platform') is-invalid @enderror" required>
                            <option value="">-- Pilih Platform --</option>
                            <option value="zoom" {{ old('platform') == 'zoom' ? 'selected' : '' }}>Zoom</option>
                            <option value="google-meet" {{ old('platform') == 'google-meet' ? 'selected' : '' }}>Google Meet</option>
                            <option value="teams" {{ old('platform') == 'teams' ? 'selected' : '' }}>Microsoft Teams</option>
                            <option value="other" {{ old('platform') == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('platform')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Meeting ID / Kode</label>
                        <input type="text" name="meeting_code" class="form-control @error('meeting_code') is-invalid @enderror" 
                               value="{{ old('meeting_code') }}" placeholder="Contoh: 123 456 7890">
                        @error('meeting_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link Meeting <span class="text-danger">*</span></label>
                    <input type="url" name="meeting_link" class="form-control @error('meeting_link') is-invalid @enderror" 
                           value="{{ old('meeting_link') }}" required placeholder="https://zoom.us/j/123456789">
                    @error('meeting_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                               value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                               value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class='bx bx-save me-1'></i>Simpan Jadwal
                    </button>
                    <a href="{{ route('guru.onlineclass', $serial->id) }}" class="btn btn-secondary">
                        <i class='bx bx-x me-1'></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

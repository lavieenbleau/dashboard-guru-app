@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Online Class /</span> Edit Meeting
    </h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('guru.onlineclass.update', [$serial->id, $meeting->id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="title" class="form-label">Judul Meeting</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $meeting->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="classroom_id" class="form-label">Kelas</label>
                    <select class="form-select @error('classroom_id') is-invalid @enderror" id="classroom_id" name="classroom_id" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id', $meeting->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $meeting->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="platform" class="form-label">Platform</label>
                    <select class="form-select @error('platform') is-invalid @enderror" id="platform" name="platform" required>
                        <option value="">Pilih Platform</option>
                        <option value="Zoom" {{ old('platform', $meeting->platform) == 'Zoom' ? 'selected' : '' }}>Zoom</option>
                        <option value="Google Meet" {{ old('platform', $meeting->platform) == 'Google Meet' ? 'selected' : '' }}>Google Meet</option>
                        <option value="Microsoft Teams" {{ old('platform', $meeting->platform) == 'Microsoft Teams' ? 'selected' : '' }}>Microsoft Teams</option>
                        <option value="Other" {{ old('platform', $meeting->platform) == 'Other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('platform')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="meeting_code" class="form-label">Kode Meeting</label>
                    <input type="text" class="form-control @error('meeting_code') is-invalid @enderror" id="meeting_code" name="meeting_code" value="{{ old('meeting_code', $meeting->meeting_code) }}" required placeholder="123-456-789">
                    @error('meeting_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="meeting_link" class="form-label">Link Meeting</label>
                    <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $meeting->meeting_link) }}" required placeholder="https://zoom.us/j/123456789">
                    @error('meeting_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $meeting->start_time ? \Carbon\Carbon::parse($meeting->start_time)->format('Y-m-d\TH:i') : '') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $meeting->end_time ? \Carbon\Carbon::parse($meeting->end_time)->format('Y-m-d\TH:i') : '') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('guru.onlineclass', $serial->id) }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

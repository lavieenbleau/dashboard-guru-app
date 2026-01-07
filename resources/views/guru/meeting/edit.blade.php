@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.meeting', $serial->id) }}">Kelas Online</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.meeting.show', [$serial->id, $meeting->id]) }}">{{ $meeting->title }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Meeting</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.meeting.update', [$serial->id, $meeting->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Judul Meeting -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Meeting <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $meeting->title) }}" 
                                   placeholder="Contoh: Kelas Matematika - Aljabar" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Deskripsi singkat tentang meeting ini...">{{ old('description', $meeting->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Mata Pelajaran -->
                            <div class="col-md-6 mb-3">
                                <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                                <select class="form-select @error('mapel_id') is-invalid @enderror" id="mapel_id" name="mapel_id">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach($mapels as $mapel)
                                        <option value="{{ $mapel->id }}" {{ old('mapel_id', $meeting->mapel_id) == $mapel->id ? 'selected' : '' }}>
                                            {{ $mapel->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mapel_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kelas -->
                            <div class="col-md-6 mb-3">
                                <label for="classroom_id" class="form-label">Kelas</label>
                                <select class="form-select @error('classroom_id') is-invalid @enderror" id="classroom_id" name="classroom_id">
                                    <option value="">-- Pilih Kelas --</option>
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
                        </div>

                        <!-- Platform (readonly jika sudah dibuat) -->
                        <div class="mb-3">
                            <label class="form-label">Platform</label>
                            <input type="text" class="form-control" value="{{ ucfirst($meeting->platform) }}" disabled>
                            <small class="text-muted">Platform tidak dapat diubah setelah meeting dibuat</small>
                        </div>

                        <!-- Meeting Link (untuk platform eksternal) -->
                        @if($meeting->platform !== 'jitsi')
                        <div class="mb-3">
                            <label for="meeting_link" class="form-label">Link Meeting</label>
                            <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" 
                                   id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $meeting->meeting_link) }}" 
                                   placeholder="https://zoom.us/j/123456789">
                            @error('meeting_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="row">
                            <!-- Waktu Mulai -->
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time', $meeting->start_time->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Waktu Selesai -->
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time', $meeting->end_time->format('Y-m-d\TH:i')) }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('guru.meeting.show', [$serial->id, $meeting->id]) }}" class="btn btn-label-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-label-warning">
                <div class="card-body">
                    <h6 class="mb-3"><i class='bx bx-info-circle me-2'></i>Perhatian</h6>
                    <ul class="mb-0">
                        <li class="mb-2">Platform meeting tidak dapat diubah setelah dibuat.</li>
                        <li class="mb-2">Pastikan waktu mulai dan selesai sesuai dengan jadwal.</li>
                        <li>Siswa yang sudah mendaftar akan melihat perubahan secara otomatis.</li>
                    </ul>
                </div>
            </div>

            @if($meeting->platform === 'jitsi')
            <div class="card mt-3 bg-label-info">
                <div class="card-body">
                    <h6 class="mb-3"><i class='bx bx-key me-2'></i>Kode Meeting</h6>
                    <code class="fs-6">{{ $meeting->meeting_code }}</code>
                    <p class="mt-2 mb-0 small">Kode ini tidak dapat diubah</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update end_time min when start_time changes
    document.getElementById('start_time').addEventListener('change', function() {
        const startTime = new Date(this.value);
        startTime.setMinutes(startTime.getMinutes() - startTime.getTimezoneOffset());
        document.getElementById('end_time').min = startTime.toISOString().slice(0, 16);
    });
});
</script>
@endpush
@endsection

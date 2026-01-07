@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.meeting', $serial->id) }}">Kelas Online</a></li>
            <li class="breadcrumb-item active">Buat Meeting Baru</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Buat Meeting Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.meeting.store', $serial->id) }}" method="POST">
                        @csrf

                        <!-- Judul Meeting -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Meeting <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
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
                                      placeholder="Deskripsi singkat tentang meeting ini...">{{ old('description') }}</textarea>
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
                                        <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
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

                        <!-- Platform -->
                        <div class="mb-3">
                            <label for="platform" class="form-label">Platform <span class="text-danger">*</span></label>
                            <select class="form-select @error('platform') is-invalid @enderror" id="platform" name="platform" required>
                                <option value="jitsi" {{ old('platform', 'jitsi') === 'jitsi' ? 'selected' : '' }}>
                                    Jitsi Meet (Gratis, Terintegrasi)
                                </option>
                                <option value="zoom" {{ old('platform') === 'zoom' ? 'selected' : '' }}>Zoom</option>
                                <option value="gmeet" {{ old('platform') === 'gmeet' ? 'selected' : '' }}>Google Meet</option>
                                <option value="other" {{ old('platform') === 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('platform')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih Jitsi Meet untuk meeting langsung di sistem</small>
                        </div>

                        <!-- Meeting Link (untuk platform eksternal) -->
                        <div class="mb-3" id="meetingLinkContainer" style="display: none;">
                            <label for="meeting_link" class="form-label">Link Meeting</label>
                            <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" 
                                   id="meeting_link" name="meeting_link" value="{{ old('meeting_link') }}" 
                                   placeholder="https://zoom.us/j/123456789">
                            @error('meeting_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan link meeting dari platform yang dipilih</small>
                        </div>

                        <div class="row">
                            <!-- Waktu Mulai -->
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Waktu Selesai -->
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i>Buat Meeting
                            </button>
                            <a href="{{ route('guru.meeting', $serial->id) }}" class="btn btn-label-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-label-info">
                <div class="card-body">
                    <h6 class="mb-3"><i class='bx bx-info-circle me-2'></i>Informasi</h6>
                    <ul class="mb-0">
                        <li class="mb-2"><strong>Jitsi Meet:</strong> Platform gratis yang terintegrasi langsung di sistem. Siswa dapat langsung join dari dashboard mereka.</li>
                        <li class="mb-2"><strong>Platform Lain:</strong> Jika menggunakan Zoom atau Google Meet, Anda perlu memasukkan link meeting manual.</li>
                        <li class="mb-2"><strong>Waktu Meeting:</strong> Pastikan waktu mulai lebih dari waktu sekarang.</li>
                        <li>Meeting akan otomatis masuk daftar siswa di kelas yang dipilih.</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3 bg-label-success">
                <div class="card-body">
                    <h6 class="mb-3"><i class='bx bx-check-circle me-2'></i>Keuntungan Jitsi Meet</h6>
                    <ul class="mb-0">
                        <li>✅ Gratis tanpa batas waktu</li>
                        <li>✅ Tidak perlu aplikasi tambahan</li>
                        <li>✅ Terintegrasi dengan sistem</li>
                        <li>✅ Screen sharing & chat</li>
                        <li>✅ Rekam meeting (opsional)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const platformSelect = document.getElementById('platform');
    const meetingLinkContainer = document.getElementById('meetingLinkContainer');
    const meetingLinkInput = document.getElementById('meeting_link');

    // Show/hide meeting link based on platform
    platformSelect.addEventListener('change', function() {
        if (this.value === 'jitsi') {
            meetingLinkContainer.style.display = 'none';
            meetingLinkInput.removeAttribute('required');
        } else {
            meetingLinkContainer.style.display = 'block';
            if (this.value !== '') {
                meetingLinkInput.setAttribute('required', 'required');
            }
        }
    });

    // Set default min datetime for start_time
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('start_time').min = now.toISOString().slice(0, 16);
    
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

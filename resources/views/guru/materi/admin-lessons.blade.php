@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.admin', $serial->id) }}">Materi Admin</a></li>
            <li class="breadcrumb-item active">{{ $mapel->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Materi Admin - {{ $mapel->name }}</h4>
                <p class="text-muted mb-0">Materi yang disediakan oleh admin untuk {{ $mapel->name }}</p>
            </div>
            <a href="{{ route('guru.materi.admin', $serial->id) }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Lessons List -->
    <div class="row g-3">
        @forelse ($lessons as $lesson)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h5 class="mb-2">{{ $lesson->name }}</h5>
                            <p class="text-muted small mb-2">
                                <i class='bx bx-book-reader'></i> Kelas {{ $lesson->grade }} - Semester {{ $lesson->semester }}
                            </p>
                            @if($lesson->description)
                            <p class="mb-2">{{ $lesson->description }}</p>
                            @endif
                            @if($lesson->file)
                            <p class="mb-2">
                                <i class='bx bx-file'></i>
                                <a href="{{ Storage::url($lesson->file) }}" target="_blank">Lihat File</a>
                            </p>
                            @endif
                            @if($lesson->classrooms && $lesson->classrooms->count() > 0)
                            <span class="badge bg-success" title="{{ $lesson->classrooms->pluck('name')->implode(', ') }}">
                                <i class='bx bx-share'></i> Dibagikan ke {{ $lesson->classrooms->count() }} Kelas
                            </span>
                            @endif
                        </div>
                        <button type="button" class="btn btn-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#shareModal{{ $lesson->id }}">
                            <i class='bx bx-share-alt'></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class='bx bx-info-circle me-2'></i>
                Belum ada materi admin untuk {{ $mapel->name }}
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Share Modals -->
@foreach ($lessons as $lesson)
<div class="modal fade" id="shareModal{{ $lesson->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.materi.admin.share', [$serial->id, $lesson->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3"><strong>{{ $lesson->name }}</strong></p>
                    <p class="text-muted small mb-3">Pilih kelas yang dapat mengakses materi ini:</p>
                    
                    @php
                        $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                        $sharedClassroomIds = $lesson->classrooms->pluck('id')->toArray();
                    @endphp
                    
                    @if($classrooms->count() > 0)
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="selectAll{{ $lesson->id }}"
                                   onclick="toggleAllClassrooms{{ $lesson->id }}(this)">
                            <label class="form-check-label fw-bold" for="selectAll{{ $lesson->id }}">
                                Pilih Semua Kelas
                            </label>
                        </div>
                        <hr class="mb-3">
                    @endif
                    
                    @forelse($classrooms as $classroom)
                    <div class="form-check mb-2">
                        <input class="form-check-input classroom-checkbox-{{ $lesson->id }}" type="checkbox" 
                               name="classrooms[]" 
                               value="{{ $classroom->id }}" 
                               id="classroom{{ $lesson->id }}_{{ $classroom->id }}"
                               {{ in_array($classroom->id, $sharedClassroomIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="classroom{{ $lesson->id }}_{{ $classroom->id }}">
                            {{ $classroom->name }} ({{ $classroom->code }})
                        </label>
                    </div>
                    @empty
                    <div class="alert alert-info">
                        <i class='bx bx-info-circle'></i> Belum ada kelas. Silakan buat kelas terlebih dahulu.
                    </div>
                    @endforelse
                    
                    @if($classrooms->count() > 0)
                        <hr class="mt-3 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   name="as_task" 
                                   value="1"
                                   id="asTask{{ $lesson->id }}">
                            <label class="form-check-label" for="asTask{{ $lesson->id }}">
                                <i class='bx bx-task me-1'></i> Bagikan sebagai Tugas
                            </label>
                            <small class="form-text text-muted d-block mt-1">
                                Jika diaktifkan, materi ini akan dibagikan sebagai tugas yang harus dikerjakan siswa.
                            </small>
                        </div>
                        
                        <div id="taskOptions{{ $lesson->id }}" class="mt-3" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label small">Deadline (opsional)</label>
                                <input type="datetime-local" class="form-control form-control-sm" name="deadline">
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save'></i> Simpan
                    </button>
                </div>
            </form>
            
            <script>
                function toggleAllClassrooms{{ $lesson->id }}(source) {
                    const checkboxes = document.querySelectorAll('.classroom-checkbox-{{ $lesson->id }}');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = source.checked;
                    });
                }
                
                document.getElementById('asTask{{ $lesson->id }}')?.addEventListener('change', function() {
                    const taskOptions = document.getElementById('taskOptions{{ $lesson->id }}');
                    if (this.checked) {
                        taskOptions.style.display = 'block';
                    } else {
                        taskOptions.style.display = 'none';
                    }
                });
            </script>
        </div>
    </div>
</div>
@endforeach
@endsection

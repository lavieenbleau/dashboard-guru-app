@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item active">{{ $categoryInfo['name'] }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">{{ $categoryInfo['name'] }}</h4>
            </div>
            <div class="d-flex gap-2">
                @if($category === 'tambahan')
                    <!-- Tombol Tambah Soal untuk Soal Tambahan -->
                    <a href="{{ route('guru.soal.ai-generator', [$serial->id]) }}" class="btn btn-success">
                        <i class='bx bx-brain me-1'></i>Generate Soal dengan AI
                    </a>
                    <a href="{{ route('guru.soal.create-custom', [$serial->id]) }}" class="btn btn-primary">
                        <i class='bx bx-plus me-1'></i>Tambah Soal Manual
                    </a>
                @else
                    <!-- Tombol Bulk Share untuk soal admin -->
                    <button type="button" class="btn btn-primary" id="bulkShareBtn" style="display: none;">
                        <i class='bx bx-share-alt me-1'></i>Share ke Semua Kelas (<span id="selectedCount">0</span>)
                    </button>
                    <button type="button" class="btn btn-label-secondary" id="toggleSelectBtn">
                        <i class='bx bx-checkbox me-1'></i>Pilih Soal
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Exercises List -->
    <div class="row g-3">
        @forelse ($exercises as $exercise)
        <div class="col-12">
            <div class="card shadow-sm exercise-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 d-flex gap-3">
                            <!-- Checkbox untuk bulk selection -->
                            <div class="form-check exercise-checkbox" style="display: none;">
                                <input class="form-check-input exercise-select" type="checkbox" value="{{ $exercise->id }}" 
                                       id="exercise{{ $exercise->id }}">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h5 class="mb-0">{{ $exercise->title }}</h5>
                                </div>
                                
                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    @if($exercise->lesson && $exercise->lesson->mapel)
                                    <span class="badge bg-label-info">
                                        <i class='bx bx-book me-1'></i>{{ $exercise->lesson->mapel->name }}
                                    </span>
                                    @endif
                                    
                                    @if($exercise->lesson && $exercise->lesson->curriculum)
                                    <span class="badge bg-label-secondary">
                                        {{ $exercise->lesson->curriculum }} {{ $exercise->lesson->grade_level }}
                                    </span>
                                    @endif
                                    
                                    @if($exercise->shared_to_classes)
                                    @php
                                        $sharedCount = count(json_decode($exercise->shared_to_classes, true) ?? []);
                                    @endphp
                                    @if($sharedCount > 0)
                                    <span class="badge bg-label-success">
                                        <i class='bx bx-share-alt'></i> Shared ke {{ $sharedCount }} kelas
                                    </span>
                                    @endif
                                    @endif
                                </div>

                                <small class="text-muted">
                                    <i class='bx bx-time'></i> {{ $exercise->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if($category === 'tambahan')
                            <!-- Edit & Delete untuk Soal Tambahan -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-label-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class='bx bx-dots-vertical-rounded'></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.soal.view-exercise', ['serial' => $serial->id, 'exerciseId' => $exercise->id]) }}">
                                            <i class='bx bx-eye me-2'></i>Lihat Soal
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.soal.edit-custom', [$serial->id, $exercise->id]) }}">
                                            <i class='bx bx-edit me-2'></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('guru.soal.destroy-custom', [$serial->id, $exercise->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Hapus soal ini?')">
                                                <i class='bx bx-trash me-2'></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <!-- Tombol Share untuk soal admin -->
                            <button type="button" class="btn btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#shareModal{{ $exercise->id }}">
                                <i class='bx bx-share-alt'></i> Share
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-folder-open bx-lg text-muted mb-3'></i>
                    <p class="text-muted mb-0">Belum ada soal {{ $categoryInfo['name'] }}.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Share Modals -->
@foreach ($exercises as $exercise)
<div class="modal fade" id="shareModal{{ $exercise->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.soal.share-direct', [$serial->id, $category, $exercise->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3"><strong>{{ $exercise->title }}</strong></p>
                    <p class="text-muted small mb-3">Pilih kelas yang dapat mengakses soal ini:</p>
                    
                    @php
                        $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                        $isSharedToCurrentSerial = $exercise->sharedSerials
                            ? $exercise->sharedSerials->pluck('id')->contains($serial->id)
                            : false;
                        $sharedClassroomIds = $isSharedToCurrentSerial ? $classrooms->pluck('id')->toArray() : [];
                    @endphp
                    
                    @forelse($classrooms as $classroom)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" 
                               name="classrooms[]" 
                               value="{{ $classroom->id }}" 
                               id="classroom{{ $exercise->id }}_{{ $classroom->id }}"
                               {{ in_array($classroom->id, $sharedClassroomIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="classroom{{ $exercise->id }}_{{ $classroom->id }}">
                            {{ $classroom->name }} ({{ $classroom->code }})
                        </label>
                    </div>
                    @empty
                    <div class="alert alert-info">
                        <i class='bx bx-info-circle'></i> Belum ada kelas. Silakan buat kelas terlebih dahulu.
                    </div>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save'></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Form untuk Bulk Share -->
<form id="bulkShareForm" action="{{ route('guru.soal.bulk-share-direct', [$serial->id, $category]) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="exercise_ids" id="exerciseIdsInput">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleSelectBtn');
    const bulkShareBtn = document.getElementById('bulkShareBtn');
    const checkboxes = document.querySelectorAll('.exercise-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkShareForm = document.getElementById('bulkShareForm');
    const exerciseIdsInput = document.getElementById('exerciseIdsInput');
    let isSelectMode = false;

    toggleBtn.addEventListener('click', function() {
        isSelectMode = !isSelectMode;
        
        if (isSelectMode) {
            checkboxes.forEach(cb => cb.style.display = 'block');
            toggleBtn.innerHTML = '<i class="bx bx-x me-1"></i>Batal';
            toggleBtn.classList.remove('btn-label-secondary');
            toggleBtn.classList.add('btn-label-danger');
        } else {
            checkboxes.forEach(cb => {
                cb.style.display = 'none';
                cb.querySelector('input').checked = false;
            });
            toggleBtn.innerHTML = '<i class="bx bx-checkbox me-1"></i>Pilih Soal';
            toggleBtn.classList.remove('btn-label-danger');
            toggleBtn.classList.add('btn-label-secondary');
            bulkShareBtn.style.display = 'none';
            updateSelectedCount();
        }
    });

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.exercise-select:checked');
        const count = selected.length;
        selectedCountSpan.textContent = count;
        
        if (count > 0 && isSelectMode) {
            bulkShareBtn.style.display = 'inline-block';
        } else {
            bulkShareBtn.style.display = 'none';
        }
    }

    document.querySelectorAll('.exercise-select').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    bulkShareBtn.addEventListener('click', function() {
        const selected = Array.from(document.querySelectorAll('.exercise-select:checked'))
            .map(cb => cb.value);
        
        if (selected.length === 0) {
            alert('Pilih minimal satu soal!');
            return;
        }

        if (confirm(`Share ${selected.length} soal ke semua kelas?`)) {
            exerciseIdsInput.value = JSON.stringify(selected);
            bulkShareForm.submit();
        }
    });
});
</script>

@endsection

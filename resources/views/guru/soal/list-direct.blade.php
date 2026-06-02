@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
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
                    <a href="{{ route('guru.soal.ai-generator', [$serial->id, $lesson->id]) }}" class="btn btn-success">
                        <i class='bx bx-brain me-1'></i>Generate Soal dengan AI
                    </a>
                    <a href="{{ route('guru.soal.create-custom', [$serial->id, $lesson->id]) }}" class="btn btn-primary">
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
                                    
                                    @php
                                        $sharedClassroomIds = \Illuminate\Support\Facades\DB::table('share_exercises')
                                            ->where('exercise_id', $exercise->id)
                                            ->whereNotNull('classroom_id')
                                            ->pluck('classroom_id')
                                            ->toArray();
                                        $sharedCount = count($sharedClassroomIds);
                                        $allClassrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                                        $sharedClassroomsList = $allClassrooms->filter(function($c) use ($sharedClassroomIds) {
                                            return in_array($c->id, $sharedClassroomIds);
                                        });
                                    @endphp
                                    
                                    @if($sharedCount > 0)
                                    <span class="badge bg-label-success">
                                        <i class='bx bx-share-alt'></i> Dibagikan ke {{ $sharedCount }} kelas
                                    </span>
                                    @else
                                    <span class="badge bg-label-secondary">
                                        <i class='bx bx-share-alt'></i> Belum dibagikan ke kelas manapun
                                    </span>
                                    @endif
                                </div>
                                
                                @if($sharedCount > 0)
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1">Daftar kelas penerima:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($sharedClassroomsList as $sc)
                                            <span class="badge bg-label-primary" style="font-size: 0.75rem;">{{ $sc->name }} ({{ $sc->code }})</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                </div>

                                <small class="text-muted">
                                    <i class='bx bx-time'></i> {{ $exercise->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-2">
                            @if($category === 'tambahan')
                                <!-- Edit & Delete untuk Soal Tambahan -->
                                <x-action-dropdown>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.soal.view-exercise', ['serial' => $serial->id, 'lesson' => $lesson->id, 'exerciseId' => $exercise->id]) }}">
                                            <i class="bx bx-show me-1"></i> Lihat Soal
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.soal.edit-custom', [$serial->id, $lesson->id, $exercise->id]) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('guru.soal.destroy-custom', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Hapus soal ini?')">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </x-action-dropdown>
                            @endif

                            <button type="button" class="btn {{ $sharedCount > 0 ? 'btn-outline-primary' : 'btn-primary' }}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#shareModal{{ $exercise->id }}">
                                <i class='bx bx-share-alt'></i> {{ $sharedCount > 0 ? 'Kelola Pembagian' : 'Buka Kuis' }}
                            </button>
                        </div>
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
                <h5 class="modal-title">Buka Kuis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.soal.share-direct', [$serial->id, $lesson->id, $category, $exercise->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3"><strong>{{ $exercise->title }}</strong></p>
                    <p class="text-muted small mb-3">Pilih kelas yang dapat mengakses soal ini:</p>
                    
                    @php
                        $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                        $sharedClassroomIds = \Illuminate\Support\Facades\DB::table('share_exercises')
                            ->where('exercise_id', $exercise->id)
                            ->whereNotNull('classroom_id')
                            ->pluck('classroom_id')
                            ->toArray();
                        $sharedCount = count($sharedClassroomIds);
                        $sharedClassroomsList = $classrooms->filter(function($c) use ($sharedClassroomIds) {
                            return in_array($c->id, $sharedClassroomIds);
                        });
                    @endphp

                    @if($sharedCount > 0)
                        <div class="alert alert-success py-2 px-3 mb-3">
                            <i class='bx bx-check-circle me-1'></i> <strong>Saat ini soal dibagikan ke {{ $sharedCount }} kelas.</strong>
                        </div>
                        
                        <div class="mb-3 p-3 bg-lighter rounded">
                            <label class="form-label mb-2 fw-semibold">Dibagikan ke:</label>
                            <ul class="list-unstyled mb-0">
                                @foreach($sharedClassroomsList as $sc)
                                    <li class="mb-1"><i class='bx bx-check text-success me-2'></i>{{ $sc->name }} ({{ $sc->code }})</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        @if($exercise->updated_at)
                        <p class="text-muted small mb-3"><i class='bx bx-time'></i> Terakhir diperbarui: {{ $exercise->updated_at->isoFormat('D MMMM YYYY HH:mm') }}</p>
                        @endif
                    @else
                        <div class="alert alert-secondary py-2 px-3 mb-3">
                            <i class='bx bx-info-circle me-1'></i> <strong>Soal ini belum dibagikan ke kelas manapun.</strong>
                        </div>
                    @endif
                    
                    <p class="text-muted small mb-2 mt-4">Kelola akses kelas (centang untuk memberikan akses):</p>
                    
                    @forelse($classrooms as $classroom)
                        @if(!in_array($classroom->id, $sharedClassroomIds))
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="classrooms[]" 
                                   value="{{ $classroom->id }}" 
                                   id="classroom{{ $exercise->id }}_{{ $classroom->id }}">
                            <label class="form-check-label" for="classroom{{ $exercise->id }}_{{ $classroom->id }}">
                                {{ $classroom->name }} ({{ $classroom->code }})
                            </label>
                        </div>
                        @endif
                    @empty
                    <div class="alert alert-warning">
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
<form id="bulkShareForm" action="{{ route('guru.soal.bulk-share-direct', [$serial->id, $lesson->id, $category]) }}" method="POST" style="display: none;">
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

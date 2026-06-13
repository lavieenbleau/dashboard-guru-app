@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.category', [$serial->id, $type]) }}">{{ $type === 'admin' ? 'Soal Admin' : 'Soal Saya' }}</a></li>
            <li class="breadcrumb-item active">{{ $exerciseType->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">{{ $exerciseType->name }}</h4>
                <p class="text-muted mb-0">{{ $type === 'admin' ? 'Soal dari Admin' : 'Soal Saya' }}</p>
            </div>
            <div class="d-flex gap-2">
                @if($type === 'admin')
                <!-- Tombol Bulk Share untuk Admin Exercises -->
                <button type="button" class="btn btn-primary" id="bulkShareBtn" style="display: none;">
                    <i class='bx bx-share-alt me-1'></i>Share ke Semua Kelas (<span id="selectedCount">0</span>)
                </button>
                <button type="button" class="btn btn-label-secondary" id="toggleSelectBtn">
                    <i class='bx bx-checkbox me-1'></i>Pilih Soal
                </button>
                @else
                <!-- Tombol Tambah untuk Custom Exercises -->
                <a href="#" class="btn btn-primary">
                    <i class='bx bx-plus me-1'></i>Tambah Soal
                </a>
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
            <div class="card shadow-sm exercise-card" data-exercise-id="{{ $exercise->id }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 d-flex gap-3">
                            @if($type === 'admin')
                            <!-- Checkbox untuk bulk selection -->
                            <div class="form-check exercise-checkbox" style="display: none;">
                                <input class="form-check-input exercise-select" type="checkbox" value="{{ $exercise->id }}" 
                                       id="exercise{{ $exercise->id }}">
                            </div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h5 class="mb-0">{{ $exercise->title }}</h5>
                                    @if($type === 'admin')
                                    <span class="badge bg-label-primary">Dari Admin</span>
                                    @endif
                                </div>
                                
                                @php
                                    $competences = collect();
                                    if($exercise->exerciseItems) {
                                        $competences = $exercise->exerciseItems->pluck('competence')->filter()->unique('id');
                                    }
                                @endphp
                                <div class="mb-2">
                                    @if($competences->count() > 0)
                                        @foreach($competences as $kd)
                                            <span class="badge bg-label-info me-1 mb-1" title="{{ $kd->description }}">[KD {{ $kd->point }}{{ $kd->description ? ' - ' . \Illuminate\Support\Str::limit($kd->description, 30) : '' }}]</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-label-warning me-1 mb-1">[Belum Memiliki KD]</span>
                                    @endif
                                </div>
                                
                                @if($exercise->lesson)
                                <p class="text-muted mb-2">
                                    <i class='bx bx-book me-1'></i>{{ $exercise->lesson->name ?? 'No lesson' }}
                                </p>
                                @endif

                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    <span class="badge bg-label-{{ $exerciseType->kode === 'UH' ? 'primary' : ($exerciseType->kode === 'PTS' ? 'warning' : 'danger') }}">
                                        {{ $exerciseType->kode }}
                                    </span>
                                    
                                    @if($type === 'admin' && $exercise->shared_to_classes)
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

                        @if($type === 'admin')
                        <!-- Tombol Share Individual untuk Soal Admin -->
                        <form action="{{ route('guru.soal.share-single', [$serial->id, $type, $exerciseType->id, $exercise->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $exercise->shared_to_classes ? 'btn-success' : 'btn-primary' }}" 
                                    onclick="confirmClick(event, 'Konfirmasi', '{{ $exercise->shared_to_classes ? 'Batalkan share soal ini?' : 'Share soal ke semua kelas?' }}')">
                                <i class='bx {{ $exercise->shared_to_classes ? 'bx-check-circle' : 'bx-share-alt' }} me-1'></i>
                                {{ $exercise->shared_to_classes ? 'Shared' : 'Share' }}
                            </button>
                        </form>
                        @else
                        <!-- Edit/Delete untuk Soal Custom Guru -->
                        <x-action-dropdown>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class='bx bx-edit me-2'></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="#" method="POST" onsubmit="confirmSubmit(event, 'Konfirmasi Hapus', 'Hapus soal ini?', 'Ya, Hapus', true)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class='bx bx-trash me-2'></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </x-action-dropdown>
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
                    <p class="text-muted mb-3">Belum ada soal {{ $exerciseType->name }}.</p>
                    @if($type !== 'admin')
                    <a href="#" class="btn btn-primary">
                        <i class='bx bx-plus me-1'></i>Tambah Soal Pertama
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@if($type === 'admin')
<!-- Form untuk Bulk Share -->
<form id="bulkShareForm" action="{{ route('guru.soal.bulk-share', [$serial->id, $type, $exerciseType->id]) }}" method="POST" style="display: none;">
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

    // Toggle select mode
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
            showError('Pilih minimal satu soal!');
            return;
        }

        showConfirm('Konfirmasi Share', `Share ${selected.length} soal ke semua kelas?`, 'Ya, Lanjutkan').then((result) => {
            if (result.isConfirmed) {
                exerciseIdsInput.value = JSON.stringify(selected);
                bulkShareForm.submit();
            }
        });
    });
});
</script>
@endif

@endsection

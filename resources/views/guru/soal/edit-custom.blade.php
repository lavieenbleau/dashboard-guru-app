@extends('layouts.sneat')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor .note-editing-area { min-height: 150px; }
    .note-editor .note-dropzone { opacity: 0 !important; }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
            <li class="breadcrumb-item active">Edit Soal</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit {{ $categoryInfo['name'] }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.soal.update-custom', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Pilih Paket Materi -->
                        <div class="mb-3">
                            <label class="form-label">Paket Materi</label>
                            <input type="text" class="form-control" value="{{ $lesson->name }} (Mapel: {{ $lesson->mapel->name ?? '-' }})" disabled>
                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                        </div>

                        <!-- Tipe Soal -->
                        <div class="mb-3">
                            <label for="exercise_type_id" class="form-label">Tipe Soal <span class="text-danger">*</span></label>
                            <select class="form-select @error('exercise_type_id') is-invalid @enderror" id="exercise_type_id" name="exercise_type_id" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($exerciseTypes as $type)
                                    <option value="{{ $type->id }}" {{ (old('exercise_type_id', $exercise->exercise_type_id) == $type->id) ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('exercise_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Judul Soal -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Paket Soal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $exercise->title) }}" placeholder="Contoh: Latihan Perkalian" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Waktu Pengerjaan -->
                        <div class="mb-3">
                            <label for="time_limit" class="form-label">Waktu Pengerjaan (Menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" min="1" max="480" value="{{ old('time_limit', $exercise->time_limit) }}" placeholder="Contoh: 45" required>
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan durasi pengerjaan soal dalam menit (1-480 menit / 1 menit hingga 8 jam)</small>
                        </div>

                        <!-- Tabs untuk semua soal items -->
                        <div class="mb-4">
                            <h6 class="mb-3"><i class='bx bx-list-check me-2'></i>Soal-soal dalam Paket ({{ count($exercise->exerciseItems) }} soal)</h6>
                            
                            @if($exercise->exerciseItems->count() > 0)
                                <!-- Nav Tabs -->
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach($exercise->exerciseItems as $index => $item)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                    id="tab-soal-{{ $item->id }}" 
                                                    data-bs-toggle="tab" 
                                                    data-bs-target="#content-soal-{{ $item->id }}" 
                                                    type="button" role="tab">
                                                Soal #{{ $index + 1 }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content border rounded p-3">
                                    @foreach($exercise->exerciseItems as $index => $item)
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                             id="content-soal-{{ $item->id }}" role="tabpanel">
                                            
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">

                                            <!-- Jenis Soal -->
                                            <div class="mb-3">
                                                <span class="badge bg-label-primary px-3 py-2" style="font-size: 0.9rem;">
                                                    {{ $item->exercise_model_id == 1 ? 'Pilihan Ganda' : ($item->exercise_model_id == 2 ? 'Essai' : 'Jawaban Singkat') }}
                                                </span>
                                                <input type="hidden" name="items[{{ $index }}][question_type]" value="{{ $item->exercise_model_id == 1 ? 'pilihan_ganda' : ($item->exercise_model_id == 2 ? 'essai' : 'jawaban_singkat') }}">
                                            </div>

                                            <!-- Pertanyaan -->
                                            <div class="mb-3">
                                                <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                                                <textarea class="form-control summernote" name="items[{{ $index }}][question]" required>{!! old("items.{$index}.question", $item->question ?? '') !!}</textarea>
                                            </div>

                                            <!-- Pilihan Jawaban (untuk Pilihan Ganda) -->
                                            @php
                                                $options = is_array($item->options) ? $item->options : json_decode($item->options, true);
                                                $showOptions = ($item->exercise_model_id == 1);
                                                $optionsDict = [];
                                                if (is_array($options)) {
                                                    foreach ($options as $opt) {
                                                        if (isset($opt['key']) && isset($opt['text'])) {
                                                            $optionsDict[$opt['key']] = $opt['text'];
                                                        }
                                                    }
                                                }
                                            @endphp
                                            
                                            <div class="options-section mb-3" style="{{ $showOptions ? '' : 'display:none' }}">
                                                <label class="form-label">Pilihan Jawaban</label>
                                                <div class="options-container">
                                                    @if(is_array($optionsDict) && !empty($optionsDict))
                                                        @foreach(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'] as $key => $label)
                                                            <div class="mb-3 border p-2 rounded">
                                                                <label class="form-label mb-1 fw-bold">Pilihan {{ $label }}</label>
                                                                <textarea class="form-control summernote" name="items[{{ $index }}][selection][{{ $key }}]" 
                                                                       placeholder="Opsi {{ $label }}">{!! old("items.{$index}.selection.{$key}", $optionsDict[$key] ?? '') !!}</textarea>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        @foreach(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'] as $key => $label)
                                                            <div class="mb-3 border p-2 rounded">
                                                                <label class="form-label mb-1 fw-bold">Pilihan {{ $label }}</label>
                                                                <textarea class="form-control summernote" name="items[{{ $index }}][selection][{{ $key }}]" placeholder="Opsi {{ $label }}"></textarea>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Kunci Jawaban -->
                                            <div class="mb-3">
                                                <label class="form-label">Kunci Jawaban</label>
                                                @php
                                                    $answerVal = is_array($item->answer) ? implode(',', $item->answer) : (is_string($item->answer) ? implode(',', json_decode($item->answer, true) ?? []) : '');
                                                @endphp
                                                <input type="text" class="form-control" name="items[{{ $index }}][answer]" 
                                                       value="{{ old("items.{$index}.answer", $answerVal) }}" 
                                                       placeholder="Untuk pilihan ganda: A/B/C/D/E. Untuk essay: jawaban yang benar">
                                                <small class="text-muted">Untuk pilihan ganda: A/B/C/D/E | Untuk essay/singkat: jawaban yang benar</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">Tidak ada soal items</div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class='bx bx-save me-2'></i>Update Semua
                            </button>
                            <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}" class="btn btn-label-secondary btn-lg">
                                <i class='bx bx-x me-2'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Bagikan ke Kelas Card -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class='bx bx-share-alt me-2'></i>Bagikan ke Kelas</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.soal.update-custom', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST" id="shareForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="mapel_id" value="{{ $exercise->lesson->mapel_id }}">
                        <input type="hidden" name="exercise_type_id" value="{{ $exercise->exercise_type_id }}">
                        <input type="hidden" name="title" value="{{ $exercise->title }}">
                        <input type="hidden" name="time_limit" value="{{ $exercise->time_limit }}">
                        @foreach($exercise->exerciseItems as $index => $item)
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            <input type="hidden" name="items[{{ $index }}][question_type]" value="{{ $item->exercise_model_id == 1 ? 'pilihan_ganda' : ($item->exercise_model_id == 2 ? 'essai' : 'jawaban_singkat') }}">
                            <input type="hidden" name="items[{{ $index }}][question]" value="{{ strip_tags($item->question) }}">
                            @php
                                $ansForHidden = is_array($item->answer) ? implode(',', $item->answer) : $item->answer;
                            @endphp
                            <input type="hidden" name="items[{{ $index }}][answer]" value="{{ is_string($ansForHidden) ? $ansForHidden : json_encode($ansForHidden) }}">
                        @endforeach

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAllClassrooms">
                            <label class="form-check-label fw-bold" for="selectAllClassrooms">
                                Pilih Semua Kelas
                            </label>
                        </div>
                        <hr>

                        @php
                            $sharedClasses = is_array($exercise->shared_to_classes) ? $exercise->shared_to_classes : (json_decode($exercise->shared_to_classes, true) ?? []);
                        @endphp

                        @if($classrooms->count() > 0)
                            <div class="classroom-list" style="max-height: 300px; overflow-y: auto;">
                                @foreach($classrooms as $classroom)
                                <div class="form-check mb-2">
                                    <input class="form-check-input classroom-checkbox" type="checkbox" name="classrooms[]" 
                                           value="{{ $classroom->id }}" id="classroom{{ $classroom->id }}"
                                           {{ in_array($classroom->id, old('classrooms', $sharedClasses)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="classroom{{ $classroom->id }}">
                                        <span class="badge bg-info me-1">{{ $classroom->name }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <hr>
                            <small class="text-muted d-block mb-3">Pilih kelas yang akan dapat mengakses soal ini</small>
                            <button type="submit" form="shareForm" class="btn btn-success btn-sm w-100">
                                <i class='bx bx-check me-1'></i>Simpan Pembagian
                            </button>
                        @else
                            <div class="alert alert-warning mb-0" role="alert">
                                <small>Tidak ada kelas yang tersedia</small>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Informasi Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class='bx bx-info-circle me-2'></i>Petunjuk</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li>✓ Isi semua field yang diperlukan</li>
                        <li>✓ Setiap tab adalah satu soal terpisah</li>
                        <li>✓ Gunakan jenis soal yang sesuai</li>
                        <li>✓ Kunci jawaban harus jelas</li>
                        <li>✓ Tentukan waktu pengerjaan soal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for main submission -->
    <form action="{{ route('guru.soal.update-custom', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST" id="mainForm" style="display:none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="mapel_id" value="{{ $exercise->lesson->mapel_id }}">
        <input type="hidden" name="exercise_type_id" value="{{ $exercise->exercise_type_id }}">
        <input type="hidden" name="title" value="{{ $exercise->title }}">
        <input type="hidden" name="time_limit" value="{{ $exercise->time_limit }}">
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllClassrooms');
    const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');
    const questionTypeSelects = document.querySelectorAll('.question-type-select');

    // Handle question type change for each tab
    questionTypeSelects.forEach(select => {
        select.addEventListener('change', function() {
            const tabPane = this.closest('.tab-pane');
            const optionsSection = tabPane.querySelector('.options-section');
            
            if (this.value === 'pilihan_ganda') {
                optionsSection.style.display = 'block';
            } else {
                optionsSection.style.display = 'none';
            }
        });
    });

    // Select all classrooms
    selectAllCheckbox.addEventListener('change', function() {
        classroomCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all checkbox state
    classroomCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(classroomCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(classroomCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });

    // Initial state for select all
    const initialAllChecked = Array.from(classroomCheckboxes).every(cb => cb.checked);
    const initialSomeChecked = Array.from(classroomCheckboxes).some(cb => cb.checked);
    selectAllCheckbox.checked = initialAllChecked;
    selectAllCheckbox.indeterminate = initialSomeChecked && !initialAllChecked;

    // Initialize Summernote editors
    if (typeof $ !== 'undefined' && $.fn.summernote) {
        $('.summernote').summernote({
            height: 150,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['picture', 'link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    }
});
</script>
@endsection

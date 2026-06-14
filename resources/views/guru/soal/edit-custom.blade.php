@extends('layouts.sneat')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor .note-editing-area { min-height: 150px; max-height: 300px; overflow-y: auto; }
    .summernote-option-container .note-editor .note-editing-area { max-height: 150px; }
    .note-editor .note-dropzone { opacity: 0 !important; }
    .sticky-footer {
        position: sticky;
        bottom: 0;
        z-index: 1020;
        background: white;
        border-top: 1px solid #d9dee3;
        padding: 1rem;
        box-shadow: 0 -0.125rem 0.25rem rgba(161, 172, 184, 0.075);
    }
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class='bx bx-list-check me-2'></i>Soal-soal dalam Paket ({{ count($exercise->exerciseItems) }} soal)</h6>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSoalModal">
                                    <i class='bx bx-plus me-1'></i>Tambah Soal
                                </button>
                            </div>
                            
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
                                            <input type="hidden" name="items[{{ $index }}][question_type]" value="{{ $item->exercise_model_id }}">

                                            <!-- Header Card -->
                                            <div class="card shadow-none border mb-4">
                                                <div class="card-body pb-0">
                                                    <h5 class="mb-2">Soal #{{ $index + 1 }}</h5>
                                                    <div class="d-flex gap-2 flex-wrap mb-3">
                                                        @if(!empty($item->competence_id) && $item->competence)
                                                            <span class="badge bg-label-warning">KD {{ $item->competence->point }}{{ $item->competence->description ? ' - ' . \Illuminate\Support\Str::limit($item->competence->description, 30) : '' }}</span>
                                                        @endif
                                                        <span class="badge bg-label-info">{{ $item->exerciseModel->name ?? 'Tipe Soal' }}</span>
                                                        <span class="badge bg-label-primary">Dibuat Guru</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 1: Informasi Soal -->
                                            <div class="card shadow-none border mb-4">
                                                <div class="card-header pb-0"><h6 class="mb-0">Informasi Soal</h6></div>
                                                <div class="card-body mt-3">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label text-muted">Nomor Soal</label>
                                                            <input type="text" class="form-control bg-light" value="{{ $index + 1 }}" readonly>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label text-muted">Tipe Soal</label>
                                                            <input type="text" class="form-control bg-light" value="{{ $item->exerciseModel->name ?? 'Tipe Soal' }}" readonly>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label text-muted">Kompetensi Dasar</label>
                                                            <input type="text" class="form-control bg-light" value="{{ !empty($item->competence_id) && $item->competence ? 'KD ' . $item->competence->point : 'Tidak Ada' }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 2: Pertanyaan -->
                                            <div class="card shadow-none border mb-4">
                                                <div class="card-header pb-0"><h6 class="mb-0">Pertanyaan</h6></div>
                                                <div class="card-body mt-3">
                                                    <textarea class="form-control summernote" name="items[{{ $index }}][question]" required>{!! old("items.{$index}.question", $item->question ?? '') !!}</textarea>
                                                </div>
                                            </div>

                                            <!-- Opsi & Jawaban berdasarkan Model -->
                                            @php
                                                $modelId = $item->exercise_model_id;
                                                $selection = is_array($item->selection) ? $item->selection : json_decode($item->selection, true) ?? [];
                                                $answers = is_array($item->answer) ? $item->answer : json_decode($item->answer, true) ?? [];
                                            @endphp

                                            @if($modelId == 1) {{-- Pilihan Ganda --}}
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Jawaban Benar</h6></div>
                                                    <div class="card-body mt-3">
                                                        <select name="items[{{ $index }}][answer]" class="form-select" required>
                                                            <option value="">-- Pilih Kunci Jawaban --</option>
                                                            @foreach(['A', 'B', 'C', 'D'] as $abjad)
                                                                <option value="{{ $abjad }}" {{ in_array($abjad, $answers) ? 'selected' : '' }}>{{ $abjad }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban</h6></div>
                                                    <div class="card-body mt-3">
                                                        <div class="row">
                                                            @foreach(['A', 'B', 'C', 'D'] as $i => $abjad)
                                                            <div class="col-md-6 mb-3">
                                                                <label class="fw-bold">Pilihan {{ $abjad }}</label>
                                                                <textarea class="form-control summernote" name="items[{{ $index }}][selection][]">{!! $selection[$i] ?? '' !!}</textarea>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                            @elseif($modelId == 2) {{-- Pilihan Ganda Banyak --}}
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Jawaban Benar (Bisa lebih dari satu)</h6></div>
                                                    <div class="card-body mt-3">
                                                        <div class="d-flex gap-4 p-3 bg-light border rounded flex-wrap">
                                                            @foreach(['A', 'B', 'C', 'D'] as $abjad)
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input" name="items[{{ $index }}][answer][]" value="{{ $abjad }}" id="q{{$index}}ans{{$abjad}}" {{ in_array($abjad, $answers) ? 'checked' : '' }}>
                                                                    <label for="q{{$index}}ans{{$abjad}}" class="form-check-label fw-bold">{{ $abjad }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban (Pilihan Ganda Banyak)</h6></div>
                                                    <div class="card-body mt-3">
                                                        <div class="row">
                                                            @foreach(['A', 'B', 'C', 'D'] as $i => $abjad)
                                                            <div class="col-md-6 mb-3">
                                                                <label class="fw-bold">Pilihan {{ $abjad }}</label>
                                                                <textarea class="form-control summernote" name="items[{{ $index }}][selection][]">{!! $selection[$i] ?? '' !!}</textarea>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                            @elseif($modelId == 3) {{-- Benar Salah --}}
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban</h6></div>
                                                    <div class="card-body mt-3">
                                                        <select name="items[{{ $index }}][answer]" class="form-select" required>
                                                            <option value="">-- Pilih Benar/Salah --</option>
                                                            <option value="Benar" {{ in_array('Benar', $answers) ? 'selected' : '' }}>Benar</option>
                                                            <option value="Salah" {{ in_array('Salah', $answers) ? 'selected' : '' }}>Salah</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            @elseif($modelId == 4) {{-- Isian --}}
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban</h6></div>
                                                    <div class="card-body mt-3">
                                                        <input type="text" name="items[{{ $index }}][answer]" class="form-control" value="{{ $answers[0] ?? '' }}" placeholder="Isi jawaban benar..." required autocomplete="off" spellcheck="false">
                                                    </div>
                                                </div>

                                            @elseif($modelId == 5 || $modelId == 7) {{-- Uraian / Argumen --}}
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Panduan / Referensi Jawaban</h6></div>
                                                    <div class="card-body mt-3">
                                                        <textarea class="form-control summernote" name="items[{{ $index }}][answer]" required>{!! $answers[0] ?? '' !!}</textarea>
                                                    </div>
                                                </div>

                                            @elseif($modelId == 6) {{-- Iya Tidak --}}
                                                <div class="card shadow-none border mb-4">
                                                    <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban</h6></div>
                                                    <div class="card-body mt-3">
                                                        <select name="items[{{ $index }}][answer]" class="form-select" required>
                                                            <option value="">-- Pilih Iya/Tidak --</option>
                                                            <option value="Iya" {{ in_array('Iya', $answers) ? 'selected' : '' }}>Iya</option>
                                                            <option value="Tidak" {{ in_array('Tidak', $answers) ? 'selected' : '' }}>Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">Tidak ada soal items</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Bagikan ke Kelas Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class='bx bx-share-alt me-2'></i>Bagikan ke Kelas</h6>
                    </div>
                    <div class="card-body">
                            <input type="hidden" name="mapel_id" value="{{ $exercise->lesson->mapel_id }}">
                            <input type="hidden" name="exercise_type_id" value="{{ $exercise->exercise_type_id }}">
                            <input type="hidden" name="title" value="{{ $exercise->title }}">
                            <input type="hidden" name="time_limit" value="{{ $exercise->time_limit }}">
                            
                            {{-- Hidden inputs for the rest of items so they don't get deleted --}}
                            @foreach($exercise->exerciseItems as $index => $item)
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][question_type]" value="{{ $item->exercise_model_id }}">
                                <input type="hidden" name="items[{{ $index }}][question]" value="{{ strip_tags($item->question) }}">
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
                            @else
                                <div class="alert alert-warning mb-0" role="alert">
                                    <small>Tidak ada kelas yang tersedia</small>
                                </div>
                            @endif
                    </div>
                </div>

                <!-- Informasi Card -->
                <div class="card shadow-sm mt-3 mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class='bx bx-info-circle me-2'></i>Petunjuk</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 small text-muted">
                            <li class="mb-1">✓ Isi semua field yang diperlukan</li>
                            <li class="mb-1">✓ Setiap tab adalah satu soal terpisah</li>
                            <li>✓ Kunci jawaban disesuaikan dengan tipe soal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sticky Footer -->
        <div class="sticky-footer d-flex justify-content-end gap-2 mt-4 rounded">
            <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}" class="btn btn-label-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                Simpan Soal
            </button>
        </div>
        </form>
    </div>
</div>
</div>

<!-- Modal Tambah Soal -->
<div class="modal fade" id="addSoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('guru.soal.store-custom-item', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class='bx bx-plus-circle me-2'></i>Tambah Soal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="new_question_type" class="form-label fw-bold text-dark">Model Soal <span class="text-danger">*</span></label>
                        <select class="form-select border-primary" id="new_question_type" name="question_type" required>
                            <option value="">-- Pilih Model Soal --</option>
                            @foreach($exerciseModels as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="newQuestionSection" style="display: none;">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Pertanyaan/Soal <span class="text-danger">*</span></label>
                            <textarea class="form-control summernote-modal" name="question" required></textarea>
                        </div>
                        <div id="dynamicInputsContainerModal" class="bg-light p-4 rounded border border-primary"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btnSimpanSoal" style="display: none;">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllClassrooms');
    const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');

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
            ],
            callbacks: {
                onImageUpload: function(files) {
                    uploadImage(files[0], 'soal', this);
                }
            }
        });
    }

    // JS for Modal Tambah Soal
    const newQuestionType = document.getElementById('new_question_type');
    const newQuestionSection = document.getElementById('newQuestionSection');
    const dynamicInputsContainerModal = document.getElementById('dynamicInputsContainerModal');
    const btnSimpanSoal = document.getElementById('btnSimpanSoal');

    newQuestionType.addEventListener('change', function() {
        if (this.value) {
            newQuestionSection.style.display = 'block';
            btnSimpanSoal.style.display = 'inline-block';
            renderDynamicInputsModal();
        } else {
            newQuestionSection.style.display = 'none';
            btnSimpanSoal.style.display = 'none';
        }
    });

    function renderDynamicInputsModal() {
        const modelId = newQuestionType.value;
        const modelText = (newQuestionType.options[newQuestionType.selectedIndex].text || '').trim().toLowerCase();
        
        let html = '';
        if (modelId == 1 || modelText === 'pilihan ganda') { // PG
            html = `
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Jawaban Benar <span class="text-danger">*</span></h6></div>
                    <div class="card-body mt-3">
                        <select name="answer" class="form-select" required>
                            <option value="">-- Pilih Kunci Jawaban --</option>
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban</h6></div>
                    <div class="card-body mt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan A</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan B</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan C</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan D</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                        </div>
                    </div>
                </div>
            `;
        } else if (modelId == 2 || modelText === 'pilihan ganda banyak') { // PG Banyak
            html = `
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Jawaban Benar (Bisa lebih dari satu) <span class="text-danger">*</span></h6></div>
                    <div class="card-body mt-3">
                        <div class="d-flex gap-4 p-3 bg-light border rounded flex-wrap">
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="answer[]" value="A" id="modalAnsA"><label for="modalAnsA" class="form-check-label fw-bold">A</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="answer[]" value="B" id="modalAnsB"><label for="modalAnsB" class="form-check-label fw-bold">B</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="answer[]" value="C" id="modalAnsC"><label for="modalAnsC" class="form-check-label fw-bold">C</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="answer[]" value="D" id="modalAnsD"><label for="modalAnsD" class="form-check-label fw-bold">D</label></div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban (Pilihan Ganda Banyak)</h6></div>
                    <div class="card-body mt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan A</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan B</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan C</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                            <div class="col-md-6 mb-3"><label class="fw-bold">Pilihan D</label><textarea class="form-control summernote-modal-option" name="selection[]"></textarea></div>
                        </div>
                    </div>
                </div>
            `;
        } else if (modelId == 3 || modelText === 'pernyataan') { // Benar Salah
            html = `
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban <span class="text-danger">*</span></h6></div>
                    <div class="card-body mt-3">
                        <select name="answer" class="form-select" required>
                            <option value="">-- Pilih Benar/Salah --</option>
                            <option value="Benar">Benar</option>
                            <option value="Salah">Salah</option>
                        </select>
                    </div>
                </div>
            `;
        } else if (modelId == 4 || modelText === 'isian') { // Isian
            html = `
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban <span class="text-danger">*</span></h6></div>
                    <div class="card-body mt-3">
                        <input type="text" name="answer" class="form-control" placeholder="Isi jawaban benar..." required autocomplete="off" spellcheck="false">
                    </div>
                </div>
            `;
        } else if (modelId == 5 || modelId == 7 || modelText === 'uraian' || modelText === 'argumen') { // Uraian / Argumen
            html = `
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Panduan / Referensi Jawaban <span class="text-danger">*</span></h6></div>
                    <div class="card-body mt-3">
                        <textarea class="form-control summernote-modal-answer" name="answer" required></textarea>
                    </div>
                </div>
            `;
        } else if (modelId == 6 || modelText === 'iya tidak') { // Iya Tidak
            html = `
                <div class="card shadow-none border mb-4">
                    <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban <span class="text-danger">*</span></h6></div>
                    <div class="card-body mt-3">
                        <select name="answer" class="form-select" required>
                            <option value="">-- Pilih Iya/Tidak --</option>
                            <option value="Iya">Iya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>
                </div>
            `;
        }

        // Destroy old summernotes
        const oldSummernotes = dynamicInputsContainerModal.querySelectorAll('.summernote-modal-option, .summernote-modal-answer');
        if (oldSummernotes.length > 0 && typeof $ !== 'undefined' && $.fn.summernote) {
            try {
                $(oldSummernotes).summernote('destroy');
            } catch(e) {}
        }

        dynamicInputsContainerModal.innerHTML = html;

        // Init new summernotes
        if (typeof $ !== 'undefined' && $.fn.summernote) {
            $(dynamicInputsContainerModal.querySelectorAll('.summernote-modal-option, .summernote-modal-answer')).summernote({
                height: 100,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['picture', 'link']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0], 'soal', this);
                    }
                }
            });
        }
    }

    // Modal shown event
    const addSoalModal = document.getElementById('addSoalModal');
    addSoalModal.addEventListener('shown.bs.modal', function () {
        if (typeof $ !== 'undefined' && $.fn.summernote) {
            if (!$('.summernote-modal').data('summernote')) {
                $('.summernote-modal').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['picture', 'link']],
                        ['view', ['fullscreen', 'codeview']]
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            uploadImage(files[0], 'soal', this);
                        }
                    }
                });
            }
        }
    });

});
</script>
@endsection

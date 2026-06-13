@extends('layouts.sneat')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .card-modern {
        border-radius: 16px;
        box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        border: none;
    }
    .note-editor .note-editing-area { min-height: 150px; }
    .note-editor .note-dropzone { opacity: 0 !important; }
    
    .list-group-item.cursor-pointer:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
            <li class="breadcrumb-item active">Tambah Soal</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold text-dark"><i class='bx bx-edit text-primary me-2'></i>Tambah {{ $categoryInfo['name'] }}</h4>
    </div>

    <form action="{{ route('guru.soal.store-custom', [$serial->id, $lesson->id]) }}" method="POST" id="createExerciseForm">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Form Utama (70%) -->
            <div class="col-lg-8 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-header border-bottom bg-transparent pb-3 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class='bx bx-info-circle me-2 text-primary'></i>Informasi Soal</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Placeholder Sebelum Tipe Soal Dipilih -->
                        <div id="placeholderSection" class="text-center py-5">
                            <i class='bx bx-file-blank text-muted mb-3' style="font-size: 64px;"></i>
                            <h5 class="text-muted mb-0">Pilih tipe soal terlebih dahulu untuk melanjutkan.</h5>
                        </div>

                        <!-- Main Form Section (Hidden initially) -->
                        <div id="mainFormSection" style="display: none;">
                            
                            <!-- Jenis Soal (Model) -->
                            <div class="mb-4" id="questionTypeSection" style="display: none;">
                                <label for="question_type" class="form-label fw-bold text-dark">Jenis Soal (Model) <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg @error('question_type') is-invalid @enderror" id="question_type" name="question_type" required>
                                    <option value="">-- Pilih Jenis Soal --</option>
                                    @foreach($exerciseModels as $model)
                                        <option value="{{ $model->id }}" {{ old('question_type') == $model->id ? 'selected' : '' }}>
                                            {{ $model->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="questionTypeRuleHint" class="form-text text-danger mt-1" style="display:none;"></div>
                                @error('question_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <!-- Judul Paket Soal -->
                                <div class="col-12">
                                    <label for="title" class="form-label fw-bold text-dark">Judul Paket Soal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Latihan Soal Matematika" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Container untuk multiple soal -->
                            <div id="questionsContainer">
                                <div class="question-item border rounded p-4 mb-4 bg-light" data-index="0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold text-primary">Soal #<span class="question-number">1</span></h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn" style="display: none;">
                                            <i class='bx bx-trash me-1'></i> Hapus
                                        </button>
                                    </div>

                                    <!-- Pertanyaan -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark">Pertanyaan/Soal <span class="text-danger">*</span></label>
                                        <textarea class="form-control summernote-question" name="questions[0][question]" placeholder="Tulis pertanyaan atau soal di sini..." required></textarea>
                                    </div>

                                    <!-- Container dinamis untuk opsi & jawaban -->
                                    <div class="dynamic-inputs-container bg-white p-3 rounded border"></div>
                                </div>
                            </div>

                            <!-- Tombol Tambah Soal -->
                            <button type="button" class="btn btn-outline-primary btn-lg w-100 border-dashed" id="addQuestionBtn">
                                <i class='bx bx-plus-circle me-1'></i>Tambah Soal Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Pengaturan & Media (30%) -->
            <div class="col-lg-4 mb-4">
                
                <!-- Card Pengaturan Soal -->
                <div class="card card-modern mb-4">
                    <div class="card-header border-bottom bg-transparent pb-3 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class='bx bx-cog me-2 text-primary'></i>Pengaturan Soal</h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Tipe Soal -->
                        <div class="mb-4">
                            <label for="exercise_type_id" class="form-label fw-bold text-dark">Tipe Soal <span class="text-danger">*</span></label>
                            <select class="form-select @error('exercise_type_id') is-invalid @enderror" id="exercise_type_id" name="exercise_type_id" required>
                                <option value="">-- Pilih Tipe Soal --</option>
                                @foreach($exerciseTypes as $type)
                                    <option value="{{ $type->id }}" data-kode="{{ $type->kode }}" {{ old('exercise_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('exercise_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Waktu Pengerjaan -->
                        <div class="mb-2">
                            <label for="time_limit" class="form-label fw-bold text-dark">Durasi (Menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" min="1" max="480" value="{{ old('time_limit') }}" placeholder="Contoh: 45" required>
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">1-480 menit (Maksimal 8 jam)</small>
                        </div>

                        <!-- Paket Materi (Hidden input, just to keep original form payload) -->
                        <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                    </div>
                </div>

                <!-- Card Bagikan ke Kelas -->
                <div class="card card-modern mb-4">
                    <div class="card-header border-bottom bg-transparent pb-3 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class='bx bx-share-alt me-2 text-primary'></i>Bagikan ke Kelas</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check mb-3 pb-3 border-bottom">
                            <input class="form-check-input" type="checkbox" id="selectAllClasses">
                            <label class="form-check-label fw-bold text-dark" for="selectAllClasses">
                                Pilih Semua Kelas
                            </label>
                        </div>

                        <div class="list-group rounded-3 overflow-hidden" style="max-height: 200px; overflow-y: auto;">
                            @forelse($classrooms as $classroom)
                                <label class="list-group-item d-flex gap-2 px-3 py-2 cursor-pointer border-1">
                                    <input class="form-check-input classroom-checkbox mt-0" type="checkbox" name="classrooms[]" value="{{ $classroom->id }}" {{ (is_array(old('classrooms')) && in_array($classroom->id, old('classrooms'))) ? 'checked' : '' }}>
                                    <span class="text-dark">{{ $classroom->name }}</span>
                                </label>
                            @empty
                                <div class="alert alert-warning mb-0 rounded-0 border-0">
                                    <i class='bx bx-info-circle'></i> Belum ada kelas tersedia.
                                </div>
                            @endforelse
                        </div>
                        <small class="text-muted d-block mt-3">
                            <i class='bx bx-info-circle me-1'></i>Soal akan langsung dibagikan ke kelas yang dipilih
                        </small>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex flex-column gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold w-100">
                        <i class='bx bx-save me-2'></i>Simpan Soal
                    </button>
                    <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}" class="btn btn-label-secondary btn-lg fw-bold w-100">
                        Batal
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const exerciseTypeSelect = document.getElementById('exercise_type_id');
    const questionTypeSection = document.getElementById('questionTypeSection');
    const questionTypeSelect = document.getElementById('question_type');
    const mainFormSection = document.getElementById('mainFormSection');
    const placeholderSection = document.getElementById('placeholderSection');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const questionsContainer = document.getElementById('questionsContainer');
    
    const selectAllCheckbox = document.getElementById('selectAllClasses');
    const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');
    
    let questionIndex = 0;

    // Show question type section
    function toggleMainSections() {
        if (exerciseTypeSelect.value) {
            // Tipe soal sudah dipilih
            placeholderSection.style.display = 'none';
            mainFormSection.style.display = 'block';
            questionTypeSection.style.display = 'block';
        } else {
            // Tipe soal belum dipilih
            placeholderSection.style.display = 'block';
            mainFormSection.style.display = 'none';
            questionTypeSection.style.display = 'none';
        }
    }

    exerciseTypeSelect.addEventListener('change', function() {
        toggleMainSections();
    });

    // Handle Model Soal change
    questionTypeSelect.addEventListener('change', function() {
        if (this.value) {
            renderAllDynamicInputs();
        }
    });

    // Apply rule script
    if (window.applyExerciseModelRule) {
        window.applyExerciseModelRule({
            typeSelectId: 'exercise_type_id',
            modelSelectId: 'question_type',
            hintId: 'questionTypeRuleHint',
            onChange: function(val) {
                toggleMainSections();
                if (val) {
                    renderAllDynamicInputs();
                }
            }
        });
    }

    selectAllCheckbox.addEventListener('change', function() {
        classroomCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    classroomCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(classroomCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(classroomCheckboxes).some(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });

    addQuestionBtn.addEventListener('click', function() {
        questionIndex++;
        const newQuestionHTML = `
            <div class="question-item border rounded p-4 mb-4 bg-light" data-index="${questionIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-primary">Soal #<span class="question-number"></span></h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn">
                        <i class='bx bx-trash me-1'></i> Hapus
                    </button>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Pertanyaan/Soal <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote-question" name="questions[${questionIndex}][question]" placeholder="Tulis pertanyaan atau soal di sini..." required></textarea>
                </div>

                <div class="dynamic-inputs-container bg-white p-3 rounded border"></div>
            </div>
        `;
        
        questionsContainer.insertAdjacentHTML('beforeend', newQuestionHTML);
        const newItem = questionsContainer.lastElementChild;
        
        initSummernote(newItem.querySelectorAll('.summernote-question'));
        renderDynamicInputs(newItem);
        updateQuestionNumbers();
    });

    questionsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question-btn')) {
            e.target.closest('.question-item').remove();
            updateQuestionNumbers();
        }
    });

    function updateQuestionNumbers() {
        const questionItems = document.querySelectorAll('.question-item');
        const removeButtons = document.querySelectorAll('.remove-question-btn');
        
        questionItems.forEach((item, idx) => {
            item.querySelector('.question-number').textContent = idx + 1;
        });
        
        if (questionItems.length > 1) {
            removeButtons.forEach(btn => btn.style.display = 'inline-block');
        } else {
            removeButtons.forEach(btn => btn.style.display = 'none');
        }
    }

    function initSummernote(elements) {
        if (typeof $ !== 'undefined' && $.fn.summernote) {
            $(elements).summernote({
                height: 120,
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

    function renderAllDynamicInputs() {
        document.querySelectorAll('.question-item').forEach(item => {
            renderDynamicInputs(item);
        });
    }

    function renderDynamicInputs(itemElement) {
        const modelId = questionTypeSelect.value;
        const container = itemElement.querySelector('.dynamic-inputs-container');
        const idx = itemElement.getAttribute('data-index');
        
        let html = '';
        let modelText = '';
        if (questionTypeSelect.selectedIndex >= 0) {
            modelText = (questionTypeSelect.options[questionTypeSelect.selectedIndex].text || '').trim().toLowerCase();
        }

        if (modelId == 1 || modelText === 'pilihan ganda') { // PG
            html = `
                <div class="mb-3">
                    <h6 class="mb-3 fw-bold text-dark">Pilihan Jawaban</h6>
                    <div class="mb-3"><label class="fw-bold">Pilihan A</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mb-3"><label class="fw-bold">Pilihan B</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mb-3"><label class="fw-bold">Pilihan C</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mb-3"><label class="fw-bold">Pilihan D</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label fw-bold text-primary">Jawaban Benar</label>
                        <select name="questions[${idx}][answer]" class="form-select border-primary" required>
                            <option value="">-- Pilih Kunci Jawaban --</option>
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>
            `;
        } else if (modelId == 2 || modelText === 'pilihan ganda banyak') { // PG Banyak
            html = `
                <div class="mb-3">
                    <h6 class="mb-3 fw-bold text-dark">Pilihan Jawaban (Pilihan Ganda Banyak)</h6>
                    <div class="mb-3"><label class="fw-bold">Pilihan A</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mb-3"><label class="fw-bold">Pilihan B</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mb-3"><label class="fw-bold">Pilihan C</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mb-3"><label class="fw-bold">Pilihan D</label><textarea class="form-control summernote-option" name="questions[${idx}][selection][]"></textarea></div>
                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label fw-bold text-primary">Jawaban Benar (Bisa lebih dari satu)</label>
                        <div class="d-flex gap-4 p-3 bg-light border border-primary rounded">
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="A" id="q${idx}ansA"><label for="q${idx}ansA" class="form-check-label fw-bold">A</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="B" id="q${idx}ansB"><label for="q${idx}ansB" class="form-check-label fw-bold">B</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="C" id="q${idx}ansC"><label for="q${idx}ansC" class="form-check-label fw-bold">C</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="D" id="q${idx}ansD"><label for="q${idx}ansD" class="form-check-label fw-bold">D</label></div>
                        </div>
                    </div>
                </div>
            `;
        } else if (modelId == 3 || modelText === 'pernyataan') { // Benar Salah
            html = `
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Kunci Jawaban</label>
                    <select name="questions[${idx}][answer]" class="form-select border-primary" required>
                        <option value="">-- Pilih Benar/Salah --</option>
                        <option value="Benar">Benar</option>
                        <option value="Salah">Salah</option>
                    </select>
                </div>
            `;
        } else if (modelId == 4 || modelText === 'isian') { // Isian
            html = `
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Kunci Jawaban</label>
                    <input type="text" name="questions[${idx}][answer]" class="form-control border-primary" placeholder="Isi jawaban benar..." required autocomplete="off" spellcheck="false">
                </div>
            `;
        } else if (modelId == 5 || modelId == 7 || modelText === 'uraian' || modelText === 'argumen') { // Uraian / Argumen
            html = `
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Panduan / Referensi Jawaban</label>
                    <div class="border border-primary rounded overflow-hidden">
                        <textarea class="form-control summernote-answer border-0" name="questions[${idx}][answer]" required></textarea>
                    </div>
                </div>
            `;
        } else if (modelId == 6 || modelText === 'iya tidak') { // Iya Tidak
            html = `
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Kunci Jawaban</label>
                    <select name="questions[${idx}][answer]" class="form-select border-primary" required>
                        <option value="">-- Pilih Iya/Tidak --</option>
                        <option value="Iya">Iya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </div>
            `;
        }

        // Destroy old summernotes
        const oldSummernotes = container.querySelectorAll('.summernote-option, .summernote-answer');
        if (oldSummernotes.length > 0 && typeof $ !== 'undefined' && $.fn.summernote) {
            try {
                $(oldSummernotes).summernote('destroy');
            } catch(e) {
                console.warn('Failed to destroy old summernote', e);
            }
        }

        container.innerHTML = html;

        // Initialize new summernotes
        const newSummernotes = container.querySelectorAll('.summernote-option, .summernote-answer');
        if (newSummernotes.length > 0) {
            initSummernote(newSummernotes);
        }
    }

    // Trigger initial toggle
    toggleMainSections();
    
    // Init the very first question item on load if visible
    initSummernote(document.querySelectorAll('.summernote-question'));
    updateQuestionNumbers();
});
</script>
@include('guru.soal.partials.model-rule-script')
@endsection

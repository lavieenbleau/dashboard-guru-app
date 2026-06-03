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
            <li class="breadcrumb-item active">Tambah Soal</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah {{ $categoryInfo['name'] }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.soal.store-custom', [$serial->id, $lesson->id]) }}" method="POST" id="createExerciseForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Tipe Soal -->
                                <div class="mb-3">
                                    <label for="exercise_type_id" class="form-label">Tipe Soal <span class="text-danger">*</span></label>
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

                                <!-- Jenis Soal -->
                                <div class="mb-3" id="questionTypeSection" style="display: none;">
                                    <label for="question_type" class="form-label">Jenis Soal (Model) <span class="text-danger">*</span></label>
                                    <select class="form-select @error('question_type') is-invalid @enderror" id="question_type" name="question_type" required>
                                        <option value="">-- Pilih Jenis Soal --</option>
                                        @foreach($exerciseModels as $model)
                                            <option value="{{ $model->id }}" {{ old('question_type') == $model->id ? 'selected' : '' }}>
                                                {{ $model->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="questionTypeRuleHint" class="form-text text-danger" style="display:none;"></div>
                                    @error('question_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Form Inputs (muncul setelah pilih tipe soal) -->
                                <div id="mainFormSection" style="display: none;">
                                    <div class="row">
                                        <!-- Paket Materi (Lesson) -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Paket Materi</label>
                                            <input type="text" class="form-control" value="{{ $lesson->name }} (Mapel: {{ $lesson->mapel->name ?? '-' }})" disabled>
                                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                                        </div>

                                        <!-- Judul Paket Soal -->
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="form-label">Judul Paket Soal <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Latihan Soal Matematika" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Waktu Pengerjaan -->
                                        <div class="col-md-6 mb-3">
                                            <label for="time_limit" class="form-label">Waktu Pengerjaan (Menit) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" min="1" max="480" value="{{ old('time_limit') }}" placeholder="Contoh: 45" required>
                                            @error('time_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted d-block mt-1">Masukkan durasi pengerjaan soal dalam menit (1-480 menit / 1 menit hingga 8 jam)</small>
                                        </div>
                                    </div>

                                    <!-- Container untuk multiple soal -->
                                    <div id="questionsContainer">
                                        <div class="question-item border rounded p-3 mb-3" data-index="0">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Soal #<span class="question-number">1</span></h6>
                                                <button type="button" class="btn btn-sm btn-danger remove-question-btn" style="display: none;">
                                                    <i class='bx bx-trash'></i> Hapus
                                                </button>
                                            </div>

                                            <!-- Pertanyaan -->
                                            <div class="mb-3">
                                                <label class="form-label">Pertanyaan/Soal <span class="text-danger">*</span></label>
                                                <textarea class="form-control summernote-question" name="questions[0][question]" placeholder="Tulis pertanyaan atau soal di sini..." required></textarea>
                                            </div>

                                            <!-- Container dinamis untuk opsi & jawaban -->
                                            <div class="dynamic-inputs-container"></div>
                                        </div>
                                    </div>

                                    <!-- Tombol Tambah Soal -->
                                    <button type="button" class="btn btn-success mb-3" id="addQuestionBtn">
                                        <i class='bx bx-plus-circle me-1'></i>Tambah Soal Lagi
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Daftar Kelas -->
                                <div class="card bg-label-info">
                                    <div class="card-body">
                                        <h6 class="mb-3"><i class='bx bx-share-alt me-2'></i>Bagikan ke Kelas</h6>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="selectAllClasses">
                                            <label class="form-check-label fw-bold" for="selectAllClasses">
                                                Pilih Semua Kelas
                                            </label>
                                        </div>

                                        <hr>

                                        <div style="max-height: 300px; overflow-y: auto;">
                                            @forelse($classrooms as $classroom)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input classroom-checkbox" type="checkbox" name="classrooms[]" value="{{ $classroom->id }}" id="class{{ $classroom->id }}">
                                                    <label class="form-check-label" for="class{{ $classroom->id }}">
                                                        {{ $classroom->name }}
                                                    </label>
                                                </div>
                                            @empty
                                                <p class="text-muted mb-0">Tidak ada kelas tersedia</p>
                                            @endforelse
                                        </div>

                                        <small class="text-muted d-block mt-3">
                                            <i class='bx bx-info-circle me-1'></i>Soal akan langsung dibagikan ke kelas yang dipilih
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i>Simpan
                            </button>
                            <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}" class="btn btn-label-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const questionsContainer = document.getElementById('questionsContainer');
    
    const selectAllCheckbox = document.getElementById('selectAllClasses');
    const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');
    
    let questionIndex = 0;

    // Show question type section
    exerciseTypeSelect.addEventListener('change', function() {
        if (this.value) {
            questionTypeSection.style.display = 'block';
        } else {
            questionTypeSection.style.display = 'none';
            mainFormSection.style.display = 'none';
        }
    });

    // Show main form after selecting question type
    questionTypeSelect.addEventListener('change', function() {
        if (this.value) {
            mainFormSection.style.display = 'block';
            renderAllDynamicInputs();
        } else {
            mainFormSection.style.display = 'none';
        }
    });

    // Apply rule script
    if (window.applyExerciseModelRule) {
        window.applyExerciseModelRule({
            typeSelectId: 'exercise_type_id',
            modelSelectId: 'question_type',
            hintId: 'questionTypeRuleHint',
            onChange: function(val) {
                if(exerciseTypeSelect.value) questionTypeSection.style.display = 'block';
                if (val) {
                    mainFormSection.style.display = 'block';
                    renderAllDynamicInputs();
                } else {
                    mainFormSection.style.display = 'none';
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
            <div class="question-item border rounded p-3 mb-3" data-index="${questionIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Soal #<span class="question-number"></span></h6>
                    <button type="button" class="btn btn-sm btn-danger remove-question-btn">
                        <i class='bx bx-trash'></i> Hapus
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pertanyaan/Soal <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote-question" name="questions[${questionIndex}][question]" placeholder="Tulis pertanyaan atau soal di sini..." required></textarea>
                </div>

                <div class="dynamic-inputs-container"></div>
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
                ]
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
        
        // Save current data before unmounting
        // Not strictly necessary for a fresh create form, but good practice
        
        let html = '';

        if (modelId === '1') { // PG
            html = `
                <div class="mb-3 border p-3 rounded">
                    <h6>Pilihan Jawaban</h6>
                    <div class="mb-3"><label>Pilihan A</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mb-3"><label>Pilihan B</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mb-3"><label>Pilihan C</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mb-3"><label>Pilihan D</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mt-3 border-top pt-3">
                        <label class="form-label fw-bold text-primary">Jawaban Benar</label>
                        <select name="questions[${idx}][answer]" class="form-select border-primary" required>
                            <option value="">-- Pilih Kunci Jawaban --</option>
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>
            `;
        } else if (modelId === '2') { // PG Banyak
            html = `
                <div class="mb-3 border p-3 rounded">
                    <h6>Pilihan Jawaban (Pilihan Ganda Banyak)</h6>
                    <div class="mb-3"><label>Pilihan A</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mb-3"><label>Pilihan B</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mb-3"><label>Pilihan C</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mb-3"><label>Pilihan D</label><textarea class="form-control summernote-option" name="questions[${idx}][options][]"></textarea></div>
                    <div class="mt-3 border-top pt-3">
                        <label class="form-label fw-bold text-primary">Jawaban Benar (Bisa lebih dari satu)</label>
                        <div class="d-flex gap-4 p-2 border border-primary rounded">
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="A" id="q${idx}ansA"><label for="q${idx}ansA" class="form-check-label fw-bold">A</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="B" id="q${idx}ansB"><label for="q${idx}ansB" class="form-check-label fw-bold">B</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="C" id="q${idx}ansC"><label for="q${idx}ansC" class="form-check-label fw-bold">C</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" name="questions[${idx}][answer][]" value="D" id="q${idx}ansD"><label for="q${idx}ansD" class="form-check-label fw-bold">D</label></div>
                        </div>
                    </div>
                </div>
            `;
        } else if (modelId === '3') { // Benar Salah
            html = `
                <div class="mb-3 border p-3 rounded">
                    <label class="form-label fw-bold text-primary">Kunci Jawaban</label>
                    <select name="questions[${idx}][answer]" class="form-select border-primary" required>
                        <option value="">-- Pilih Benar/Salah --</option>
                        <option value="Benar">Benar</option>
                        <option value="Salah">Salah</option>
                    </select>
                </div>
            `;
        } else if (modelId === '4') { // Isian
            html = `
                <div class="mb-3 border p-3 rounded">
                    <label class="form-label fw-bold text-primary">Kunci Jawaban</label>
                    <input type="text" name="questions[${idx}][answer]" class="form-control border-primary" placeholder="Isi jawaban benar..." required autocomplete="off" spellcheck="false">
                </div>
            `;
        } else if (modelId === '5' || modelId === '7') { // Uraian / Argumen
            html = `
                <div class="mb-3 border p-3 rounded">
                    <label class="form-label fw-bold text-primary">Panduan / Referensi Jawaban</label>
                    <div class="border border-primary rounded">
                        <textarea class="form-control summernote-answer border-0" name="questions[${idx}][answer]" required></textarea>
                    </div>
                </div>
            `;
        } else if (modelId === '6') { // Iya Tidak
            html = `
                <div class="mb-3 border p-3 rounded">
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
            $(oldSummernotes).summernote('destroy');
        }

        container.innerHTML = html;

        // Initialize new summernotes
        const newSummernotes = container.querySelectorAll('.summernote-option, .summernote-answer');
        if (newSummernotes.length > 0) {
            initSummernote(newSummernotes);
        }
    }

    // Init the very first question item on load
    initSummernote(document.querySelectorAll('.summernote-question'));
    updateQuestionNumbers();
});
</script>
@include('guru.soal.partials.model-rule-script')
@endsection

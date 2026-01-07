@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
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
                    <form action="{{ route('guru.soal.store-custom', [$serial->id]) }}" method="POST" id="createExerciseForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Tipe Soal -->
                                <div class="mb-3">
                                    <label for="exercise_type_id" class="form-label">Tipe Soal <span class="text-danger">*</span></label>
                                    <select class="form-select @error('exercise_type_id') is-invalid @enderror" id="exercise_type_id" name="exercise_type_id" required>
                                        <option value="">-- Pilih Tipe Soal --</option>
                                        @foreach($exerciseTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('exercise_type_id') == $type->id ? 'selected' : '' }}>
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
                                    <label for="question_type" class="form-label">Jenis Soal <span class="text-danger">*</span></label>
                                    <select class="form-select @error('question_type') is-invalid @enderror" id="question_type" name="question_type" required>
                                        <option value="">-- Pilih Jenis Soal --</option>
                                        <option value="pilihan_ganda" {{ old('question_type') == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                                        <option value="essai" {{ old('question_type') == 'essai' ? 'selected' : '' }}>Essai</option>
                                        <option value="jawaban_singkat" {{ old('question_type') == 'jawaban_singkat' ? 'selected' : '' }}>Jawaban Singkat</option>
                                    </select>
                                    @error('question_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Form Inputs (muncul setelah pilih tipe soal) -->
                                <div id="mainFormSection" style="display: none;">
                                    <!-- Pilih Mapel -->
                                    <div class="mb-3">
                                        <label for="mapel_id" class="form-label">Pilih Mapel <span class="text-danger">*</span></label>
                                        <select class="form-select @error('mapel_id') is-invalid @enderror" id="mapel_id" name="mapel_id" required>
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

                                    <!-- Container untuk multiple soal -->
                                    <div id="questionsContainer">
                                        <div class="question-item border rounded p-3 mb-3" data-question-number="1">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Soal #<span class="question-number">1</span></h6>
                                                <button type="button" class="btn btn-sm btn-danger remove-question-btn" style="display: none;">
                                                    <i class='bx bx-trash'></i> Hapus
                                                </button>
                                            </div>

                                            <!-- Judul Soal -->
                                            <div class="mb-3">
                                                <label class="form-label">Judul Soal <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="questions[0][title]" placeholder="Contoh: Latihan Perkalian" required>
                                            </div>

                                            <!-- Pertanyaan -->
                                            <div class="mb-3">
                                                <label class="form-label">Pertanyaan/Soal <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="questions[0][question]" rows="4" placeholder="Tulis pertanyaan atau soal di sini..." required></textarea>
                                            </div>

                                            <!-- Pilihan Ganda Options -->
                                            <div class="mb-3 options-section" style="display: none;">
                                                <label class="form-label">Pilihan Jawaban</label>
                                                <div class="options-container">
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text">A</span>
                                                        <input type="text" class="form-control" name="questions[0][options][]" placeholder="Opsi A">
                                                    </div>
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text">B</span>
                                                        <input type="text" class="form-control" name="questions[0][options][]" placeholder="Opsi B">
                                                    </div>
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text">C</span>
                                                        <input type="text" class="form-control" name="questions[0][options][]" placeholder="Opsi C">
                                                    </div>
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text">D</span>
                                                        <input type="text" class="form-control" name="questions[0][options][]" placeholder="Opsi D">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Jawaban -->
                                            <div class="mb-3">
                                                <label class="form-label">Kunci Jawaban</label>
                                                <textarea class="form-control" name="questions[0][answer]" rows="2" placeholder="Tulis kunci jawaban di sini..."></textarea>
                                                <small class="text-muted">Untuk pilihan ganda, tulis huruf jawaban yang benar (A/B/C/D).</small>
                                            </div>
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
                            <a href="{{ route('guru.soal.list-direct', [$serial->id, $category]) }}" class="btn btn-label-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exerciseTypeSelect = document.getElementById('exercise_type_id');
    const questionTypeSection = document.getElementById('questionTypeSection');
    const questionTypeSelect = document.getElementById('question_type');
    const mainFormSection = document.getElementById('mainFormSection');
    const selectAllCheckbox = document.getElementById('selectAllClasses');
    const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const questionsContainer = document.getElementById('questionsContainer');
    let questionCount = 1;

    // Show question type after selecting exercise type
    exerciseTypeSelect.addEventListener('change', function() {
        if (this.value) {
            questionTypeSection.style.display = 'block';
        } else {
            questionTypeSection.style.display = 'none';
            mainFormSection.style.display = 'none';
        }
    });

    // Show main form and handle options after selecting question type
    questionTypeSelect.addEventListener('change', function() {
        if (this.value) {
            mainFormSection.style.display = 'block';
            
            // Show/hide options based on question type for all question items
            const optionsSections = document.querySelectorAll('.options-section');
            optionsSections.forEach(section => {
                if (questionTypeSelect.value === 'pilihan_ganda') {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        } else {
            mainFormSection.style.display = 'none';
        }
    });

    // Select all classes functionality
    selectAllCheckbox.addEventListener('change', function() {
        classroomCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all checkbox when individual checkboxes change
    classroomCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(classroomCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(classroomCheckboxes).some(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });

    // Add new question
    addQuestionBtn.addEventListener('click', function() {
        const newQuestionHTML = `
            <div class="question-item border rounded p-3 mb-3" data-question-number="${questionCount}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Soal #<span class="question-number">${questionCount + 1}</span></h6>
                    <button type="button" class="btn btn-sm btn-danger remove-question-btn">
                        <i class='bx bx-trash'></i> Hapus
                    </button>
                </div>

                <!-- Judul Soal -->
                <div class="mb-3">
                    <label class="form-label">Judul Soal <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="questions[${questionCount}][title]" placeholder="Contoh: Latihan Perkalian" required>
                </div>

                <!-- Pertanyaan -->
                <div class="mb-3">
                    <label class="form-label">Pertanyaan/Soal <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="questions[${questionCount}][question]" rows="4" placeholder="Tulis pertanyaan atau soal di sini..." required></textarea>
                </div>

                <!-- Pilihan Ganda Options -->
                <div class="mb-3 options-section" style="display: ${questionTypeSelect.value === 'pilihan_ganda' ? 'block' : 'none'};">
                    <label class="form-label">Pilihan Jawaban</label>
                    <div class="options-container">
                        <div class="input-group mb-2">
                            <span class="input-group-text">A</span>
                            <input type="text" class="form-control" name="questions[${questionCount}][options][]" placeholder="Opsi A">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text">B</span>
                            <input type="text" class="form-control" name="questions[${questionCount}][options][]" placeholder="Opsi B">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text">C</span>
                            <input type="text" class="form-control" name="questions[${questionCount}][options][]" placeholder="Opsi C">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text">D</span>
                            <input type="text" class="form-control" name="questions[${questionCount}][options][]" placeholder="Opsi D">
                        </div>
                    </div>
                </div>

                <!-- Jawaban -->
                <div class="mb-3">
                    <label class="form-label">Kunci Jawaban</label>
                    <textarea class="form-control" name="questions[${questionCount}][answer]" rows="2" placeholder="Tulis kunci jawaban di sini..."></textarea>
                    <small class="text-muted">Untuk pilihan ganda, tulis huruf jawaban yang benar (A/B/C/D).</small>
                </div>
            </div>
        `;
        
        questionsContainer.insertAdjacentHTML('beforeend', newQuestionHTML);
        questionCount++;
        updateRemoveButtons();
        updateQuestionNumbers();
    });

    // Remove question
    questionsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question-btn')) {
            e.target.closest('.question-item').remove();
            updateRemoveButtons();
            updateQuestionNumbers();
        }
    });

    // Update remove buttons visibility
    function updateRemoveButtons() {
        const questionItems = document.querySelectorAll('.question-item');
        const removeButtons = document.querySelectorAll('.remove-question-btn');
        
        if (questionItems.length > 1) {
            removeButtons.forEach(btn => btn.style.display = 'inline-block');
        } else {
            removeButtons.forEach(btn => btn.style.display = 'none');
        }
    }

    // Update question numbers
    function updateQuestionNumbers() {
        const questionItems = document.querySelectorAll('.question-item');
        questionItems.forEach((item, index) => {
            item.querySelector('.question-number').textContent = index + 1;
        });
    }

    // Trigger change event if old values exist
    if (exerciseTypeSelect.value) {
        questionTypeSection.style.display = 'block';
    }
    if (questionTypeSelect.value) {
        mainFormSection.style.display = 'block';
        const optionsSections = document.querySelectorAll('.options-section');
        optionsSections.forEach(section => {
            if (questionTypeSelect.value === 'pilihan_ganda') {
                section.style.display = 'block';
            }
        });
    }
});
</script>
@endsection

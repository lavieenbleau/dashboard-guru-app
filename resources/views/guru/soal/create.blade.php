@extends('layouts.sneat')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor .note-editing-area { min-height: 150px; }
    .note-editor .note-dropzone { opacity: 0 !important; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.tema', [$serial->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list', [$serial->id, $category, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item active">Tambah Soal</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-file-blank text-{{ $categoryInfo['color'] }} me-2'></i>Tambah Soal Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.soal.store', [$serial->id, $category, $tema->id]) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Soal <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Soal</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan instruksi atau informasi tambahan untuk soal ini</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Materi</label>
                            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link') }}" placeholder="https://example.com">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Link ke sumber materi atau file (Google Drive, YouTube, dll)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select @error('semester') is-invalid @enderror">
                                <option value="1" {{ old('semester') == 1 ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ old('semester') == 2 ? 'selected' : '' }}>Semester 2</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Jenis Soal -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Soal <span class="text-danger">*</span></label>
                                <select id="exercise_model_id" name="exercise_model_id" class="form-select @error('exercise_model_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis Soal --</option>
                                    @foreach($exerciseModels as $model)
                                        <option value="{{ $model->id }}" {{ old('exercise_model_id') == $model->id ? 'selected' : '' }}>
                                            {{ $model->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('exercise_model_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="questionTypeRuleHint" class="text-danger small mt-1" style="display: none;"></div>
                            </div>

                            <!-- Tipe Soal -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipe Soal <span class="text-danger">*</span></label>
                                <select id="exercise_type_id" name="exercise_type_id" class="form-select @error('exercise_type_id') is-invalid @enderror" required>
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
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Daftar Soal</label>
                                <button type="button" class="btn btn-sm btn-outline-{{ $categoryInfo['color'] }}" id="addQuestion">
                                    <i class='bx bx-plus'></i> Tambah Soal
                                </button>
                            </div>
                            <div id="questionsContainer">
                                <div class="question-item mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text align-items-start">1.</span>
                                        <div class="flex-grow-1">
                                            <textarea name="questions[]" class="form-control summernote" placeholder="Tulis soal..."></textarea>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger remove-question align-self-start" style="display: none;">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Tambahkan soal-soal (opsional)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-{{ $categoryInfo['color'] }}">
                                <i class='bx bx-save me-1'></i>Simpan Soal
                            </button>
                            <a href="{{ route('guru.soal.list', [$serial->id, $category, $tema->id]) }}" class="btn btn-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Informasi</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Kategori:</strong><br>
                            {{ $categoryInfo['name'] }}
                        </li>
                        <li class="mb-2">
                            <strong>Mata Pelajaran:</strong><br>
                            {{ $tema->name }}
                        </li>
                    </ul>
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
let questionCount = 1;

document.getElementById('addQuestion').addEventListener('click', function() {
    questionCount++;
    const container = document.getElementById('questionsContainer');
    const newQuestion = document.createElement('div');
    newQuestion.className = 'question-item mb-2';
    newQuestion.innerHTML = `
        <div class="input-group">
            <span class="input-group-text align-items-start">${questionCount}.</span>
            <div class="flex-grow-1">
                <textarea name="questions[]" class="form-control summernote" placeholder="Tulis soal..."></textarea>
            </div>
            <button type="button" class="btn btn-outline-danger remove-question align-self-start">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    `;
    container.appendChild(newQuestion);
    
    // Initialize Summernote on the new textarea
    const newTextarea = newQuestion.querySelector('.summernote');
    $(newTextarea).summernote({
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
    
    updateRemoveButtons();
});

document.getElementById('questionsContainer').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-question') || e.target.closest('.remove-question')) {
        const questionItem = e.target.closest('.question-item');
        questionItem.remove();
        renumberQuestions();
        updateRemoveButtons();
    }
});

function renumberQuestions() {
    const questions = document.querySelectorAll('.question-item');
    questionCount = questions.length;
    questions.forEach((item, index) => {
        item.querySelector('.input-group-text').textContent = (index + 1) + '.';
    });
}

function updateRemoveButtons() {
    const questions = document.querySelectorAll('.question-item');
    const removeButtons = document.querySelectorAll('.remove-question');
    if (questions.length === 1) {
        removeButtons.forEach(btn => btn.style.display = 'none');
    } else {
        removeButtons.forEach(btn => btn.style.display = 'block');
    }
}

document.addEventListener('DOMContentLoaded', function() {
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
});

    if (window.applyExerciseModelRule) {
        window.applyExerciseModelRule({
            typeSelectId: 'exercise_type_id',
            modelSelectId: 'exercise_model_id',
            hintId: 'questionTypeRuleHint'
        });
    }
</script>
@include('guru.soal.partials.model-rule-script')
@endsection

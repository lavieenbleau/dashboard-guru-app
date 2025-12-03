@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.tema', [$serial->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list', [$serial->id, $category, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item active">Edit Soal</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-edit text-{{ $categoryInfo['color'] }} me-2'></i>Edit Soal</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.soal.update', [$serial->id, $category, $tema->id, $lesson->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Soal <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $lesson->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Soal</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $lesson->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan instruksi atau informasi tambahan untuk soal ini</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Materi</label>
                            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $lesson->link ?? '') }}" placeholder="https://example.com">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Link ke sumber materi atau file (Google Drive, YouTube, dll)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select @error('semester') is-invalid @enderror">
                                <option value="1" {{ old('semester', $lesson->semester) == 1 ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ old('semester', $lesson->semester) == 2 ? 'selected' : '' }}>Semester 2</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Daftar Soal</label>
                                <button type="button" class="btn btn-sm btn-outline-{{ $categoryInfo['color'] }}" id="addQuestion">
                                    <i class='bx bx-plus'></i> Tambah Soal
                                </button>
                            </div>
                            <div id="questionsContainer">
                                @php
                                    $existingQuestions = old('questions', isset($lesson->questions) && is_array($lesson->questions) ? $lesson->questions : []);
                                @endphp
                                @if(count($existingQuestions) > 0)
                                    @foreach($existingQuestions as $index => $question)
                                        <div class="question-item mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $index + 1 }}.</span>
                                                <textarea name="questions[]" rows="2" class="form-control" placeholder="Tulis soal...">{{ $question }}</textarea>
                                                <button type="button" class="btn btn-outline-danger remove-question" {{ count($existingQuestions) == 1 ? 'style=display:none;' : '' }}>
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="question-item mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">1.</span>
                                            <textarea name="questions[]" rows="2" class="form-control" placeholder="Tulis soal..."></textarea>
                                            <button type="button" class="btn btn-outline-danger remove-question" style="display: none;">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <small class="text-muted">Tambahkan soal-soal (opsional)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-{{ $categoryInfo['color'] }}">
                                <i class='bx bx-save me-1'></i>Update Soal
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
                        <li class="mb-2">
                            <strong>Dibuat:</strong><br>
                            {{ $lesson->created_at->format('d M Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let questionCount = {{ count($existingQuestions ?? [1]) }};

document.getElementById('addQuestion').addEventListener('click', function() {
    questionCount++;
    const container = document.getElementById('questionsContainer');
    const newQuestion = document.createElement('div');
    newQuestion.className = 'question-item mb-2';
    newQuestion.innerHTML = `
        <div class="input-group">
            <span class="input-group-text">${questionCount}.</span>
            <textarea name="questions[]" rows="2" class="form-control" placeholder="Tulis soal..."></textarea>
            <button type="button" class="btn btn-outline-danger remove-question">
                <i class='bx bx-trash'></i>
            </button>
        </div>
    `;
    container.appendChild(newQuestion);
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
</script>
@endsection

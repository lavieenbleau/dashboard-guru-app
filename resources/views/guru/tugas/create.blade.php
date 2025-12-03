@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.tema', [$serial->id, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.list', [$serial->id, $tema->id, $subtema->id]) }}">{{ $subtema->name }}</a></li>
            <li class="breadcrumb-item active">Tambah Tugas</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-task text-warning me-2'></i>Tambah Tugas Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.tugas.store', [$serial->id, $tema->id, $subtema->id]) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Tugas</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan tugas secara detail</small>
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

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Soal Tugas</label>
                                <button type="button" class="btn btn-sm btn-outline-warning" id="addQuestion">
                                    <i class='bx bx-plus'></i> Tambah Soal
                                </button>
                            </div>
                            <div id="questionsContainer">
                                <div class="question-item mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text">1.</span>
                                        <textarea name="questions[]" rows="2" class="form-control" placeholder="Tulis soal..."></textarea>
                                        <button type="button" class="btn btn-outline-danger remove-question" style="display: none;">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Tambahkan soal-soal untuk tugas ini (opsional)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class='bx bx-save me-1'></i>Simpan Tugas
                            </button>
                            <a href="{{ route('guru.tugas.list', [$serial->id, $tema->id, $subtema->id]) }}" class="btn btn-secondary">
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
                            <strong>Tema:</strong><br>
                            {{ $tema->name }}
                        </li>
                        <li class="mb-2">
                            <strong>Sub Tema:</strong><br>
                            {{ $subtema->name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let questionCount = 1;

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

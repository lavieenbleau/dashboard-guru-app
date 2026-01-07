@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}">{{ $mapel->name }}</a></li>
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
                    <form action="{{ route('guru.tugas.store', [$serial->id, $mapel->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required>
                            @error('title')
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
                            <label class="form-label">Lampiran File</label>
                            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png">
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload file tugas (PDF, Word, Excel, PowerPoint, ZIP, atau gambar, max 10MB)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deadline Pengumpulan</label>
                            <input type="datetime-local" name="deadline" class="form-control @error('deadline') is-invalid @enderror" 
                                   value="{{ old('deadline') }}">
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Batas waktu pengumpulan tugas (opsional)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class='bx bx-save me-1'></i>Simpan Tugas
                            </button>
                            <a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}" class="btn btn-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Informasi</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Mata Pelajaran:</strong><br>
                            {{ $mapel->name }}
                        </li>
                        <li class="mb-2">
                            <strong>Serial:</strong><br>
                            {{ $serial->product->name }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Bagikan ke Kelas</h6>
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="selectAllClasses">
                        <label class="form-check-label small" for="selectAllClasses">
                            Pilih Semua
                        </label>
                    </div>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @if($classrooms->count() > 0)
                        @foreach($classrooms as $classroom)
                            <div class="form-check mb-2">
                                <input class="form-check-input classroom-checkbox" type="checkbox" 
                                       name="classrooms[]" value="{{ $classroom->id }}" 
                                       id="classroom{{ $classroom->id }}">
                                <label class="form-check-label" for="classroom{{ $classroom->id }}">
                                    {{ $classroom->name }}
                                    <small class="text-muted">({{ $classroom->grade }})</small>
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">Belum ada kelas tersedia</p>
                    @endif
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

// Select all classrooms functionality
const selectAllCheckbox = document.getElementById('selectAllClasses');
const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        classroomCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    classroomCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(classroomCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(classroomCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });
}
</script>
@endsection

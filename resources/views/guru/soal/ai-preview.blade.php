@extends('layouts.sneat')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    /* Typography & Color System Fix */
    h5, h6, .card-header h5, .card-header h6 { color: #1e293b !important; font-weight: 700 !important; }
    label, .form-label, .fw-bold, .card-body label, .card-body .fw-bold { color: #334155 !important; font-weight: 600 !important; }
    .form-control, .form-select, .form-control[readonly] { color: #1e293b !important; }
    .form-control::placeholder { color: #94a3b8 !important; opacity: 1 !important; }
    .text-muted, small.text-muted { color: #64748b !important; font-size: 0.875rem !important; }
    
    /* Summernote Editor */
    .note-editor .note-toolbar, .note-editor .note-statusbar, .note-editor .note-btn { color: #334155 !important; background-color: #ffffff !important; border-bottom: 1px solid #e2e8f0; }
    .note-editor, .note-editor.note-frame, .note-editor .note-editing-area, .note-editor .note-editable { color: #1e293b !important; background-color: #ffffff !important; }
    .note-editor.note-frame { border-color: #cbd5e1 !important; box-shadow: none !important; }
    .note-editor.note-frame .note-statusbar { background-color: #ffffff !important; border-top: 1px solid #e2e8f0; }
    .note-editor .note-dropzone { opacity: 0 !important; }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serialModel->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serialModel->id, $lessonModel->id]) }}">{{ $lessonModel->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serialModel->id, $lessonModel->id, 'tambahan']) }}">Soal Tambahan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.ai-generator', [$serialModel->id, $lessonModel->id]) }}">Generate AI</a></li>
            <li class="breadcrumb-item active">Preview & Edit</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Info Card -->
            <div class="card bg-label-success mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="badge bg-success p-3 me-3">
                                <i class='bx bx-check-circle bx-md'></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Soal Berhasil Di-generate!</h5>
                                <p class="mb-0 text-muted">Review dan edit soal di bawah ini sebelum menyimpan. Anda dapat mengedit, menghapus, atau menyimpan langsung ke bank soal.</p>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-success" style="font-size: 1.2rem;">{{ count($aiData['questions']) }} Soal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Form -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class='bx bx-edit me-2'></i>Preview & Edit Soal</h5>
                    <a href="{{ route('guru.soal.ai-generator', [$serialModel->id, $lessonModel->id]) }}" class="btn btn-sm btn-label-primary">
                        <i class='bx bx-brain me-1'></i>Generate Lagi
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.soal.ai-save', [$serialModel->id, $lessonModel->id]) }}" method="POST" id="saveQuestionsForm">
                        @csrf

                        <!-- Hidden Fields -->
                        <input type="hidden" name="exercise_model_id" value="{{ $aiData['exercise_model_id'] ?? '' }}">
                        <input type="hidden" name="lesson_id" value="{{ $lessonModel->id }}">
                        <input type="hidden" name="exercise_type_id" value="{{ $aiData['exercise_type_id'] ?? '' }}">
                        <input type="hidden" name="time_limit" value="{{ $aiData['time_limit'] ?? '' }}">
                        @if(isset($aiData['classrooms']))
                            @foreach($aiData['classrooms'] as $classroomId)
                                <input type="hidden" name="classrooms[]" value="{{ $classroomId }}">
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Settings Section -->
                                <div class="mb-4 p-3 border rounded bg-label-secondary">
                                    <h6 class="mb-3"><i class='bx bx-cog me-2'></i>Pengaturan Soal</h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="exercise_title" class="form-label">Judul Paket Soal <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="exercise_title" name="exercise_title" placeholder="Contoh: Latihan Soal Bab 5" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Paket Pembelajaran</label>
                                            <input type="text" class="form-control" value="{{ $lessonModel->name }} ({{ $lessonModel->mapel->name ?? '-' }})" disabled>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="time_limit_edit" class="form-label">Waktu Pengerjaan (Menit) <span class="text-danger">*</span></label>
                                            <input 
                                                type="number" 
                                                class="form-control" 
                                                id="time_limit_edit" 
                                                name="time_limit" 
                                                min="1" 
                                                max="480" 
                                                value="{{ $aiData['time_limit'] ?? '' }}"
                                                placeholder="Contoh: 45"
                                                required>
                                            <small class="text-muted">Masukkan durasi pengerjaan soal dalam menit (1-480 menit / 1 menit hingga 8 jam)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Questions Container -->
                                <div id="questionsContainer">
                                    @foreach($aiData['questions'] as $index => $question)
                                        <div class="question-item border rounded p-3 mb-3" data-question-index="{{ $index }}">
                                            <input type="hidden" name="questions[{{ $index }}][deleted]" value="false" class="deleted-input">
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-2">Soal #{{ $index + 1 }}</span>
                                                    <span class="badge bg-info">{{ $aiData['exercise_model_name'] ?? 'Jenis Soal' }}</span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger remove-question-btn">
                                                    <i class='bx bx-trash'></i> Hapus
                                                </button>
                                            </div>

                                            <!-- Judul Soal -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Judul Soal <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="questions[{{ $index }}][title]" value="{{ $question['title'] ?? '' }}" required>
                                            </div>

                                            <!-- KD Badge -->
                                            @php
                                                $kompetensi = null;
                                                if (isset($question['competence_id']) && $question['competence_id']) {
                                                    $kompetensi = \App\Models\Competence::find($question['competence_id']);
                                                }
                                            @endphp
                                            @if($kompetensi)
                                                <div class="mb-3">
                                                    <span class="badge bg-label-warning">
                                                        KD: {{ $kompetensi->point }}{{ $kompetensi->description ? ' - ' . \Illuminate\Support\Str::limit($kompetensi->description, 30) : '' }}
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Pertanyaan -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Pertanyaan/Soal <span class="text-danger">*</span></label>
                                                <textarea class="form-control summernote-question" name="questions[{{ $index }}][question]" required>{{ $question['question'] ?? '' }}</textarea>
                                            </div>

                                            @if(in_array($aiData['exercise_model_id'], [1, 2]))
                                                <!-- Pilihan Ganda Options -->
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Pilihan Jawaban</label>
                                                    <div class="options-container">
                                                        @php $options = $question['selection'] ?? $question['options'] ?? []; @endphp
                                                        @foreach(['A', 'B', 'C', 'D'] as $optIndex => $optLabel)
                                                            <div class="mb-3">
                                                                <label class="fw-bold">Pilihan {{ $optLabel }}</label>
                                                                <textarea class="form-control summernote-option" name="questions[{{ $index }}][selection][]" placeholder="Opsi {{ $optLabel }}">{{ $options[$optIndex] ?? '' }}</textarea>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Kunci Jawaban -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Kunci Jawaban</label>
                                                @if(in_array($aiData['exercise_model_id'], [1, 2]))
                                                    <input type="text" class="form-control" name="questions[{{ $index }}][answer]" value="{{ is_array($question['correct_answer'] ?? null) ? implode(', ', $question['correct_answer']) : ($question['correct_answer'] ?? '') }}">
                                                    <small class="text-muted">Tulis huruf jawaban yang benar (A/B/C/D)</small>
                                                @else
                                                    <textarea class="form-control summernote-answer" name="questions[{{ $index }}][answer]">{{ is_array($question['correct_answer'] ?? null) ? implode(', ', $question['correct_answer']) : ($question['correct_answer'] ?? '') }}</textarea>
                                                    <small class="text-muted">Tulis poin-poin kunci jawaban untuk panduan penilaian</small>
                                                @endif
                                            </div>

                                            @if(isset($question['explanation']))
                                                <!-- Penjelasan -->
                                                <div class="alert alert-info mb-0">
                                                    <strong><i class='bx bx-bulb me-1'></i>Penjelasan AI:</strong>
                                                    <p class="mb-0 mt-2">{{ $question['explanation'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div class="alert alert-warning" id="noQuestionsAlert" style="display: none;">
                                    <i class='bx bx-error me-1'></i>Tidak ada soal yang dipilih. Pilih minimal 1 soal untuk disimpan.
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Summary Card -->
                                <div class="card bg-label-primary mb-3 sticky-top" style="top: 20px;">
                                    <div class="card-body">
                                        <h6 class="mb-3"><i class='bx bx-info-circle me-2'></i>Ringkasan</h6>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Total Soal</small>
                                            <h4 class="mb-0" id="totalQuestionsCount">{{ count($aiData['questions']) }}</h4>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Jenis Soal</small>
                                            <p class="mb-0">
                                                @if((isset($aiData['question_type']) && $aiData['question_type'] === 'pilihan_ganda') || (isset($aiData['exercise_model_id']) && in_array($aiData['exercise_model_id'], [1, 2])))
                                                    <span class="badge bg-info">Pilihan Ganda</span>
                                                @else
                                                    <span class="badge bg-warning">Essay</span>
                                                @endif
                                            </p>
                                        </div>
                                        @if(isset($aiData['classrooms']) && count($aiData['classrooms']) > 0)
                                            <div>
                                                <small class="text-muted d-block">Dibagikan ke</small>
                                                <p class="mb-0">{{ count($aiData['classrooms']) }} Kelas</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-3"><i class='bx bx-save me-2'></i>Simpan Soal</h6>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class='bx bx-save me-1'></i>Simpan ke Bank Soal
                                            </button>
                                            <a href="{{ route('guru.soal.ai-generator', [$serialModel->id, $lessonModel->id]) }}" class="btn btn-label-secondary">
                                                <i class='bx bx-brain me-1'></i>Buat Soal Baru
                                            </a>
                                            <a href="{{ route('guru.soal.list-direct', [$serialModel->id, $lessonModel->id, 'tambahan']) }}" class="btn btn-label-secondary">
                                                <i class='bx bx-x me-1'></i>Batal
                                            </a>
                                        </div>
                                        <small class="text-muted d-block mt-3">
                                            <i class='bx bx-info-circle me-1'></i>Soal yang dihapus tidak akan disimpan
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionsContainer = document.getElementById('questionsContainer');
    const totalCountElement = document.getElementById('totalQuestionsCount');
    const noQuestionsAlert = document.getElementById('noQuestionsAlert');
    const form = document.getElementById('saveQuestionsForm');
    let questionCount = {{ count($aiData['questions']) }};

    // Remove question functionality
    document.querySelectorAll('.remove-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionItem = this.closest('.question-item');
            const deletedInput = questionItem.querySelector('.deleted-input');
            const button = this; // save context for the promise
            
            showConfirm('Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus soal ini?', 'Ya, Hapus', true).then((result) => {
                if (result.isConfirmed) {
                    deletedInput.value = 'true';
                    questionItem.style.opacity = '0.5';
                    questionItem.style.background = '#f8d7da';
                    button.disabled = true;
                    button.innerHTML = '<i class="bx bx-check"></i> Dihapus';
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-secondary');
                    
                    // Update count
                    updateQuestionCount();
                }
            });
        });
    });

    function updateQuestionCount() {
        const activeQuestions = document.querySelectorAll('.question-item').length - 
                               document.querySelectorAll('.deleted-input[value="true"]').length;
        totalCountElement.textContent = activeQuestions;
        
        if (activeQuestions === 0) {
            noQuestionsAlert.style.display = 'block';
        } else {
            noQuestionsAlert.style.display = 'none';
        }
    }

    // Validate on submit
    form.addEventListener('submit', function(e) {
        const activeQuestions = document.querySelectorAll('.question-item').length - 
                               document.querySelectorAll('.deleted-input[value="true"]').length;
        
        if (activeQuestions === 0) {
            e.preventDefault();
            showError('Anda harus memilih minimal 1 soal untuk disimpan!');
            noQuestionsAlert.style.display = 'block';
            window.scrollTo(0, 0);
        } else {
            // Sync summernote to textarea
            if (typeof $ !== 'undefined' && $.fn.summernote) {
                $('.summernote-question, .summernote-option, .summernote-answer').each(function() {
                    if ($(this).next('.note-editor').length > 0) {
                        $(this).val($(this).summernote('code'));
                    }
                });
            }

            // Prevent double submission
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Menyimpan...';
            }
        }
    });
});
</script>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    if (typeof $ !== 'undefined' && $.fn.summernote) {
        const toolbar = [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['picture', 'link']],
            ['view', ['fullscreen', 'codeview']]
        ];

        $('.summernote-question').summernote({
            height: 150,
            toolbar: toolbar,
            callbacks: {
                onImageUpload: function(files) { uploadImage(files[0], 'soal', this); }
            }
        });

        $('.summernote-option').summernote({
            height: 100,
            toolbar: toolbar,
            callbacks: {
                onImageUpload: function(files) { uploadImage(files[0], 'soal', this); }
            }
        });

        $('.summernote-answer').summernote({
            height: 120,
            toolbar: toolbar,
            callbacks: {
                onImageUpload: function(files) { uploadImage(files[0], 'soal', this); }
            }
        });
    }
</script>
@endsection

@endsection

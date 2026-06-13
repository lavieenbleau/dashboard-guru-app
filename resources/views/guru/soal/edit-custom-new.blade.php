@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
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
                    <form action="{{ route('guru.soal.update-custom', [$serial->id, $exercise->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Paket Pembelajaran -->
                        <div class="mb-3">
                            <label class="form-label">Paket Pembelajaran</label>
                            <input type="text" class="form-control" value="{{ $lesson->name }} ({{ $lesson->mapel->name ?? '-' }})" disabled>
                        </div>

                        <!-- Tipe Soal -->
                        <div class="mb-3">
                            <label for="exercise_type_id" class="form-label">Tipe Soal <span class="text-danger">*</span></label>
                            <select class="form-select @error('exercise_type_id') is-invalid @enderror" id="exercise_type_id" name="exercise_type_id" required>
                                <option value="">-- Pilih Tipe Soal --</option>
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

                        <!-- Jenis Soal -->
                        <div class="mb-3">
                            <label for="question_type" class="form-label">Jenis Soal <span class="text-danger">*</span></label>
                            @php
                                $currentQuestionType = old('question_type', $exercise->exerciseItems->first()->question_type ?? 'pilihan_ganda');
                            @endphp
                            <select class="form-select @error('question_type') is-invalid @enderror" id="question_type" name="question_type" required>
                                <option value="pilihan_ganda" {{ $currentQuestionType == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="essai" {{ $currentQuestionType == 'essai' ? 'selected' : '' }}>Essai</option>
                                <option value="jawaban_singkat" {{ $currentQuestionType == 'jawaban_singkat' ? 'selected' : '' }}>Jawaban Singkat</option>
                            </select>
                            @error('question_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Judul Soal -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Soal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $exercise->title) }}" placeholder="Contoh: Latihan Perkalian" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pertanyaan -->
                        <div class="mb-3">
                            <label for="question" class="form-label">Pertanyaan/Soal <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="5" placeholder="Tulis pertanyaan atau soal di sini..." required>{{ old('question', $exercise->exerciseItems->first()->question ?? '') }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pilihan Ganda Options -->
                        <div id="multiple-choice-options" style="{{ $currentQuestionType == 'pilihan_ganda' ? '' : 'display:none;' }}">
                            @php
                                $firstItem = $exercise->exerciseItems->first();
                                $selection = [
                                    $firstItem->option_a ?? '',
                                    $firstItem->option_b ?? '',
                                    $firstItem->option_c ?? '',
                                    $firstItem->option_d ?? '',
                                    $firstItem->option_e ?? '',
                                ];
                            @endphp
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $index => $letter)
                            <div class="mb-3">
                                <label for="option_{{ strtolower($letter) }}" class="form-label">Pilihan {{ $letter }}</label>
                                <input type="text" class="form-control" id="option_{{ strtolower($letter) }}" name="selection[]" value="{{ old('selection.' . $index, $selection[$index]) }}" placeholder="Pilihan {{ $letter }}">
                            </div>
                            @endforeach
                        </div>

                        <!-- Jawaban -->
                        <div class="mb-3">
                            <label for="answer" class="form-label">Kunci Jawaban <span id="answer-hint"></span></label>
                            <input type="text" class="form-control @error('answer') is-invalid @enderror" id="answer" name="answer" value="{{ old('answer', $exercise->exerciseItems->first()->correct_answer ?? '') }}" placeholder="Masukkan kunci jawaban">
                            @error('answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" id="answer-description">Untuk Pilihan Ganda, isi dengan huruf (A, B, C, D, atau E)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i>Update
                            </button>
                            <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}" class="btn btn-label-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Kelas Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Bagikan ke Kelas</h6>
                </div>
                <div class="card-body">
                    @if($classrooms->count() > 0)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="selectAllClasses">
                            <label class="form-check-label fw-bold" for="selectAllClasses">
                                Pilih Semua
                            </label>
                        </div>
                        <hr>
                        @foreach($classrooms as $classroom)
                        <div class="form-check mb-2">
                            @php
                                $sharedClasses = $exercise->shared_to_classes;
                                if (is_string($sharedClasses)) {
                                    $sharedClasses = json_decode($sharedClasses, true);
                                }
                                $isChecked = is_array($sharedClasses) && in_array($classroom->id, $sharedClasses);
                            @endphp
                            <input form="editForm" class="form-check-input classroom-checkbox" type="checkbox" name="classrooms[]" value="{{ $classroom->id }}" id="classroom{{ $classroom->id }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="form-check-label" for="classroom{{ $classroom->id }}">
                                {{ $classroom->name }}
                            </label>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Belum ada kelas yang tersedia.</p>
                    @endif
                </div>
            </div>

            <!-- Info Card -->
            <div class="card bg-label-info">
                <div class="card-body">
                    <h6 class="mb-3"><i class='bx bx-info-circle me-2'></i>Informasi</h6>
                    <ul class="mb-0">
                        <li>Pilih jenis soal terlebih dahulu</li>
                        <li>Untuk Pilihan Ganda, isi pilihan A-E</li>
                        <li>Kunci jawaban Pilihan Ganda: A/B/C/D/E</li>
                        <li>Pilih kelas untuk membagikan soal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionType = document.getElementById('question_type');
    const multipleChoiceOptions = document.getElementById('multiple-choice-options');
    const answerHint = document.getElementById('answer-hint');
    const answerDescription = document.getElementById('answer-description');
    
    function updateForm() {
        const selected = questionType.value;
        
        if (selected === 'pilihan_ganda') {
            multipleChoiceOptions.style.display = '';
            answerHint.textContent = '(A/B/C/D/E)';
            answerDescription.textContent = 'Untuk Pilihan Ganda, isi dengan huruf (A, B, C, D, atau E)';
        } else {
            multipleChoiceOptions.style.display = 'none';
            if (selected === 'essai') {
                answerHint.textContent = '(Jawaban Lengkap)';
                answerDescription.textContent = 'Tulis jawaban lengkap untuk soal essai';
            } else {
                answerHint.textContent = '(Jawaban Singkat)';
                answerDescription.textContent = 'Tulis jawaban singkat yang tepat';
            }
        }
    }
    
    questionType.addEventListener('change', updateForm);
    updateForm(); // Initialize on load
    
    // Select All functionality
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
});
</script>
@endsection

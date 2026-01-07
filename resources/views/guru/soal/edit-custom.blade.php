@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
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

                        @php
                            $exerciseItem = $exercise->exerciseItems->first();
                        @endphp

                        <!-- Pilih Mapel -->
                        <div class="mb-3">
                            <label for="mapel_id" class="form-label">Pilih Mapel <span class="text-danger">*</span></label>
                            <select class="form-select @error('mapel_id') is-invalid @enderror" id="mapel_id" name="mapel_id" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}" {{ (old('mapel_id', $exercise->lesson->mapel_id ?? '') == $mapel->id) ? 'selected' : '' }}>
                                        {{ $mapel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mapel_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                        <!-- Jenis Soal -->
                        <div class="mb-3">
                            <label for="question_type" class="form-label">Jenis Soal <span class="text-danger">*</span></label>
                            <select class="form-select @error('question_type') is-invalid @enderror" id="question_type" name="question_type" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="pilihan_ganda" {{ (old('question_type', $exerciseItem->question_type ?? '') == 'pilihan_ganda') ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="essai" {{ (old('question_type', $exerciseItem->question_type ?? '') == 'essai') ? 'selected' : '' }}>Essai</option>
                                <option value="jawaban_singkat" {{ (old('question_type', $exerciseItem->question_type ?? '') == 'jawaban_singkat') ? 'selected' : '' }}>Jawaban Singkat</option>
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
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="5" placeholder="Tulis pertanyaan atau soal di sini..." required>{{ old('question', $exerciseItem->question ?? '') }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pilihan Jawaban (untuk Pilihan Ganda) -->
                        <div class="mb-3" id="optionsContainer" style="{{ (old('question_type', $exerciseItem->question_type ?? '') == 'pilihan_ganda') ? '' : 'display:none' }}">
                            <label class="form-label">Pilihan Jawaban</label>
                            @for($i = 0; $i < 5; $i++)
                            @php
                                $optionKey = chr(65 + $i); // A, B, C, D, E
                                $optionField = 'option_' . strtolower($optionKey);
                                $optionValue = old("options.{$i}", $exerciseItem->{$optionField} ?? '');
                            @endphp
                            <div class="input-group mb-2">
                                <span class="input-group-text">{{ $optionKey }}</span>
                                <input type="text" class="form-control" name="options[]" value="{{ $optionValue }}" placeholder="Pilihan {{ $optionKey }}">
                            </div>
                            @endfor
                        </div>

                        <!-- Kunci Jawaban -->
                        <div class="mb-3">
                            <label for="answer" class="form-label">Kunci Jawaban</label>
                            <input type="text" class="form-control @error('answer') is-invalid @enderror" id="answer" name="answer" value="{{ old('answer', $exerciseItem->correct_answer ?? '') }}" placeholder="Untuk pilihan ganda: A/B/C/D/E">
                            @error('answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Untuk pilihan ganda, isi dengan huruf (A/B/C/D/E). Untuk essai/jawaban singkat, isi dengan jawaban yang benar.</small>
                        </div>

                        <!-- Pilih Kelas -->
                        <div class="mb-3">
                            <label class="form-label">Bagikan ke Kelas</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllClassrooms">
                                        <label class="form-check-label fw-bold" for="selectAllClassrooms">
                                            Pilih Semua
                                        </label>
                                    </div>
                                    <hr>
                                    @php
                                        $sharedClasses = is_array($exercise->shared_to_classes) ? $exercise->shared_to_classes : (json_decode($exercise->shared_to_classes, true) ?? []);
                                    @endphp
                                    @foreach($classrooms as $classroom)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input classroom-checkbox" type="checkbox" name="classrooms[]" 
                                               value="{{ $classroom->id }}" id="classroom{{ $classroom->id }}"
                                               {{ in_array($classroom->id, old('classrooms', $sharedClasses)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="classroom{{ $classroom->id }}">
                                            {{ $classroom->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">Pilih kelas yang akan dapat mengakses soal ini</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i>Update
                            </button>
                            <a href="{{ route('guru.soal.list-direct', [$serial->id, $category]) }}" class="btn btn-label-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-label-info">
                <div class="card-body">
                    <h6 class="mb-3"><i class='bx bx-info-circle me-2'></i>Informasi</h6>
                    <ul class="mb-0">
                        <li>Pilih tipe dan jenis soal yang sesuai</li>
                        <li>Judul soal harus jelas dan deskriptif</li>
                        <li>Untuk pilihan ganda, isi minimal 2 opsi</li>
                        <li>Kunci jawaban wajib diisi</li>
                        <li>Pilih kelas yang akan mengakses soal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionTypeSelect = document.getElementById('question_type');
    const optionsContainer = document.getElementById('optionsContainer');
    const selectAllCheckbox = document.getElementById('selectAllClassrooms');
    const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');

    // Toggle options based on question type
    questionTypeSelect.addEventListener('change', function() {
        if (this.value === 'pilihan_ganda') {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    });

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
});
</script>
@endpush
@endsection

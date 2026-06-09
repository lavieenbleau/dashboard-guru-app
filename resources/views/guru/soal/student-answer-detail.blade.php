@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, 'tambahan']) }}">Soal Tambahan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.student-results', [$serial->id, $lesson->id, $exercise->id]) }}">Hasil Pengerjaan</a></li>
            <li class="breadcrumb-item active">Detail Jawaban Siswa</li>
        </ol>
    </nav>

    @php
        $typeStr = strtoupper($exercise->exerciseType->kode ?? $exercise->exerciseType->name ?? '');
        $isAkmOrAspd = str_contains($typeStr, 'AKM') || str_contains($typeStr, 'ASPD');
    @endphp

    <div class="row">
        <div class="col-12">
            <!-- Header Information -->
            <div class="card mb-4 bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-1"><i class="bx bx-user me-2"></i>{{ $student->name }}</h4>
                            <p class="mb-0">{{ $exercise->title }}</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <h5 class="text-white mb-0">Total Nilai Saat Ini</h5>
                            <h2 class="text-white mb-0 fw-bold" id="header-total-score">{{ $exercisePoint->exercise_point ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Penilaian -->
            <form id="grading-form">
                @csrf
                <!-- Daftar Soal -->
                @forelse($exercise->exerciseItems as $item)
                    <div class="card mb-4">
                        <div class="card-body">
                            <!-- Informasi Soal -->
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <div>
                                    <span class="badge bg-primary me-1">Soal #{{ $item->exercise_number }}</span>
                                    @if($item->exerciseModel)
                                        <span class="badge bg-info">{{ $item->exerciseModel->name }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Pertanyaan -->
                            <div class="mb-4">
                                <div class="text-dark fs-6">{!! $item->question !!}</div>
                            </div>

                            <div class="row">
                                <!-- Kunci Jawaban -->
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="p-3 border rounded h-100 bg-light">
                                        <h6 class="fw-bold text-success"><i class="bx bx-check-circle me-1"></i>Kunci Jawaban</h6>
                                        @php
                                            $ans = $item->answer ?? '-';
                                            if (is_string($ans)) {
                                                $decoded = json_decode($ans, true);
                                                if (json_last_error() === JSON_ERROR_NONE) { $ans = $decoded; }
                                            }
                                            if (is_string($ans)) {
                                                $decoded = json_decode($ans, true);
                                                if (json_last_error() === JSON_ERROR_NONE) { $ans = $decoded; }
                                            }
                                            if (is_array($ans)) { $ans = implode(', ', $ans); }
                                            $ans = strip_tags((string)$ans);
                                        @endphp
                                        <div class="mt-2">{{ $ans }}</div>
                                    </div>
                                </div>

                                <!-- Jawaban Siswa -->
                                <div class="col-md-6">
                                    <div class="p-3 border rounded h-100">
                                        <h6 class="fw-bold text-primary"><i class="bx bx-edit-alt me-1"></i>Jawaban Siswa</h6>
                                        @php
                                            $studentAns = $studentAnswers[$item->id] ?? '-';
                                            if (is_array($studentAns)) { $studentAns = implode(', ', $studentAns); }
                                        @endphp
                                        <div class="mt-2">{{ $studentAns }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Penilaian Manual -->
                            @if($isAkmOrAspd)
                                <div class="mt-4 pt-3 border-top">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold mb-0">Nilai Manual:</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="number" 
                                                    name="grades[{{ $item->id }}]" 
                                                    class="form-control grade-input" 
                                                    min="0" 
                                                    max="100"
                                                    step="0.01"
                                                    value="{{ $competencePoints[$item->id] ?? 0 }}"
                                                    placeholder="0-100">
                                                <span class="input-group-text">Pts</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">
                        Tidak ada soal yang ditemukan pada exercise ini.
                    </div>
                @endforelse

                <!-- Ringkasan & Tombol Simpan -->
                <div class="card mb-5 border-primary shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-3">Ringkasan Penilaian</h5>
                                <div class="d-flex gap-4">
                                    <div>
                                        <p class="mb-1 text-muted">Jumlah Soal Dinilai</p>
                                        <h5 class="mb-0" id="graded-count">{{ count($competencePoints) }}</h5>
                                    </div>
                                    <div class="border-start ps-4">
                                        <p class="mb-1 text-muted">Belum Dinilai</p>
                                        <h5 class="mb-0 text-warning" id="ungraded-count">{{ max(0, $exercise->exerciseItems->count() - count($competencePoints)) }}</h5>
                                    </div>
                                    <div class="border-start ps-4">
                                        <p class="mb-1 text-muted">Total Skor Akhir</p>
                                        <h5 class="mb-0 text-primary fw-bold" id="summary-total-score">{{ $exercisePoint->exercise_point ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                                @if($isAkmOrAspd)
                                    <button type="button" id="btn-save-grades" class="btn btn-primary btn-lg px-4">
                                        <i class="bx bx-save me-2"></i>Simpan Penilaian
                                    </button>
                                @else
                                    <div class="alert alert-info mb-0 d-inline-block text-start">
                                        <i class="bx bx-info-circle me-1"></i> Penilaian manual hanya tersedia untuk AKM/ASPD.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeInputs = document.querySelectorAll('.grade-input');
    const totalScoreEls = [document.getElementById('header-total-score'), document.getElementById('summary-total-score')];
    const gradedCountEl = document.getElementById('graded-count');
    const ungradedCountEl = document.getElementById('ungraded-count');
    const totalQuestions = {{ $exercise->exerciseItems->count() }};
    const btnSave = document.getElementById('btn-save-grades');
    
    // Auto-calculate on input change
    gradeInputs.forEach(input => {
        input.addEventListener('input', updateSummary);
    });

    function updateSummary() {
        let total = 0;
        let graded = 0;

        gradeInputs.forEach(input => {
            let val = parseFloat(input.value);
            if (!isNaN(val)) {
                total += val;
                graded++;
            }
        });

        // Calculate average based on total questions in the exercise
        let finalScore = totalQuestions > 0 ? (total / totalQuestions) : 0;

        // Update UI
        totalScoreEls.forEach(el => {
            if(el) el.innerText = finalScore.toFixed(1).replace(/\.0$/, '');
        });
        
        if (gradedCountEl) gradedCountEl.innerText = graded;
        if (ungradedCountEl) ungradedCountEl.innerText = Math.max(0, totalQuestions - graded);
    }

    if (btnSave) {
        btnSave.addEventListener('click', function() {
            // Validate
            let isValid = true;
            gradeInputs.forEach(input => {
                if (parseFloat(input.value) < 0) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                showError('Terdapat nilai negatif yang tidak valid. Mohon periksa kembali.');
                return;
            }

            // Prepare data
            const form = document.getElementById('grading-form');
            const formData = new FormData(form);

            // Loading state
            const originalText = btnSave.innerHTML;
            btnSave.innerHTML = '<i class="bx bx-loader bx-spin me-2"></i>Menyimpan...';
            btnSave.disabled = true;

            // Submit AJAX
            fetch("{{ route('guru.soal.save-manual-grade', [$serial->id, $lesson->id, $exercise->id, $student->id]) }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;

                if (data.success) {
                    // Show success
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil!', data.message, 'success');
                    } else {
                        showError(data.message);
                    }
                    
                    if (data.total_score !== undefined) {
                        totalScoreEls.forEach(el => {
                            if(el) el.innerText = parseFloat(data.total_score).toFixed(1).replace(/\.0$/, '');
                        });
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    } else {
                        showError(data.message || 'Terjadi kesalahan saat menyimpan.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;
                showError('Terjadi kesalahan pada sistem.');
            });
        });
    }
});
</script>
@endsection

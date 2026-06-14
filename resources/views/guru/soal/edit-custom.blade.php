@extends('layouts.sneat')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    /* Summernote constraints */
    .note-editor .note-editing-area { min-height: 250px; max-height: 400px; overflow-y: auto; }
    .summernote-option-container .note-editor .note-editing-area { min-height: 120px; max-height: 150px; }
    .note-editor .note-dropzone { opacity: 0 !important; }
    
    /* Sticky Footer */
    .sticky-footer {
        position: sticky;
        bottom: 0;
        z-index: 1020;
        background: white;
        border-top: 1px solid #d9dee3;
        padding: 1rem;
        box-shadow: 0 -0.125rem 0.25rem rgba(161, 172, 184, 0.075);
    }

    /* Navigator */
    .question-navigator {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .btn-nav-soal {
        min-width: 80px;
    }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item active">Edit Soal</li>
        </ol>
    </nav>

    <form id="mainForm" action="{{ route('guru.soal.update-custom', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Hidden fields for package level data to satisfy validation -->
        <input type="hidden" name="lesson_id" value="{{ $exercise->lesson_id ?? $lesson->id }}">
        <input type="hidden" name="exercise_type_id" value="{{ $exercise->exercise_type_id }}">
        <input type="hidden" name="title" value="{{ $exercise->title }}">
        <input type="hidden" name="time_limit" value="{{ $exercise->time_limit }}">

        <!-- Navigator Horizontal -->
        <div class="question-navigator" id="questionNavigator">
            @foreach($exercise->exerciseItems as $index => $item)
                <button type="button" class="btn btn-nav-soal {{ $index === 0 ? 'btn-primary' : 'btn-outline-primary' }}" data-index="{{ $index }}">
                    Soal {{ $index + 1 }}
                </button>
            @endforeach
            <button type="button" class="btn btn-outline-success" id="btnTambahSoal">
                <i class='bx bx-plus me-1'></i>Tambah Soal
            </button>
        </div>

        <!-- Panels Container -->
        <div id="panelsContainer">
            @foreach($exercise->exerciseItems as $index => $item)
                <div class="question-panel" id="panel-{{ $index }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}" class="item-id-input">
                    <input type="hidden" name="items[{{ $index }}][question_type]" value="{{ $item->exercise_model_id }}" class="item-type-input">

                    <!-- Card Informasi -->
                    <div class="card shadow-none border mb-4">
                        <div class="card-body pb-0">
                            <h5 class="mb-2">Soal #<span class="soal-number-display">{{ $index + 1 }}</span></h5>
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                @if(!empty($item->competence_id) && $item->competence)
                                    <span class="badge bg-label-warning">KD {{ $item->competence->point }}</span>
                                @endif
                                <span class="badge bg-label-info">{{ $item->exerciseModel->name ?? 'Tipe Soal' }}</span>
                                <span class="badge bg-label-primary">Dibuat Guru</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Pertanyaan -->
                    <div class="card shadow-none border mb-4">
                        <div class="card-header pb-0"><h6 class="mb-0">Pertanyaan</h6></div>
                        <div class="card-body mt-3">
                            <textarea class="form-control summernote-question" name="items[{{ $index }}][question]" required>{!! old("items.{$index}.question", $item->question ?? '') !!}</textarea>
                        </div>
                    </div>

                    <!-- Opsi & Jawaban berdasarkan Model -->
                    @php
                        $modelId = $item->exercise_model_id;
                        $selection = is_array($item->selection) ? $item->selection : json_decode($item->selection, true) ?? [];
                        $answers = is_array($item->answer) ? $item->answer : json_decode($item->answer, true) ?? [];
                    @endphp

                    @if($modelId == 1) {{-- Pilihan Ganda --}}
                        <div class="card shadow-none border mb-4 summernote-option-container">
                            <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban</h6></div>
                            <div class="card-body mt-3">
                                <div class="row">
                                    @foreach(['A', 'B', 'C', 'D'] as $i => $abjad)
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold text-dark">Pilihan {{ $abjad }}</label>
                                        <textarea class="form-control summernote-selection" name="items[{{ $index }}][selection][]">{!! $selection[$i] ?? '' !!}</textarea>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-none border mb-4">
                            <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban</h6></div>
                            <div class="card-body mt-3">
                                <select name="items[{{ $index }}][answer]" class="form-select answer-select" required>
                                    <option value="">-- Pilih Kunci Jawaban --</option>
                                    @foreach(['A', 'B', 'C', 'D'] as $abjad)
                                        <option value="{{ $abjad }}" {{ in_array($abjad, $answers) ? 'selected' : '' }}>{{ $abjad }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    @elseif($modelId == 2) {{-- Pilihan Ganda Kompleks --}}
                        <div class="card shadow-none border mb-4 summernote-option-container">
                            <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban</h6></div>
                            <div class="card-body mt-3">
                                <div class="row">
                                    @foreach(['A', 'B', 'C', 'D'] as $i => $abjad)
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold text-dark">Pilihan {{ $abjad }}</label>
                                        <textarea class="form-control summernote-selection" name="items[{{ $index }}][selection][]">{!! $selection[$i] ?? '' !!}</textarea>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-none border mb-4">
                            <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban (Bisa lebih dari satu)</h6></div>
                            <div class="card-body mt-3">
                                <div class="d-flex gap-4 p-3 bg-light border rounded flex-wrap">
                                    @foreach(['A', 'B', 'C', 'D'] as $abjad)
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input answer-checkbox" name="items[{{ $index }}][answer][]" value="{{ $abjad }}" id="q{{$index}}ans{{$abjad}}" {{ in_array($abjad, $answers) ? 'checked' : '' }}>
                                            <label for="q{{$index}}ans{{$abjad}}" class="form-check-label fw-bold">{{ $abjad }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @elseif($modelId == 3) {{-- Benar Salah --}}
                        <div class="card shadow-none border mb-4">
                            <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban</h6></div>
                            <div class="card-body mt-3">
                                <select name="items[{{ $index }}][answer]" class="form-select answer-select" required>
                                    <option value="">-- Pilih Benar/Salah --</option>
                                    <option value="Benar" {{ in_array('Benar', $answers) ? 'selected' : '' }}>Benar</option>
                                    <option value="Salah" {{ in_array('Salah', $answers) ? 'selected' : '' }}>Salah</option>
                                </select>
                            </div>
                        </div>

                    @else {{-- Essai / Singkat --}}
                        <div class="card shadow-none border mb-4">
                            <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban / Kata Kunci</h6></div>
                            <div class="card-body mt-3">
                                <textarea class="form-control summernote-answer answer-textarea" name="items[{{ $index }}][answer]">{!! $item->answer !!}</textarea>
                                <small class="text-muted mt-2 d-block">Biarkan kosong jika akan diperiksa manual oleh guru.</small>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Sticky Footer -->
        <div class="sticky-footer d-flex justify-content-end gap-2 mt-4 rounded">
            <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, $category]) }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="button" class="btn btn-primary" id="btnSimpan">
                <i class='bx bx-save me-1'></i> Simpan Perubahan
            </button>
            <button type="button" class="btn btn-success" id="btnSimpanLanjut">
                <i class='bx bx-check-double me-1'></i> Simpan & Lanjut
            </button>
        </div>
    </form>
</div>

<!-- Template for New Panel -->
<template id="newPanelTemplate">
    <div class="question-panel is-new" style="display:none;">
        <input type="hidden" name="question_type" value="{{ $exercise->exerciseItems->first()->exercise_model_id ?? 1 }}" class="item-type-input">
        
        <!-- Card Informasi -->
        <div class="card shadow-none border mb-4">
            <div class="card-body pb-0">
                <h5 class="mb-2">Soal #<span class="soal-number-display">NEW</span></h5>
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <span class="badge bg-label-info">{{ $exercise->exerciseItems->first()->exerciseModel->name ?? 'Pilihan Ganda' }}</span>
                    <span class="badge bg-label-primary">Dibuat Guru</span>
                </div>
            </div>
        </div>

        <!-- Card Pertanyaan -->
        <div class="card shadow-none border mb-4">
            <div class="card-header pb-0"><h6 class="mb-0">Pertanyaan</h6></div>
            <div class="card-body mt-3">
                <textarea class="form-control summernote-question" name="question" required></textarea>
            </div>
        </div>

        @php
            $templateModelId = $exercise->exerciseItems->first()->exercise_model_id ?? 1;
        @endphp

        @if($templateModelId == 1)
            <div class="card shadow-none border mb-4 summernote-option-container">
                <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban</h6></div>
                <div class="card-body mt-3">
                    <div class="row">
                        @foreach(['A', 'B', 'C', 'D'] as $abjad)
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark">Pilihan {{ $abjad }}</label>
                            <textarea class="form-control summernote-selection" name="selection[]"></textarea>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card shadow-none border mb-4">
                <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban</h6></div>
                <div class="card-body mt-3">
                    <select name="answer" class="form-select answer-select" required>
                        <option value="">-- Pilih Kunci Jawaban --</option>
                        @foreach(['A', 'B', 'C', 'D'] as $abjad)
                            <option value="{{ $abjad }}">{{ $abjad }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @elseif($templateModelId == 2)
            <div class="card shadow-none border mb-4 summernote-option-container">
                <div class="card-header pb-0"><h6 class="mb-0">Pilihan Jawaban</h6></div>
                <div class="card-body mt-3">
                    <div class="row">
                        @foreach(['A', 'B', 'C', 'D'] as $abjad)
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark">Pilihan {{ $abjad }}</label>
                            <textarea class="form-control summernote-selection" name="selection[]"></textarea>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card shadow-none border mb-4">
                <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban (Bisa lebih dari satu)</h6></div>
                <div class="card-body mt-3">
                    <div class="d-flex gap-4 p-3 bg-light border rounded flex-wrap">
                        @foreach(['A', 'B', 'C', 'D'] as $abjad)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input answer-checkbox" name="answer[]" value="{{ $abjad }}" id="new_ans_{{ $abjad }}">
                                <label for="new_ans_{{ $abjad }}" class="form-check-label fw-bold">{{ $abjad }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($templateModelId == 3)
            <div class="card shadow-none border mb-4">
                <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban</h6></div>
                <div class="card-body mt-3">
                    <select name="answer" class="form-select answer-select" required>
                        <option value="">-- Pilih Benar/Salah --</option>
                        <option value="Benar">Benar</option>
                        <option value="Salah">Salah</option>
                    </select>
                </div>
            </div>
        @else
            <div class="card shadow-none border mb-4">
                <div class="card-header pb-0"><h6 class="mb-0">Kunci Jawaban / Kata Kunci</h6></div>
                <div class="card-body mt-3">
                    <textarea class="form-control summernote-answer answer-textarea" name="answer"></textarea>
                </div>
            </div>
        @endif
    </div>
</template>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPanelIndex = 0;
    const navigatorContainer = document.getElementById('questionNavigator');
    const panelsContainer = document.getElementById('panelsContainer');
    const template = document.getElementById('newPanelTemplate');

    // Initialize Summernote
    function initSummernote(element) {
        $(element).summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['codeview']]
            ]
        });
    }

    function initSummernoteOption(element) {
        $(element).summernote({
            height: 120,
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['insert', ['link', 'picture']]
            ]
        });
    }

    // Initialize existing Summernotes
    $('.summernote-question, .summernote-answer').each(function() { initSummernote(this); });
    $('.summernote-selection').each(function() { initSummernoteOption(this); });

    // Handle Navigation Click
    navigatorContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-nav-soal')) {
            const index = parseInt(e.target.getAttribute('data-index'));
            switchPanel(index);
        }
    });

    function switchPanel(index) {
        // Update Buttons
        document.querySelectorAll('.btn-nav-soal').forEach(btn => {
            if(parseInt(btn.getAttribute('data-index')) === index) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            }
        });

        // Show/Hide Panels
        document.querySelectorAll('.question-panel').forEach((panel, i) => {
            if (i === index) {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        });
        currentPanelIndex = index;
    }

    // Handle Tambah Soal
    document.getElementById('btnTambahSoal').addEventListener('click', function() {
        const newIndex = document.querySelectorAll('.question-panel').length;
        
        // 1. Create Nav Button
        const newBtn = document.createElement('button');
        newBtn.type = 'button';
        newBtn.className = 'btn btn-outline-primary btn-nav-soal';
        newBtn.setAttribute('data-index', newIndex);
        newBtn.innerText = 'Soal ' + (newIndex + 1);
        
        navigatorContainer.insertBefore(newBtn, this);

        // 2. Clone Panel
        const clone = template.content.cloneNode(true);
        const newPanel = clone.querySelector('.question-panel');
        newPanel.id = 'panel-' + newIndex;
        newPanel.querySelector('.soal-number-display').innerText = (newIndex + 1);
        
        // Fix Checkbox IDs to avoid conflicts
        newPanel.querySelectorAll('.answer-checkbox').forEach(cb => {
            const oldId = cb.id;
            const newId = oldId + '_' + newIndex;
            cb.id = newId;
            const label = cb.nextElementSibling;
            if(label && label.tagName === 'LABEL') label.setAttribute('for', newId);
        });

        panelsContainer.appendChild(newPanel);

        // 3. Initialize Summernotes
        const panelEl = document.getElementById('panel-' + newIndex);
        $(panelEl.querySelectorAll('.summernote-question, .summernote-answer')).each(function() { initSummernote(this); });
        $(panelEl.querySelectorAll('.summernote-selection')).each(function() { initSummernoteOption(this); });

        // 4. Switch to new panel
        switchPanel(newIndex);
    });

    // Handle Simpan Action
    async function processSimpan(redirectListDirect) {
        const btnSimpan = document.getElementById('btnSimpan');
        const btnSimpanLanjut = document.getElementById('btnSimpanLanjut');
        
        btnSimpan.disabled = true;
        btnSimpanLanjut.disabled = true;
        btnSimpan.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i>Menyimpan...';

        const newPanels = document.querySelectorAll('.question-panel.is-new');
        
        try {
            // Process new items via storeCustomItem
            for (const panel of newPanels) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                
                const qType = panel.querySelector('.item-type-input').value;
                formData.append('question_type', qType);
                
                const qContent = panel.querySelector('.summernote-question').value;
                formData.append('question', qContent);
                
                if (qType == 1 || qType == 2) {
                    const selections = panel.querySelectorAll('.summernote-selection');
                    selections.forEach(sel => formData.append('selection[]', sel.value));
                    
                    if (qType == 1) {
                        const ansSelect = panel.querySelector('.answer-select');
                        if (ansSelect) formData.append('answer', ansSelect.value);
                    } else {
                        const ansCheckboxes = panel.querySelectorAll('.answer-checkbox:checked');
                        ansCheckboxes.forEach(cb => formData.append('answer[]', cb.value));
                    }
                } else if (qType == 3) {
                    const ansSelect = panel.querySelector('.answer-select');
                    if (ansSelect) formData.append('answer', ansSelect.value);
                } else {
                    const ansTextarea = panel.querySelector('.answer-textarea');
                    if (ansTextarea) formData.append('answer', ansTextarea.value);
                }
                
                await fetch('{{ route("guru.soal.store-custom-item", [$serial->id, $lesson->id, $exercise->id]) }}', {
                    method: 'POST',
                    body: formData
                });
                
                // Remove the panel so updateCustom doesn't try to process it
                panel.remove();
            }

            // After all new items are saved via AJAX, we submit the main form to update existing items
            if (redirectListDirect) {
                document.getElementById('mainForm').submit();
            } else {
                // If "Simpan & Lanjut", we can just submit but wait, standard submit redirects to list.
                // We should append a query param or hidden input to let controller know? 
                // Unfortunately controller hardcodes redirect to list-direct.
                // To force stay, we can either submit via fetch or just let it redirect.
                // The user's specification didn't mention modifying controller.
                // If they click "Simpan & Lanjut", we can't change controller's redirect.
                // We will submit via fetch and then reload the page!
                const mainFormData = new FormData(document.getElementById('mainForm'));
                await fetch('{{ route("guru.soal.update-custom", [$serial->id, $lesson->id, $exercise->id]) }}', {
                    method: 'POST',
                    body: mainFormData
                });
                window.location.reload();
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menyimpan data.');
            btnSimpan.disabled = false;
            btnSimpanLanjut.disabled = false;
            btnSimpan.innerHTML = '<i class="bx bx-save me-1"></i> Simpan Perubahan';
        }
    }

    document.getElementById('btnSimpan').addEventListener('click', function(e) {
        e.preventDefault();
        processSimpan(true); // Redirects back to list
    });

    document.getElementById('btnSimpanLanjut').addEventListener('click', function(e) {
        e.preventDefault();
        processSimpan(false); // Stays on page (reloads)
    });
});
</script>
@endsection

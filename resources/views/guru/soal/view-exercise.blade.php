@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <style>
        .card-body img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
        }
    </style>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, 'tambahan']) }}">Soal Tambahan</a></li>
            <li class="breadcrumb-item active">Lihat Soal</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Exercise Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <div class="flex-grow-1">
                            <h4 class="mb-2">{{ $exercise->title }}</h4>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($exercise->lesson && $exercise->lesson->mapel)
                                    <span class="badge bg-label-info">
                                        <i class='bx bx-book me-1'></i>{{ $exercise->lesson->mapel->name }}
                                    </span>
                                @endif
                                
                                @if($exercise->exerciseType)
                                    <span class="badge bg-label-secondary">
                                        {{ $exercise->exerciseType->name }}
                                    </span>
                                @endif
                                
                                @if($exercise->shared_to_classes)
                                @php
                                    $sharedCount = count(json_decode($exercise->shared_to_classes, true) ?? []);
                                @endphp
                                @if($sharedCount > 0)
                                <span class="badge bg-label-success">
                                    <i class='bx bx-share-alt'></i> Shared ke {{ $sharedCount }} kelas
                                </span>
                                @endif
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-start align-items-md-end gap-2">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, 'tambahan']) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class='bx bx-arrow-back me-1'></i>Kembali
                                </a>
                                @if($exercise->is_admin != 1)
                                <a href="{{ route('guru.soal.edit-custom', [$serial->id, $lesson->id, $exercise->id]) }}" class="btn btn-sm btn-primary">
                                    <i class='bx bx-edit me-1'></i>Edit Soal
                                </a>
                                <form method="POST" action="{{ route('guru.soal.destroy-custom', [$serial->id, $lesson->id, $exercise->id]) }}" class="d-inline" onsubmit="confirmSubmit(event, 'Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus soal ini?', 'Ya, Hapus', true);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class='bx bx-trash me-1'></i>Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                            <small class="text-muted mt-1">Dibuat: {{ $exercise->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('guru.soal.view-exercise', [$serial->id, $lesson->id, $exercise->id]) }}">
                        <i class="bx bx-list-ol me-1"></i> Detail Soal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guru.soal.student-results', [$serial->id, $lesson->id, $exercise->id]) }}">
                        <i class="bx bx-check-shield me-1"></i> Hasil Pengerjaan Siswa
                    </a>
                </li>
            </ul>

            <!-- Questions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Soal ({{ $exercise->exerciseItems->count() }} soal)</h5>
                </div>
                <div class="card-body">
                    @forelse($exercise->exerciseItems as $item)
                        <div class="mb-4 pb-4 @if(!$loop->last) border-bottom @endif">
                            <!-- Question Number & Type -->
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">Soal #{{ $item->exercise_number }}</span>
                                @if($item->exerciseModel)
                                    <span class="badge bg-info">{{ $item->exerciseModel->name }}</span>
                                @endif
                            </div>

                            <!-- Question Text -->
                            <div class="mb-3">
                                @if(!empty($item->competence_id) && $item->competence)
                                    <div class="mb-2">
                                        <span class="badge bg-label-warning">
                                            KD: {{ $item->competence->point }}{{ $item->competence->description ? ' - ' . \Illuminate\Support\Str::limit($item->competence->description, 30) : '' }}
                                        </span>
                                    </div>
                                @endif
                                <h6 class="fw-bold mb-2">Pertanyaan:</h6>
                                <div class="text-dark">{!! $item->question !!}</div>
                            </div>

                            <!-- Options (if multiple choice) -->
                            @if($item->exerciseModel && $item->exerciseModel->name && str_contains($item->exerciseModel->name, 'Pilihan'))
                                @php
                                    $answers = is_array($item->answer) ? $item->answer : json_decode($item->answer, true) ?? [];
                                    if(!is_array($answers)) $answers = [$answers];
                                @endphp
                                @if($item->selection)
                                    <div class="mb-3">
                                        <h6 class="fw-bold mb-2">Pilihan Jawaban:</h6>
                                        @php
                                            $letters = ['A', 'B', 'C', 'D', 'E'];
                                            
                                            // Fallback to legacy options column if selection is empty
                                            $selectionRaw = (!empty($item->selection) && $item->selection !== '[]' && $item->selection !== '"[]"') 
                                                ? $item->selection 
                                                : $item->options;
                                            
                                            // Robust decoding for selection (handles legacy double encoding)
                                            if (is_string($selectionRaw)) {
                                                $decoded = json_decode($selectionRaw, true);
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    $selectionRaw = $decoded;
                                                }
                                            }
                                            // Second pass for double encoding
                                            if (is_string($selectionRaw)) {
                                                $decoded = json_decode($selectionRaw, true);
                                                if (json_last_error() !== JSON_ERROR_NONE) {
                                                    $decoded = json_decode(stripslashes($selectionRaw), true);
                                                }
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    $selectionRaw = $decoded;
                                                }
                                            }
                                            
                                            $selection = is_array($selectionRaw) ? $selectionRaw : [];
                                        @endphp
                                        @if(!empty($selection))
                                            <style>
                                                .option-content p:last-child { margin-bottom: 0; }
                                                .option-content p { margin-top: 0; }
                                            </style>
                                            <div class="options-list">
                                                @foreach($letters as $index => $letter)
                                                    @if(isset($selection[$index]) && $selection[$index])
                                                        <div class="option-item mb-2 d-flex align-items-start {{ in_array($letter, $answers) || in_array((string)$index, $answers) ? 'text-success fw-bold' : '' }}">
                                                            <strong class="me-2">{{ $letter }}.</strong> 
                                                            <div class="option-content">{!! $selection[$index] !!}</div>
                                                        </div>
                                                    @elseif(isset($selection[$letter]) && $selection[$letter])
                                                        <div class="option-item mb-2 d-flex align-items-start {{ in_array($letter, $answers) || in_array((string)$index, $answers) ? 'text-success fw-bold' : '' }}">
                                                            <strong class="me-2">{{ $letter }}.</strong> 
                                                            <div class="option-content">{!! $selection[$letter] !!}</div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif

                            <!-- Answer Key -->
                            <div class="alert alert-info mb-0">
                                <strong><i class='bx bx-check-circle me-1'></i>Kunci Jawaban:</strong>
                                @php
                                    $ans = $item->answer ?? 'Tidak ada';
                                    
                                    // Robust decoding
                                    if (is_string($ans)) {
                                        $decoded = json_decode($ans, true);
                                        if (json_last_error() === JSON_ERROR_NONE) {
                                            $ans = $decoded;
                                        }
                                    }
                                    // Second pass in case of double-encoding
                                    if (is_string($ans)) {
                                        $decoded = json_decode($ans, true);
                                        if (json_last_error() === JSON_ERROR_NONE) {
                                            $ans = $decoded;
                                        }
                                    }
                                    
                                    if (is_array($ans)) {
                                        $ans = implode(', ', $ans);
                                    }
                                    
                                    // Hilangkan tag HTML
                                    $ans = strip_tags((string)$ans);
                                @endphp
                                <div class="mb-0 mt-2">{{ $ans }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class='bx bx-exclamation-circle me-2'></i>Tidak ada soal yang ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>


        </div>
    </div>
</div>
@endsection

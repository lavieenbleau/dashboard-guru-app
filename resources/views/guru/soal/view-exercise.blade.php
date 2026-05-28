@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, 'tambahan']) }}">Soal Tambahan</a></li>
            <li class="breadcrumb-item active">Lihat Soal</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Exercise Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
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
                        <div>
                            <small class="text-muted d-block text-end">Dibuat: {{ $exercise->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

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
                                <h6 class="fw-bold mb-2">Pertanyaan:</h6>
                                <p class="text-dark">{!! nl2br(e($item->question)) !!}</p>
                            </div>

                            <!-- Options (if multiple choice) -->
                            @if($item->exerciseModel && $item->exerciseModel->name && str_contains($item->exerciseModel->name, 'Pilihan'))
                                @if($item->selection)
                                    <div class="mb-3">
                                        <h6 class="fw-bold mb-2">Pilihan Jawaban:</h6>
                                        <div class="options-list">
                                            @php
                                                $options = json_decode($item->selection, true) ?? [];
                                                $letters = ['A', 'B', 'C', 'D', 'E'];
                                            @endphp
                                            @foreach($letters as $index => $letter)
                                                @if(isset($options[$letter]) && $options[$letter])
                                                    <div class="option-item p-2 mb-2 border rounded bg-light">
                                                        <strong>{{ $letter }}.</strong> {{ $options[$letter] }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <!-- Answer Key -->
                            <div class="alert alert-info mb-0">
                                <strong><i class='bx bx-check-circle me-1'></i>Kunci Jawaban:</strong>
                                <p class="mb-0 mt-2">{!! nl2br(e($item->answer ?? 'Tidak ada')) !!}</p>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class='bx bx-exclamation-circle me-2'></i>Tidak ada soal yang ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body d-flex gap-2">
                    <a href="{{ route('guru.soal.list-direct', [$serial->id, 'tambahan']) }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back me-1'></i>Kembali
                    </a>
                    <a href="{{ route('guru.soal.edit-custom', [$serial->id, $exercise->id]) }}" class="btn btn-primary">
                        <i class='bx bx-edit me-1'></i>Edit Soal
                    </a>
                    <form method="POST" action="{{ route('guru.soal.destroy-custom', [$serial->id, $exercise->id]) }}" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class='bx bx-trash me-1'></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item active">{{ $categoryInfo['name'] }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">{{ $categoryInfo['name'] }}</h4>
            </div>
            <div class="d-flex gap-2">
                @if($category === 'tambahan')
                    <!-- Tombol Tambah Soal untuk Soal Tambahan -->
                    <a href="{{ route('guru.soal.ai-generator', [$serial->id, $lesson->id]) }}" class="btn btn-success">
                        <i class='bx bx-brain me-1'></i>Generate Soal dengan AI
                    </a>
                    <a href="{{ route('guru.soal.create-custom', [$serial->id, $lesson->id]) }}" class="btn btn-primary">
                        <i class='bx bx-plus me-1'></i>Tambah Soal Manual
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Exercises List -->
    <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
        @forelse ($exercises as $exercise)
        <div class="col">
            <div class="card shadow-sm exercise-card h-100 d-flex flex-column">
                <div class="card-body flex-grow-1 d-flex flex-column">
                    <div class="mb-2">
                        <h5 class="mb-1">{{ $exercise->title }}</h5>
                    </div>
                    
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @if($exercise->lesson && $exercise->lesson->mapel)
                        <span class="badge bg-label-info">
                            <i class='bx bx-book me-1'></i>{{ $exercise->lesson->mapel->name }}
                        </span>
                        @endif
                        
                        @if($exercise->lesson && $exercise->lesson->curriculum)
                        <span class="badge bg-label-secondary">
                            {{ $exercise->lesson->curriculum }} {{ $exercise->lesson->grade_level }}
                        </span>
                        @endif
                    </div>

                    @php
                        $sharedClassroomIds = \Illuminate\Support\Facades\DB::table('share_exercises')
                            ->where('exercise_id', $exercise->id)
                            ->whereNotNull('classroom_id')
                            ->pluck('classroom_id')
                            ->toArray();
                        $sharedCount = count($sharedClassroomIds);
                        $allClassrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                        $sharedClassroomsList = $allClassrooms->filter(function($c) use ($sharedClassroomIds) {
                            return in_array($c->id, $sharedClassroomIds);
                        });
                    @endphp
                    
                    @if($sharedCount > 0)
                    <div class="mb-3">
                        <span class="badge bg-label-success mb-2">
                            <i class='bx bx-share-alt me-1'></i> Dibagikan ke {{ $sharedCount }} Kelas
                        </span>
                        <div class="text-muted small">
                            @if($sharedCount <= 2)
                                @foreach($sharedClassroomsList as $sc)
                                    <span class="d-block"><i class='bx bx-check text-success me-1'></i>{{ $sc->name }}</span>
                                @endforeach
                            @else
                                <span class="d-block"><i class='bx bx-check text-success me-1'></i>{{ $sharedClassroomsList->first()->name }}</span>
                                <span class="d-block fst-italic">+ {{ $sharedCount - 1 }} kelas lainnya</span>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="mb-3">
                        <span class="badge bg-label-secondary">
                            <i class='bx bx-lock-alt me-1'></i> Belum dibagikan
                        </span>
                    </div>
                    @endif
                    
                    <small class="text-muted mt-auto pt-2 border-top">
                        <i class='bx bx-time'></i> Dibuat: {{ $exercise->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="card-footer bg-transparent border-top p-3">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        @if($category === 'tambahan')
                            <div class="d-flex gap-1">
                                <a href="{{ route('guru.soal.view-exercise', ['serial' => $serial->id, 'lesson' => $lesson->id, 'exerciseId' => $exercise->id]) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Lihat Soal">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('guru.soal.edit-custom', [$serial->id, $lesson->id, $exercise->id]) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit Soal">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <form action="{{ route('guru.soal.destroy-custom', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Hapus soal ini?')" title="Hapus Soal">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div></div>
                        @endif
                        
                        <button type="button" class="btn btn-sm {{ $sharedCount > 0 ? 'btn-outline-primary' : 'btn-primary' }}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#shareModal{{ $exercise->id }}">
                            <i class='bx bx-share-alt me-1'></i> {{ $sharedCount > 0 ? 'Kelola Pembagian' : 'Bagikan' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-folder-open bx-lg text-muted mb-3'></i>
                    <p class="text-muted mb-0">Belum ada soal {{ $categoryInfo['name'] }}.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Share Modals -->
@foreach ($exercises as $exercise)
<div class="modal fade" id="shareModal{{ $exercise->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buka Kuis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.soal.share-direct', [$serial->id, $lesson->id, $category, $exercise->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3"><strong>{{ $exercise->title }}</strong></p>
                    <p class="text-muted small mb-3">Pilih kelas yang dapat mengakses soal ini:</p>
                    
                    @php
                        $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                        $sharedClassroomIds = \Illuminate\Support\Facades\DB::table('share_exercises')
                            ->where('exercise_id', $exercise->id)
                            ->whereNotNull('classroom_id')
                            ->pluck('classroom_id')
                            ->toArray();
                        $sharedCount = count($sharedClassroomIds);
                        $sharedClassroomsList = $classrooms->filter(function($c) use ($sharedClassroomIds) {
                            return in_array($c->id, $sharedClassroomIds);
                        });
                    @endphp

                    @if($sharedCount > 0)
                        <div class="alert alert-success py-2 px-3 mb-3">
                            <i class='bx bx-check-circle me-1'></i> <strong>Saat ini soal dibagikan ke {{ $sharedCount }} kelas.</strong>
                        </div>
                        
                        <div class="mb-3 p-3 bg-lighter rounded">
                            <label class="form-label mb-2 fw-semibold">Dibagikan ke:</label>
                            <ul class="list-unstyled mb-0">
                                @foreach($sharedClassroomsList as $sc)
                                    <li class="mb-1"><i class='bx bx-check text-success me-2'></i>{{ $sc->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        @if($exercise->updated_at)
                        <p class="text-muted small mb-3"><i class='bx bx-time'></i> Terakhir diperbarui: {{ $exercise->updated_at->isoFormat('D MMMM YYYY HH:mm') }}</p>
                        @endif
                    @else
                        <div class="alert alert-secondary py-2 px-3 mb-3">
                            <i class='bx bx-info-circle me-1'></i> <strong>Soal ini belum dibagikan ke kelas manapun.</strong>
                        </div>
                    @endif
                    
                    <p class="text-muted small mb-2 mt-4">Kelola akses kelas (centang untuk memberikan akses):</p>
                    
                    @forelse($classrooms as $classroom)
                        @if(!in_array($classroom->id, $sharedClassroomIds))
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="classrooms[]" 
                                   value="{{ $classroom->id }}" 
                                   id="classroom{{ $exercise->id }}_{{ $classroom->id }}">
                            <label class="form-check-label" for="classroom{{ $exercise->id }}_{{ $classroom->id }}">
                                {{ $classroom->name }}
                            </label>
                        </div>
                        @endif
                    @empty
                    <div class="alert alert-warning">
                        <i class='bx bx-info-circle'></i> Belum ada kelas. Silakan buat kelas terlebih dahulu.
                    </div>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save'></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

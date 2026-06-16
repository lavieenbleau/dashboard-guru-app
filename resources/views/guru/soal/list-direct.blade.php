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
    <div class="row g-3">
        @forelse ($exercises as $exercise)
        <div class="col-12">
            <div class="card shadow-sm exercise-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class='bx bx-file-blank'></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">
                                    {{ $exercise->title }}
                                    @if($exercise->is_admin == 1)
                                        <span class="badge bg-primary ms-2" style="font-size: 0.75em; vertical-align: middle;">Admin - {{ $exercise->exerciseType->name ?? 'Lainnya' }}</span>
                                    @else
                                        <span class="badge bg-success ms-2" style="font-size: 0.75em; vertical-align: middle;">{{ $exercise->exerciseType->name ?? 'Soal Guru' }}</span>
                                    @endif
                                </h6>
                                
                                @php
                                    $competences = collect();
                                    if($exercise->exerciseItems) {
                                        $competences = $exercise->exerciseItems->pluck('competence')->filter()->unique('id');
                                    }
                                @endphp
                                <div class="mt-1 mb-1">
                                    @if($competences->count() > 0)
                                        @foreach($competences as $kd)
                                            <span class="badge bg-label-warning me-1 mb-1" title="{{ $kd->description }}">[KD {{ $kd->point }}{{ $kd->description ? ' - ' . \Illuminate\Support\Str::limit($kd->description, 30) : '' }}]</span>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div class="mt-2 mb-1">
                                    <strong class="text-dark d-block">Kelas:</strong>
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
                                        @php
                                            $classNames = $sharedClassroomsList->pluck('name')->implode(', ');
                                        @endphp
                                        <span class="badge bg-label-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $classNames }}">
                                            Dibagikan ke {{ $sharedCount }} Kelas
                                        </span>
                                    @else
                                        <span class="badge bg-label-danger">Belum Ditentukan</span>
                                    @endif
                                </div>
                                
                                <small class="text-muted d-block mt-2">
                                    <strong class="text-dark">Informasi Tambahan:</strong><br>
                                    @if($exercise->lesson && $exercise->lesson->mapel)
                                    <span class="badge bg-label-info me-1">{{ $exercise->lesson->mapel->name }}</span>
                                    @endif
                                    @if($exercise->lesson && $exercise->lesson->curriculum)
                                    <span class="badge bg-label-secondary me-1">{{ $exercise->lesson->curriculum }} {{ $exercise->lesson->grade_level }}</span>
                                    @endif
                                    <span class="text-muted ms-1"><i class='bx bx-time-five'></i> Dibuat: {{ \Carbon\Carbon::parse($exercise->created_at)->locale('id')->diffForHumans() }}</span>
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('guru.soal.view-exercise', ['serial' => $serial->id, 'lesson' => $lesson->id, 'exerciseId' => $exercise->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-show me-1"></i> Detail Soal
                            </a>
                            <x-action-dropdown>
                                @if($category === 'tambahan' && $exercise->is_admin != 1)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.soal.edit-custom', [$serial->id, $lesson->id, $exercise->id]) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                    </li>
                                @endif
                            
                            <li>
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareModal{{ $exercise->id }}">
                                    <i class='bx bx-share-alt me-1'></i> Kelola Distribusi
                                </button>
                            </li>

                            @if($category === 'tambahan' && $exercise->is_admin != 1)
                                <li>
                                    <form action="{{ route('guru.soal.destroy-custom', [$serial->id, $lesson->id, $exercise->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" onclick="confirmClick(event, 'Konfirmasi Hapus', 'Hapus soal ini?', 'Ya, Hapus', true)">
                                            <i class="bx bx-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </li>
                            @endif
                            </x-action-dropdown>
                        </div>
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
<div class="modal fade" id="shareModal{{ $exercise->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Distribusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

                    <div class="mb-3">
                                        <label class="form-label">Pilih Kelas <span class="text-danger">*</span></label>
                                        <div class="list-group" style="max-height: 200px; overflow-y: auto;">
                                            @forelse($classrooms as $classroom)
                                                <label class="list-group-item">
                                                    <input class="form-check-input me-1" type="checkbox" name="classrooms[]" value="{{ $classroom->id }}" {{ in_array($classroom->id, $sharedClassroomIds) ? 'checked' : '' }}>
                                                    {{ $classroom->name }}
                                                </label>
                                            @empty
                                                <div class="alert alert-warning mb-0">
                                                    <i class='bx bx-info-circle'></i> Belum ada kelas. Silakan buat kelas terlebih dahulu.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

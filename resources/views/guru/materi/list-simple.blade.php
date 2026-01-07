@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.custom', $serial->id) }}">Materi Tambahan</a></li>
            <li class="breadcrumb-item active">{{ $mapel->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Materi Tambahan - {{ $mapel->name }}</h4>
                <p class="text-muted mb-0">Kelola materi untuk mata pelajaran {{ $mapel->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.materi.create', [$serial->id, $mapel->id]) }}" class="btn btn-primary">
                    <i class='bx bx-plus me-1'></i>Tambah Materi
                </a>
                <a href="{{ route('guru.materi.custom', $serial->id) }}" class="btn btn-outline-secondary">
                    <i class='bx bx-arrow-back me-1'></i> Kembali
                </a>
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

    <!-- Materi List -->
    <div class="row g-3">
        @forelse ($materis as $materi)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h5 class="mb-0">{{ $materi->title }}</h5>
                            </div>
                            
                            @if($materi->description)
                            <p class="text-muted mb-3">{{ Str::limit($materi->description, 200) }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($materi->link)
                                <span class="badge bg-label-primary">
                                    <i class='bx bx-link-alt'></i> Link
                                </span>
                                @endif
                                
                                @if($materi->attachment)
                                <span class="badge bg-label-success">
                                    <i class='bx bx-file'></i> File
                                </span>
                                @endif
                                
                                @if($materi->embed)
                                <span class="badge bg-label-info">
                                    <i class='bx bx-video'></i> Embed
                                </span>
                                @endif
                            </div>

                            <small class="text-muted">
                                <i class='bx bx-user'></i> {{ $materi->user->name ?? 'Unknown' }} • 
                                <i class='bx bx-time'></i> {{ $materi->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <!-- Edit/Delete Actions -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#shareModal{{ $materi->id }}">
                                        <i class='bx bx-share-alt me-2'></i>Bagikan
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('guru.materi.edit', [$serial->id, $mapel->id, $materi->id]) }}">
                                        <i class='bx bx-edit me-2'></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('guru.materi.destroy', [$serial->id, $mapel->id, $materi->id]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class='bx bx-trash me-2'></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Content Details -->
                    @if($materi->link || $materi->attachment || $materi->embed)
                    <div class="mt-3 pt-3 border-top">
                        @if($materi->link)
                        <div class="mb-2">
                            <a href="{{ $materi->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external'></i> Buka Link
                            </a>
                        </div>
                        @endif

                        @if($materi->attachment)
                        <div class="mb-2">
                            <a href="{{ Storage::url($materi->attachment) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class='bx bx-download'></i> Download File
                            </a>
                        </div>
                        @endif

                        @if($materi->embed)
                        <div class="mt-3">
                            <div class="ratio ratio-16x9">
                                {!! $materi->embed !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-book-open' style="font-size: 48px; opacity: 0.3;"></i>
                    <h5 class="mt-3">Belum Ada Materi</h5>
                    <p class="text-muted mb-3">Tambahkan materi pertama untuk mata pelajaran {{ $mapel->name }}</p>
                    <a href="{{ route('guru.materi.create', [$serial->id, $mapel->id]) }}" class="btn btn-primary">
                        <i class='bx bx-plus me-1'></i>Tambah Materi
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Share Modals -->
@foreach ($materis as $materi)
<div class="modal fade" id="shareModal{{ $materi->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.materi.share', [$serial->id, $materi->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3"><strong>{{ $materi->title }}</strong></p>
                    <p class="text-muted small mb-3">Pilih kelas yang dapat mengakses materi ini:</p>
                    
                    @php
                        $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                        $sharedClassroomIds = [];
                        if ($materi->shared_to_classes) {
                            $sharedClassroomIds = is_array($materi->shared_to_classes) 
                                ? $materi->shared_to_classes 
                                : json_decode($materi->shared_to_classes, true) ?? [];
                        }
                    @endphp
                    
                    @if($classrooms->count() > 0)
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="selectAll{{ $materi->id }}"
                                   onclick="toggleAllClassrooms{{ $materi->id }}(this)">
                            <label class="form-check-label fw-bold" for="selectAll{{ $materi->id }}">
                                Pilih Semua Kelas
                            </label>
                        </div>
                        <hr class="mb-3">
                    @endif
                    
                    @forelse($classrooms as $classroom)
                    <div class="form-check mb-2">
                        <input class="form-check-input classroom-checkbox-{{ $materi->id }}" type="checkbox" 
                               name="classrooms[]" 
                               value="{{ $classroom->id }}" 
                               id="classroom{{ $materi->id }}_{{ $classroom->id }}"
                               {{ in_array($classroom->id, $sharedClassroomIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="classroom{{ $materi->id }}_{{ $classroom->id }}">
                            {{ $classroom->name }} ({{ $classroom->code }})
                        </label>
                    </div>
                    @empty
                    <div class="alert alert-info">
                        <i class='bx bx-info-circle'></i> Belum ada kelas. Silakan buat kelas terlebih dahulu.
                    </div>
                    @endforelse
                    
                    @if($classrooms->count() > 0)
                        <hr class="mt-3 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   name="as_task" 
                                   value="1"
                                   id="asTask{{ $materi->id }}"
                                   {{ $materi->is_task ? 'checked' : '' }}>
                            <label class="form-check-label" for="asTask{{ $materi->id }}">
                                <i class='bx bx-task me-1'></i> Bagikan sebagai Tugas
                            </label>
                            <small class="form-text text-muted d-block mt-1">
                                Jika diaktifkan, materi ini akan dibagikan sebagai tugas yang harus dikerjakan siswa.
                            </small>
                        </div>
                        
                        <div id="taskOptions{{ $materi->id }}" class="mt-3" style="display: {{ $materi->is_task ? 'block' : 'none' }};">
                            <div class="mb-2">
                                <label class="form-label small">Deadline (opsional)</label>
                                <input type="datetime-local" class="form-control form-control-sm" 
                                       name="deadline"
                                       value="{{ $materi->deadline ? \Carbon\Carbon::parse($materi->deadline)->format('Y-m-d\TH:i') : '' }}">
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save'></i> Simpan
                    </button>
                </div>
            </form>
            
            <script>
                function toggleAllClassrooms{{ $materi->id }}(source) {
                    const checkboxes = document.querySelectorAll('.classroom-checkbox-{{ $materi->id }}');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = source.checked;
                    });
                }
                
                document.getElementById('asTask{{ $materi->id }}')?.addEventListener('change', function() {
                    const taskOptions = document.getElementById('taskOptions{{ $materi->id }}');
                    if (this.checked) {
                        taskOptions.style.display = 'block';
                    } else {
                        taskOptions.style.display = 'none';
                    }
                });
            </script>
        </div>
    </div>
</div>
@endforeach
@endsection

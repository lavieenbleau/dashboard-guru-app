@extends('layouts.sneat')

@section('content')
<style>
    /* Layout Elements */
    .lesson-title-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #E5E7EB;
    }
    .theme-wrapper { margin-bottom: 2rem; }
    .subtheme-header {
        display: flex;
        align-items: center;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #374151;
    }
    .subtheme-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #06B6D4;
        margin-right: 12px;
        display: inline-block;
    }
    
    /* Lists */
    .item-row {
        display: flex;
        align-items: flex-start;
        padding: 20px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        margin-bottom: 20px;
        transition: all 0.2s ease;
    }
    .item-row:hover {
        border-color: #C7D2FE;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transform: translateY(-1px);
    }
    .item-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background-color: #EEF2FF;
        color: #4F46E5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    @media (max-width: 767.98px) {
        .item-row {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .item-actions {
            width: 100%;
            margin-top: 1rem;
        }
        .item-actions .btn-edu {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<div class="container-xxl py-4 edu-wrapper">
    <div class="edu-page-bg">
        <!-- Breadcrumb & Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 1px solid #E5E7EB;">
            <div>
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}" class="text-sub text-decoration-none">Materi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('guru.materi.admin', $serial->id) }}" class="text-sub text-decoration-none">Materi Admin</a></li>
                        <li class="breadcrumb-item active text-main fw-medium">{{ $lesson->name }}</li>
                    </ol>
                </nav>
                <h4 class="mb-1 text-main fw-bold">{{ $lesson->name }}</h4>
                <p class="text-sub mb-0" style="font-size: 0.9rem;">Manajemen materi dari pusat administrator</p>
            </div>
            <a href="{{ route('guru.materi.admin', $serial->id) }}" class="btn-edu btn-edu-outline text-decoration-none">
                <i class='bx bx-arrow-back me-2'></i> Kembali
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible bg-white border border-success fade show d-flex align-items-center shadow-sm" role="alert" style="border-radius: 10px;">
            <i class='bx bx-check-circle fs-4 text-success me-3'></i>
            <div class="text-main fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Lesson Items List with Hierarchy -->
        <div class="row m-0">
            <div class="col-12 p-0">
                @php
                    $itemsByLesson = $items->groupBy('lesson_id');
                @endphp
                
                @forelse ($itemsByLesson as $lessonId => $lessonItems)
                    @php
                        $lesson = $lessonItems->first()->lesson;
                        $themesGroup = $lessonItems->groupBy('theme_id');
                        $babCounter = 1;
                    @endphp
                    
                    <div class="lesson-section mb-5">
                        <div class="lesson-title-box">
                            <h5 class="mb-0 text-main fw-bold d-flex align-items-center">
                                <span style="display:inline-block; width: 4px; height: 24px; background-color: #4F46E5; border-radius: 4px; margin-right: 12px;"></span>
                                Daftar Isi: {{ $lesson->name }}
                            </h5>
                        </div>
                    
                        @forelse ($themesGroup as $themeId => $themeItems)
                            @php
                                $theme = $themeItems->first()->theme;
                                $subthemesGroup = $themeItems->groupBy('subtheme_id');
                                $subbabCounter = 1;
                            @endphp
                            
                            <div class="edu-card theme-wrapper">
                                <div class="edu-card-header">
                                    <h6 class="m-0 fw-bold text-main" style="font-size: 1.05rem;">
                                        Bab {{ $babCounter++ }}: {{ $theme ? $theme->name : 'Tanpa Bab' }}
                                    </h6>
                                </div>
                                
                                <div class="edu-card-body">
                                    @foreach ($subthemesGroup as $subthemeId => $subthemeItems)
                                        @php
                                            $subtheme = $subthemeItems->first()->subtheme;
                                        @endphp
                                        
                                        <div class="subtheme-header">
                                            <span class="subtheme-dot"></span>
                                            Subbab {{ $subbabCounter++ }}: {{ $subtheme ? $subtheme->name : 'Tanpa Subbab' }}
                                        </div>
                                        
                                        <!-- Items in this subtheme -->
                                        @php $materiCounter = 1; @endphp
                                        @foreach ($subthemeItems as $item)
                                        <div class="item-row">
                                            <div class="d-flex flex-column flex-md-row w-100">
                                                @php
                                                    $youtubeId = null;
                                                    $embedUrl = null;
                                                    if($item->embed) {
                                                        $embedUrl = $item->embed;
                                                        if (preg_match('/src="([^"]+)"/i', $item->embed, $matches)) {
                                                            $embedUrl = $matches[1];
                                                        } elseif (preg_match("/src='([^']+)'/i", $item->embed, $matches)) {
                                                            $embedUrl = $matches[1];
                                                        }
                                                        
                                                        // Ambil ID YouTube
                                                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $embedUrl, $match)) {
                                                            $youtubeId = $match[1];
                                                        }
                                                    }
                                                @endphp

                                                @if($youtubeId)
                                                    <div class="flex-shrink-0 mb-3 mb-md-0 me-md-3" style="width: 180px;">
                                                        <div class="position-relative" style="width: 100%; height: 100px; border-radius: 8px; overflow: hidden; background-color: #000;">
                                                            <img src="https://img.youtube.com/vi/{{ $youtubeId }}/maxresdefault.jpg" onerror="this.src='https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg'" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                                                            <a href="{{ $embedUrl }}" target="_blank" class="position-absolute top-50 start-50 translate-middle text-white text-decoration-none">
                                                                <i class='bx bx-play-circle' style="font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5);"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="item-icon-box mb-3 mb-md-0 d-flex flex-shrink-0 me-md-3">
                                                        <i class='bx bx-file'></i>
                                                    </div>
                                                @endif

                                                <div class="flex-grow-1 d-flex flex-column justify-content-between">
                                                    <div class="pe-0">
                                                        <h6 class="mb-1 text-main fw-bold" style="font-size: 1rem;">
                                                            Materi {{ $item->number ?? $materiCounter++ }}: {{ $item->title }}
                                                        </h6>
                                                        <div class="d-flex align-items-center gap-2 text-sub" style="font-size: 0.8rem;">
                                                            <span class="edu-badge badge-gray px-2 py-1">Admin</span>
                                                            <span>•</span>
                                                            <span class="d-flex align-items-center"><i class='bx bx-time-five me-1'></i> {{ $item->created_at ? $item->created_at->format('d M Y') : '' }}</span>
                                                        </div>
                                                        @if(isset($item->description) && $item->description)
                                                        <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                            {{ strip_tags($item->description) }}
                                                        </p>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-end mt-3 mt-md-0 align-items-center" style="gap: 12px;">
                                                        @if($item->embed)
                                                        <a href="{{ $embedUrl }}" target="_blank" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 6px;">
                                                            <i class='bx bx-play-circle me-1'></i> Video
                                                        </a>
                                                        @endif
                                                        <x-action-dropdown>
                                                            <li>
                                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareModal{{ $item->id }}">
                                                                    <i class='bx bx-share-alt me-2'></i>Share
                                                                </button>
                                                            </li>
                                                        </x-action-dropdown>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-sub text-center py-3" style="font-size: 0.85rem;">Tidak ada bab di pelajaran ini.</p>
                        @endforelse
                    </div>
                @empty
                <div class="edu-card text-center py-5">
                    <div class="mx-auto mb-3" style="width: 48px; height: 48px; background-color: #F3F4F6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class='bx bx-book-open text-sub fs-3'></i>
                    </div>
                    <h6 class="text-main fw-semibold mb-1">Daftar Materi Kosong</h6>
                    <p class="mb-0 text-sub" style="font-size: 0.9rem;">
                        Belum ada materi untuk {{ $lesson->name }}
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Share Modals -->
@foreach ($items as $item)
<div class="modal fade" id="shareModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.materi.admin.share', [$serial->id, $item->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3"><strong>{{ $item->number }}. {{ $item->title }}</strong></p>
                    <p class="text-muted small mb-3">Pilih kelas yang dapat mengakses materi ini:</p>
                    
                    @php
                        $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                        $sharedClassroomIds = [];
                    @endphp
                    
                    @if($classrooms->count() > 0)
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="selectAll{{ $item->id }}"
                                   onclick="toggleAllClassrooms{{ $item->id }}(this)">
                            <label class="form-check-label fw-bold" for="selectAll{{ $item->id }}">
                                Pilih Semua Kelas
                            </label>
                        </div>
                        <hr class="mb-3">
                    @endif
                    
                    @forelse($classrooms as $classroom)
                    <div class="form-check mb-2">
                        <input class="form-check-input classroom-checkbox-{{ $item->id }}" type="checkbox" 
                               name="classrooms[]" 
                               value="{{ $classroom->id }}" 
                               id="classroom{{ $item->id }}_{{ $classroom->id }}"
                               {{ in_array($classroom->id, $sharedClassroomIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="classroom{{ $item->id }}_{{ $classroom->id }}">
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
                                   id="asTask{{ $item->id }}">
                            <label class="form-check-label" for="asTask{{ $item->id }}">
                                <i class='bx bx-task me-1'></i> Bagikan sebagai Tugas
                            </label>
                            <small class="form-text text-muted d-block mt-1">
                                Jika diaktifkan, materi ini akan dibagikan sebagai tugas yang harus dikerjakan siswa.
                            </small>
                        </div>
                        
                        <div id="taskOptions{{ $item->id }}" class="mt-3" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label small">Deadline (opsional)</label>
                                <input type="datetime-local" class="form-control form-control-sm" name="deadline">
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
                function toggleAllClassrooms{{ $item->id }}(source) {
                    const checkboxes = document.querySelectorAll('.classroom-checkbox-{{ $item->id }}');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = source.checked;
                    });
                }
                
                document.getElementById('asTask{{ $item->id }}')?.addEventListener('change', function() {
                    const taskOptions = document.getElementById('taskOptions{{ $item->id }}');
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

@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.custom', $serial->id) }}">Materi Tambahan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item active">Detail Materi</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                    <h3 class="mb-2">{{ $materi->title }}</h3>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-label-primary">
                            <i class='bx bx-book'></i> {{ $lesson->name }}
                        </span>
                        
                        @if($materi->is_task)
                        <span class="badge bg-label-warning">
                            <i class='bx bx-task'></i> Tugas
                        </span>
                        @else
                        <span class="badge bg-label-info">
                            <i class='bx bx-info-circle'></i> Materi
                        </span>
                        @endif
                        
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
                            <i class='bx bx-video'></i> Video
                        </span>
                        @endif
                    </div>
                    
                    <div class="text-muted small">
                        <i class='bx bx-user me-1'></i> Dibuat oleh: <strong>{{ $materi->user->name ?? 'Unknown' }}</strong> • 
                        <i class='bx bx-calendar me-1'></i> {{ $materi->created_at->format('d M Y, H:i') }} 
                        @if($materi->created_at != $materi->updated_at)
                        <span class="ms-2">• <i class='bx bx-edit me-1'></i> Diupdate: {{ $materi->updated_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class='bx bx-dots-vertical-rounded'></i> Aksi
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <i class='bx bx-share-alt me-2'></i>Bagikan ke Kelas
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('guru.materi.edit', [$serial->id, $lesson->id, $materi->id]) }}">
                                <i class='bx bx-edit me-2'></i>Edit
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('guru.materi.destroy', [$serial->id, $lesson->id, $materi->id]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
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
            
            <div class="d-flex gap-2">
                <a href="{{ route('guru.materi.mapel', [$serial->id, $lesson->id]) }}" class="btn btn-outline-secondary btn-sm">
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

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Description -->
            @if($materi->description)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-text me-2'></i>Deskripsi</h5>
                </div>
                <div class="card-body">
                    <div class="materi-content">
                        {!! nl2br(e($materi->description)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Link -->
            @if($materi->link)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-link-alt me-2'></i>Link Materi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ $materi->link }}" target="_blank" class="btn btn-primary">
                            <i class='bx bx-link-external me-1'></i> Buka Link
                        </a>
                        <div class="flex-grow-1">
                            <small class="text-muted text-break">{{ $materi->link }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Attachment -->
            @if($materi->attachment)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-file me-2'></i>File Lampiran</h5>
                </div>
                <div class="card-body">
                    @php
                        $filePath = Storage::url($materi->attachment);
                        $fileName = basename($materi->attachment);
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $fileSize = Storage::disk('public')->exists($materi->attachment) 
                            ? Storage::disk('public')->size($materi->attachment) 
                            : 0;
                        $fileSizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024 / 1024, 2) . ' MB' : 'Unknown';
                    @endphp

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="file-icon" style="font-size: 48px;">
                            @if(in_array($fileExtension, ['pdf']))
                                <i class='bx bxs-file-pdf text-danger'></i>
                            @elseif(in_array($fileExtension, ['doc', 'docx']))
                                <i class='bx bxs-file-doc text-primary'></i>
                            @elseif(in_array($fileExtension, ['xls', 'xlsx']))
                                <i class='bx bxs-file text-success'></i>
                            @elseif(in_array($fileExtension, ['ppt', 'pptx']))
                                <i class='bx bxs-file text-warning'></i>
                            @elseif(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                <i class='bx bxs-file-image text-info'></i>
                            @elseif(in_array($fileExtension, ['zip', 'rar', '7z']))
                                <i class='bx bxs-file-archive text-secondary'></i>
                            @else
                                <i class='bx bxs-file text-muted'></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-break">{{ $fileName }}</h6>
                            <small class="text-muted">
                                <i class='bx bx-purchase-tag'></i> {{ strtoupper($fileExtension) }} • 
                                <i class='bx bx-data'></i> {{ $fileSizeFormatted }}
                            </small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ $filePath }}" target="_blank" class="btn btn-success">
                            <i class='bx bx-download me-1'></i> Download File
                        </a>
                        @if(in_array($fileExtension, ['pdf', 'jpg', 'jpeg', 'png', 'gif']))
                        <a href="{{ $filePath }}" target="_blank" class="btn btn-outline-primary">
                            <i class='bx bx-show me-1'></i> Preview
                        </a>
                        @endif
                    </div>

                    <!-- Preview untuk PDF dan Image -->
                    @if(in_array($fileExtension, ['pdf']))
                    <div class="mt-3">
                        <iframe src="{{ $filePath }}" style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 4px;"></iframe>
                    </div>
                    @elseif(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                    <div class="mt-3">
                        <img src="{{ $filePath }}" alt="{{ $fileName }}" class="img-fluid rounded" style="max-height: 500px;">
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Embed Video -->
            @if($materi->embed)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-video me-2'></i>Video Pembelajaran</h5>
                </div>
                <div class="card-body">
                    <div class="ratio ratio-16x9">
                        {!! $materi->embed !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Empty State -->
            @if(!$materi->description && !$materi->link && !$materi->attachment && !$materi->embed)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-info-circle' style="font-size: 64px; opacity: 0.3;"></i>
                    <h5 class="mt-3">Belum Ada Konten</h5>
                    <p class="text-muted">Materi ini belum memiliki konten. Silakan edit untuk menambahkan.</p>
                    <a href="{{ route('guru.materi.edit', [$serial->id, $lesson->id, $materi->id]) }}" class="btn btn-primary">
                        <i class='bx bx-edit me-1'></i>Edit Materi
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Info Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-info-circle me-2'></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted" style="width: 120px;"><i class='bx bx-book me-1'></i> Mata Pelajaran</td>
                            <td><strong>{{ $lesson->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class='bx bx-user me-1'></i> Pembuat</td>
                            <td>{{ $materi->user->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class='bx bx-calendar me-1'></i> Dibuat</td>
                            <td>{{ $materi->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class='bx bx-edit me-1'></i> Diupdate</td>
                            <td>{{ $materi->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @if($materi->is_task && $materi->deadline)
                        <tr>
                            <td class="text-muted"><i class='bx bx-time me-1'></i> Deadline</td>
                            <td>
                                <span class="badge bg-label-warning">
                                    {{ \Carbon\Carbon::parse($materi->deadline)->format('d M Y, H:i') }}
                                </span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted"><i class='bx bx-message-dots me-1'></i> Diskusi</td>
                            <td><strong>{{ $materi->comments->count() }}</strong> komentar</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Shared Classes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-share-alt me-2'></i>Dibagikan ke Kelas</h5>
                </div>
                <div class="card-body">
                    @if($sharedClassrooms && $sharedClassrooms->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($sharedClassrooms as $classroom)
                            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                <i class='bx bx-group text-primary'></i>
                                <div>
                                    <div class="fw-semibold">{{ $classroom->name }}</div>
                                    <small class="text-muted">{{ $classroom->code ?? '-' }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                Total: <strong>{{ $sharedClassrooms->count() }}</strong> kelas
                            </small>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class='bx bx-group' style="font-size: 32px; opacity: 0.3;"></i>
                            <p class="text-muted small mb-2">Belum dibagikan ke kelas manapun</p>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <i class='bx bx-share-alt me-1'></i>Bagikan Sekarang
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-bar-chart me-2'></i>Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <div class="text-center flex-fill">
                            <div class="h4 mb-0 text-primary">{{ $sharedClassrooms ? $sharedClassrooms->count() : 0 }}</div>
                            <small class="text-muted">Kelas</small>
                        </div>
                        <div class="text-center flex-fill border-start">
                            <div class="h4 mb-0 text-success">
                                @if($sharedClassrooms && $sharedClassrooms->count() > 0)
                                    {{ $sharedClassrooms->sum(function($c) { return $c->students()->count(); }) }}
                                @else
                                    0
                                @endif
                            </div>
                            <small class="text-muted">Siswa</small>
                        </div>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class='bx bx-show me-1'></i> Materi dapat diakses oleh siswa di kelas yang dibagikan
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discussion Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class='bx bx-message-dots me-2'></i>Diskusi & Komentar</h5>
                    <span class="badge bg-label-primary">{{ $materi->comments->count() }} Komentar</span>
                </div>
                <div class="card-body">
                    <!-- Comment Form -->
                    <div class="mb-4 pb-4 border-bottom">
                        <form action="{{ route('guru.materi.comment.store', [$serial->id, $lesson->id, $materi->id]) }}" method="POST">
                            @csrf
                            <div class="d-flex gap-3">
                                <div class="avatar avatar-sm flex-shrink-0">
                                    <div class="avatar-initial rounded-circle bg-label-primary">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <textarea name="message" 
                                              class="form-control @error('message') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="Tulis komentar Anda..."
                                              required></textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class='bx bx-send me-1'></i>Kirim Komentar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    @forelse($materi->comments as $comment)
                    <div class="mb-4" id="comment-{{ $comment->id }}">
                        <div class="d-flex gap-3">
                            <!-- Avatar -->
                            <div class="avatar avatar-sm flex-shrink-0">
                                <div class="avatar-initial rounded-circle {{ $comment->is_user ? 'bg-label-primary' : 'bg-label-success' }}">
                                    {{ strtoupper(substr($comment->commenter_name, 0, 1)) }}
                                </div>
                            </div>
                            
                            <!-- Comment Content -->
                            <div class="flex-grow-1">
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong class="d-block">{{ $comment->commenter_name }}</strong>
                                            <small class="text-muted">
                                                <span class="badge badge-sm bg-label-{{ $comment->is_user ? 'primary' : 'success' }}">
                                                    {{ $comment->commenter_type }}
                                                </span>
                                                • {{ $comment->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        
                                        <x-action-dropdown>
                                                <li>
                                                    <form action="{{ route('guru.materi.comment.delete', [$serial->id, $lesson->id, $materi->id, $comment->id]) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Hapus komentar ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class='bx bx-trash me-2'></i>Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </x-action-dropdown>
                                    </div>
                                    <p class="mb-0">{{ $comment->message }}</p>
                                </div>

                                <!-- Reply Button -->
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-text-primary" 
                                            type="button"
                                            onclick="toggleReplyForm({{ $comment->id }})">
                                        <i class='bx bx-reply me-1'></i>Balas
                                    </button>
                                    @if($comment->replies->count() > 0)
                                    <span class="text-muted small ms-2">
                                        {{ $comment->replies->count() }} balasan
                                    </span>
                                    @endif
                                </div>

                                <!-- Reply Form (Hidden by default) -->
                                <div id="reply-form-{{ $comment->id }}" class="mt-3" style="display: none;">
                                    <form action="{{ route('guru.materi.comment.reply', [$serial->id, $lesson->id, $materi->id, $comment->id]) }}" method="POST">
                                        @csrf
                                        <div class="d-flex gap-2">
                                            <textarea name="message" 
                                                      class="form-control form-control-sm" 
                                                      rows="2" 
                                                      placeholder="Tulis balasan..."
                                                      required></textarea>
                                            <div class="d-flex flex-column gap-1">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class='bx bx-send'></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-outline-secondary btn-sm" 
                                                        onclick="toggleReplyForm({{ $comment->id }})">
                                                    <i class='bx bx-x'></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Replies -->
                                @if($comment->replies->count() > 0)
                                <div class="mt-3 ms-4">
                                    @foreach($comment->replies as $reply)
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="avatar avatar-xs flex-shrink-0">
                                            <div class="avatar-initial rounded-circle {{ $reply->is_user ? 'bg-label-primary' : 'bg-label-success' }}">
                                                {{ strtoupper(substr($reply->commenter_name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="bg-light p-2 rounded">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div>
                                                        <strong class="small">{{ $reply->commenter_name }}</strong>
                                                        <small class="text-muted d-block">
                                                            <span class="badge badge-sm bg-label-{{ $reply->is_user ? 'primary' : 'success' }}">
                                                                {{ $reply->commenter_type }}
                                                            </span>
                                                            • {{ $reply->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    
                                                    <x-action-dropdown>
                                                            <li>
                                                                <form action="{{ route('guru.materi.reply.delete', [$serial->id, $lesson->id, $materi->id, $reply->id]) }}" 
                                                                      method="POST" 
                                                                      onsubmit="return confirm('Hapus balasan ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class='bx bx-trash me-2'></i>Hapus
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </x-action-dropdown>
                                                </div>
                                                <p class="mb-0 small">{{ $reply->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class='bx bx-message-rounded-dots' style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="text-muted mt-3 mb-0">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Materi ke Kelas</h5>
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
                                   id="selectAllClassrooms"
                                   onclick="toggleAllClassrooms(this)">
                            <label class="form-check-label fw-bold" for="selectAllClassrooms">
                                Pilih Semua Kelas
                            </label>
                        </div>
                        <hr class="mb-3">
                    @endif
                    
                    @forelse($classrooms as $classroom)
                    <div class="form-check mb-2">
                        <input class="form-check-input classroom-checkbox" type="checkbox" 
                               name="classrooms[]" 
                               value="{{ $classroom->id }}" 
                               id="classroom_{{ $classroom->id }}"
                               {{ in_array($classroom->id, $sharedClassroomIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="classroom_{{ $classroom->id }}">
                            <i class='bx bx-group me-1'></i>{{ $classroom->name }} 
                            <span class="text-muted small">({{ $classroom->code ?? '-' }})</span>
                        </label>
                    </div>
                    @empty
                    <div class="alert alert-info mb-0">
                        <i class='bx bx-info-circle'></i> Belum ada kelas. Silakan buat kelas terlebih dahulu.
                    </div>
                    @endforelse
                    
                    @if($classrooms->count() > 0)
                        <hr class="mt-3 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   name="as_task" 
                                   value="1"
                                   id="asTaskCheckbox"
                                   {{ $materi->is_task ? 'checked' : '' }}>
                            <label class="form-check-label" for="asTaskCheckbox">
                                <i class='bx bx-task me-1'></i> Bagikan sebagai Tugas
                            </label>
                            <small class="form-text text-muted d-block mt-1">
                                Jika diaktifkan, materi ini akan dibagikan sebagai tugas yang harus dikerjakan siswa.
                            </small>
                        </div>
                        
                        <div id="taskOptionsDiv" class="mt-3" style="display: {{ $materi->is_task ? 'block' : 'none' }};">
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
        </div>
    </div>
</div>

<script>
    function toggleAllClassrooms(source) {
        const checkboxes = document.querySelectorAll('.classroom-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const asTaskCheckbox = document.getElementById('asTaskCheckbox');
        if (asTaskCheckbox) {
            asTaskCheckbox.addEventListener('change', function() {
                const taskOptions = document.getElementById('taskOptionsDiv');
                if (taskOptions) {
                    if (this.checked) {
                        taskOptions.style.display = 'block';
                    } else {
                        taskOptions.style.display = 'none';
                    }
                }
            });
        }
    });

    function toggleReplyForm(commentId) {
        const replyForm = document.getElementById('reply-form-' + commentId);
        if (replyForm) {
            if (replyForm.style.display === 'none' || replyForm.style.display === '') {
                replyForm.style.display = 'block';
                // Focus on textarea
                const textarea = replyForm.querySelector('textarea');
                if (textarea) textarea.focus();
            } else {
                replyForm.style.display = 'none';
            }
        }
    }
</script>
@endsection

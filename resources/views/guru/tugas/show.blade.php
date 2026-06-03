@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.mapel', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item active">{{ $task->title }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-edit text-primary me-2'></i>{{ $task->title }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.tugas.edit', [$serial->id, $lesson->id, $task->id]) }}" class="btn btn-primary">
                <i class='bx bx-edit me-1'></i>Edit Tugas
            </a>
            <a href="{{ route('guru.tugas.mapel', [$serial->id, $lesson->id]) }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i>Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Tugas -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informasi Tugas</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Mata Pelajaran</strong>
                            {{ $lesson->name }}
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Serial</strong>
                            {{ $serial->product->name }}
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Kelas Tujuan</strong>
                            @if(count($task->classrooms) > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($task->classrooms as $cls)
                                        <span class="badge bg-label-primary">{{ $cls->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge bg-label-danger">Belum Ditentukan</span>
                            @endif
                        </li>
                        @if($task->deadline)
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Deadline Pengumpulan</strong>
                            <div class="d-flex align-items-center">
                                <i class='bx bx-time-five me-2 text-warning'></i>
                                <span>{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y, H:i') }}</span>
                            </div>
                            @php
                                $now = \Carbon\Carbon::now();
                                $deadline = \Carbon\Carbon::parse($task->deadline);
                                $diff = $now->diffInHours($deadline, false);
                            @endphp
                            @if($diff < 0)
                                <small class="text-danger">Sudah lewat {{ abs($diff) }} jam</small>
                            @elseif($diff < 24)
                                <small class="text-warning">Tersisa {{ $diff }} jam lagi</small>
                            @else
                                <small class="text-muted">Tersisa {{ floor($diff/24) }} hari lagi</small>
                            @endif
                        </li>
                        @endif
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Dibuat</strong>
                            {{ $task->created_at->format('d M Y H:i') }}
                        </li>
                        <li class="mb-0">
                            <strong class="d-block text-muted small">Terakhir Diupdate</strong>
                            {{ $task->updated_at->format('d M Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Statistik -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Statistik</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total Siswa Mengumpulkan</span>
                        <strong>0</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Rata-rata Nilai</span>
                        <strong>-</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Nilai Tertinggi</span>
                        <strong>-</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Belum Mengumpulkan</span>
                        <strong class="text-danger">0</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class='bx bx-message-dots me-1'></i>Diskusi</span>
                        <strong>{{ $task->comments->count() }} komentar</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Konten Tugas -->
        <div class="col-md-8">
            <!-- Deskripsi & File -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Deskripsi Tugas</h5>
                </div>
                <div class="card-body">
                    @if($task->description)
                        <div class="mb-3">{!! nl2br(e($task->description)) !!}</div>
                    @else
                        <p class="text-muted">Tidak ada deskripsi untuk tugas ini.</p>
                    @endif
                    
                    @if($task->attachment)
                        <div class="alert alert-light d-flex align-items-center mb-3">
                            <i class='bx bx-file text-primary me-3' style="font-size: 2rem;"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Lampiran File</h6>
                                <small class="text-muted">{{ basename($task->attachment) }}</small>
                            </div>
                            <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="btn btn-sm btn-primary" download>
                                <i class='bx bx-download me-1'></i>Download
                            </a>
                        </div>
                    @endif
                    
                    @if($task->link)
                        <div class="mt-3">
                            <a href="{{ $task->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external me-1'></i>Buka Link Materi
                            </a>
                        </div>
                    @endif
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
                    <span class="badge bg-label-primary">{{ $task->comments->count() }} Komentar</span>
                </div>
                <div class="card-body">
                    <!-- Comment Form -->
                    <div class="mb-4 pb-4 border-bottom">
                        <form action="{{ route('guru.tugas.comment.store', [$serial->id, $lesson->id, $task->id]) }}" method="POST">
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
                    @forelse($task->comments as $comment)
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
                                                    <form action="{{ route('guru.tugas.comment.delete', [$serial->id, $lesson->id, $task->id, $comment->id]) }}" 
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
                                    <form action="{{ route('guru.tugas.comment.reply', [$serial->id, $lesson->id, $task->id, $comment->id]) }}" method="POST">
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
                                                                <form action="{{ route('guru.tugas.reply.delete', [$serial->id, $lesson->id, $task->id, $reply->id]) }}" 
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

<script>
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

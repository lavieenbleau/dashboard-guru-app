@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.meeting', $serial->id) }}">Kelas Online</a></li>
            <li class="breadcrumb-item active">{{ $meeting->title }}</li>
        </ol>
    </nav>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class='bx bx-error me-2'></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-2">{{ $meeting->title }}</h4>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($meeting->status === 'ongoing')
                                    <span class="badge bg-danger">Sedang Berlangsung</span>
                                @elseif($meeting->status === 'scheduled')
                                    <span class="badge bg-primary">Dijadwalkan</span>
                                @elseif($meeting->status === 'ended')
                                    <span class="badge bg-secondary">Selesai</span>
                                @else
                                    <span class="badge bg-secondary">Dibatalkan</span>
                                @endif

                                @if($meeting->platform === 'jitsi')
                                    <span class="badge bg-success">
                                        <i class='bx bx-video'></i> Jitsi Meet
                                    </span>
                                @elseif($meeting->platform === 'zoom')
                                    <span class="badge bg-info">
                                        <i class='bx bx-video'></i> Zoom
                                    </span>
                                @elseif($meeting->platform === 'gmeet')
                                    <span class="badge bg-warning">
                                        <i class='bx bx-video'></i> Google Meet
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        @if($meeting->status === 'scheduled' || $meeting->status === 'ongoing')
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-secondary" type="button" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <ul class="dropdown-menu">
                                @if($meeting->status === 'scheduled')
                                <li>
                                    <a class="dropdown-item" href="{{ route('guru.meeting.edit', [$serial->id, $meeting->id]) }}">
                                        <i class='bx bx-edit me-2'></i>Edit
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <form action="{{ route('guru.meeting.destroy', [$serial->id, $meeting->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Hapus meeting ini?')">
                                            <i class='bx bx-trash me-2'></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>

                    @if($meeting->description)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Deskripsi</h6>
                        <p>{{ $meeting->description }}</p>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    @if($meeting->isActive())
                    <div class="alert alert-danger d-flex align-items-center mb-4">
                        <i class='bx bx-broadcast bx-lg me-3'></i>
                        <div class="flex-grow-1">
                            <strong>Meeting Sedang Berlangsung</strong>
                            <p class="mb-0">Klik tombol di bawah untuk bergabung</p>
                        </div>
                    </div>
                    <div class="d-grid gap-2 mb-4">
                        <a href="{{ route('guru.meeting.join', [$serial->id, $meeting->id]) }}" class="btn btn-danger btn-lg">
                            <i class='bx bx-video me-2'></i>Gabung Meeting
                        </a>
                        @if($meeting->platform !== 'jitsi' && $meeting->meeting_link)
                        <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-primary">
                            <i class='bx bx-link-external me-2'></i>Buka di {{ ucfirst($meeting->platform) }}
                        </a>
                        @endif
                    </div>
                    @elseif($meeting->status === 'scheduled')
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class='bx bx-time-five bx-lg me-3'></i>
                        <div>
                            <strong>Meeting Akan Dimulai</strong>
                            <p class="mb-0">{{ $meeting->start_time->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Meeting Link for External Platforms -->
                    @if($meeting->platform !== 'jitsi' && $meeting->meeting_link && $meeting->status !== 'ended')
                    <div class="card bg-label-info mb-4">
                        <div class="card-body">
                            <h6 class="mb-2"><i class='bx bx-link me-2'></i>Link Meeting</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $meeting->meeting_link }}" id="meetingLink" readonly>
                                <button class="btn btn-primary" onclick="copyLink()">
                                    <i class='bx bx-copy'></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Meeting Code for Jitsi -->
                    @if($meeting->platform === 'jitsi')
                    <div class="card bg-label-success mb-4">
                        <div class="card-body">
                            <h6 class="mb-2"><i class='bx bx-key me-2'></i>Kode Meeting</h6>
                            <div class="d-flex align-items-center gap-3">
                                <code class="fs-5">{{ $meeting->meeting_code }}</code>
                                <button class="btn btn-sm btn-success" onclick="copyCode('{{ $meeting->meeting_code }}')">
                                    <i class='bx bx-copy'></i> Copy
                                </button>
                            </div>
                            <small class="text-muted">Siswa dapat menggunakan kode ini untuk bergabung</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Meeting Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Informasi Meeting</h6>
                    
                    @if($meeting->classroom)
                    <div class="mb-3">
                        <small class="text-muted">Kelas</small>
                        <div><i class='bx bx-group me-2'></i>{{ $meeting->classroom->name }}</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted">Waktu Mulai</small>
                        <div><i class='bx bx-time me-2'></i>{{ $meeting->start_time->format('d M Y, H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Waktu Selesai</small>
                        <div><i class='bx bx-time me-2'></i>{{ $meeting->end_time->format('d M Y, H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Durasi</small>
                        <div><i class='bx bx-hourglass me-2'></i>{{ $meeting->start_time->diffInMinutes($meeting->end_time) }} menit</div>
                    </div>

                    <div>
                        <small class="text-muted">Dibuat oleh</small>
                        <div><i class='bx bx-user me-2'></i>{{ $meeting->user->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($meeting->status === 'ongoing')
            <div class="card bg-label-danger">
                <div class="card-body">
                    <h6 class="mb-3"><i class='bx bx-stop-circle me-2'></i>Kontrol Meeting</h6>
                    <form action="{{ route('guru.meeting.end', [$serial->id, $meeting->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Akhiri meeting ini?')">
                            <i class='bx bx-stop-circle me-1'></i>Akhiri Meeting
                        </button>
                    </form>
                    <small class="text-muted mt-2 d-block">Meeting akan diakhiri untuk semua peserta</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyLink() {
    const linkInput = document.getElementById('meetingLink');
    linkInput.select();
    document.execCommand('copy');
    
    alert('Link berhasil disalin!');
}

function copyCode(code) {
    const textarea = document.createElement('textarea');
    textarea.value = code;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    
    alert('Kode meeting berhasil disalin!');
}
</script>
@endpush
@endsection

@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Kelas Online</h4>
            <p class="text-muted mb-0">Kelola kelas online dengan Jitsi Meet</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickStartModal">
                <i class='bx bx-play-circle me-1'></i>Quick Start Meeting
            </button>
            <a href="{{ route('guru.meeting.create', $serial->id) }}" class="btn btn-primary">
                <i class='bx bx-plus me-1'></i>Jadwalkan Meeting
            </a>
        </div>
    </div>

    <!-- Success Message -->
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

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ongoing" role="tab">
                <i class='bx bx-video me-1'></i>Sedang Berlangsung 
                @if($ongoingMeetings->count() > 0)
                <span class="badge bg-danger ms-1">{{ $ongoingMeetings->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#upcoming" role="tab">
                <i class='bx bx-calendar me-1'></i>Akan Datang
                @if($upcomingMeetings->count() > 0)
                <span class="badge bg-primary ms-1">{{ $upcomingMeetings->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ended" role="tab">
                <i class='bx bx-history me-1'></i>Riwayat
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Ongoing Meetings -->
        <div class="tab-pane fade show active" id="ongoing" role="tabpanel">
            <div class="row g-3">
                @forelse($ongoingMeetings as $meeting)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="mb-0">{{ $meeting->title }}</h5>
                                <span class="badge bg-danger">Live</span>
                            </div>
                            
                            @if($meeting->description)
                            <p class="text-muted mb-3">{{ Str::limit($meeting->description, 100) }}</p>
                            @endif

                            <div class="mb-3">
                                @if($meeting->classroom)
                                <span class="badge bg-label-info">
                                    <i class='bx bx-group'></i> {{ $meeting->classroom->name }}
                                </span>
                                @endif
                            </div>

                            <div class="text-muted small mb-3">
                                <div><i class='bx bx-time'></i> {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}</div>
                                <div><i class='bx bx-calendar'></i> {{ $meeting->start_time->format('d M Y') }}</div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('guru.meeting.join', [$serial->id, $meeting->id]) }}" class="btn btn-danger">
                                    <i class='bx bx-video me-1'></i>Gabung Meeting
                                </a>
                                <a href="{{ route('guru.meeting.show', [$serial->id, $meeting->id]) }}" class="btn btn-sm btn-label-secondary">
                                    <i class='bx bx-info-circle me-1'></i>Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class='bx bx-video-off' style="font-size: 48px; opacity: 0.3;"></i>
                            <h5 class="mt-3">Tidak Ada Meeting Aktif</h5>
                            <p class="text-muted">Belum ada kelas online yang sedang berlangsung</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="tab-pane fade" id="upcoming" role="tabpanel">
            <div class="row g-3">
                @forelse($upcomingMeetings as $meeting)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="mb-0">{{ $meeting->title }}</h5>
                                <span class="badge bg-primary">Dijadwalkan</span>
                            </div>
                            
                            @if($meeting->description)
                            <p class="text-muted mb-3">{{ Str::limit($meeting->description, 100) }}</p>
                            @endif

                            <div class="mb-3">
                                @if($meeting->classroom)
                                <span class="badge bg-label-info">
                                    <i class='bx bx-group'></i> {{ $meeting->classroom->name }}
                                </span>
                                @endif

                                @if($meeting->platform === 'jitsi')
                                <span class="badge bg-label-success">
                                    <i class='bx bx-video'></i> Jitsi Meet
                                </span>
                                @endif
                            </div>

                            <div class="text-muted small mb-3">
                                <div><i class='bx bx-time'></i> {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}</div>
                                <div><i class='bx bx-calendar'></i> {{ $meeting->start_time->format('d M Y') }}</div>
                                <div class="text-primary mt-1">
                                    <i class='bx bx-hourglass'></i> {{ $meeting->start_time->diffForHumans() }}
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('guru.meeting.show', [$serial->id, $meeting->id]) }}" class="btn btn-sm btn-primary flex-fill">
                                    <i class='bx bx-info-circle'></i> Detail
                                </a>
                                <a href="{{ route('guru.meeting.edit', [$serial->id, $meeting->id]) }}" class="btn btn-sm btn-label-secondary">
                                    <i class='bx bx-edit'></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class='bx bx-calendar-x' style="font-size: 48px; opacity: 0.3;"></i>
                            <h5 class="mt-3">Tidak Ada Meeting Terjadwal</h5>
                            <p class="text-muted mb-3">Buat meeting baru untuk memulai kelas online</p>
                            <a href="{{ route('guru.meeting.create', $serial->id) }}" class="btn btn-primary">
                                <i class='bx bx-plus me-1'></i>Buat Meeting
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Ended Meetings -->
        <div class="tab-pane fade" id="ended" role="tabpanel">
            <div class="row g-3">
                @forelse($endedMeetings as $meeting)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="mb-0">{{ $meeting->title }}</h5>
                                <span class="badge bg-label-secondary">
                                    {{ $meeting->status === 'cancelled' ? 'Dibatalkan' : 'Selesai' }}
                                </span>
                            </div>

                            <div class="mb-3">

                                @if($meeting->classroom)
                                <span class="badge bg-label-info">
                                    <i class='bx bx-group'></i> {{ $meeting->classroom->name }}
                                </span>
                                @endif
                            </div>

                            <div class="text-muted small mb-3">
                                <div><i class='bx bx-calendar'></i> {{ $meeting->start_time->format('d M Y') }}</div>
                                <div><i class='bx bx-time'></i> {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}</div>
                            </div>

                            <a href="{{ route('guru.meeting.show', [$serial->id, $meeting->id]) }}" class="btn btn-sm btn-label-secondary">
                                <i class='bx bx-info-circle me-1'></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class='bx bx-history' style="font-size: 48px; opacity: 0.3;"></i>
                            <h5 class="mt-3">Belum Ada Riwayat</h5>
                            <p class="text-muted">Riwayat meeting akan muncul di sini</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Start Meeting Modal -->
<div class="modal fade" id="quickStartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class='bx bx-play-circle me-2'></i>Quick Start Meeting
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.meeting.quick-start', $serial->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class='bx bx-info-circle me-2'></i>
                        <small>Meeting akan dimulai sekarang dan langsung masuk ke room Jitsi Meet</small>
                    </div>

                    <!-- Judul Meeting -->
                    <div class="mb-3">
                        <label for="quick_title" class="form-label">Judul Meeting <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_title" name="title" 
                               placeholder="Contoh: Kelas Matematika" required autofocus>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div class="mb-3">
                        <label for="quick_mapel" class="form-label">Mata Pelajaran (Opsional)</label>
                        <select class="form-select" id="quick_mapel" name="mapel_id">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div class="mb-3">
                        <label for="quick_classroom" class="form-label">Kelas (Opsional)</label>
                        <select class="form-select" id="quick_classroom" name="classroom_id">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Durasi -->
                    <div class="mb-3">
                        <label for="quick_duration" class="form-label">Durasi Meeting</label>
                        <select class="form-select" id="quick_duration" name="duration">
                            <option value="30">30 Menit</option>
                            <option value="60" selected>1 Jam</option>
                            <option value="90">1.5 Jam</option>
                            <option value="120">2 Jam</option>
                            <option value="180">3 Jam</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class='bx bx-play-circle me-1'></i>Start Meeting Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

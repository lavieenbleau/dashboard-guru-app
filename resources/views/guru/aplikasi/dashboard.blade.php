@extends('layouts.sneat')

@section('content')

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">Hello {{ auth()->user()->name }} 👋</h3>
        <p class="text-muted m-0">Mari mulai pembelajaran hari ini!</p>
    </div>

    <div class="d-flex align-items-center">

        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}"
            class="rounded-circle shadow-sm" width="55">

        <div class="ms-3">
            <h6 class="fw-bold mb-0">{{ auth()->user()->name }}</h6>
            <small class="text-muted">{{ $serial->product->name }}</small>
        </div>
    </div>
</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 summary-box">
            <span class="badge bg-primary mb-2">Materi</span>
            <h3 class="fw-bold">{{ $stats['materi'] }}</h3>
            <p class="text-muted small mb-0">Materi Tersedia</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 summary-box">
            <span class="badge bg-info mb-2">Soal</span>
            <h3 class="fw-bold">{{ $stats['soal'] }}</h3>
            <p class="text-muted small mb-0">Soal & Ujian</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 summary-box">
            <span class="badge bg-warning mb-2">Tugas</span>
            <h3 class="fw-bold">{{ $stats['tugas'] }}</h3>
            <p class="text-muted small mb-0">Tugas Aktif</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 summary-box">
            <span class="badge bg-success mb-2">Kelas</span>
            <h3 class="fw-bold">{{ $stats['classrooms'] }}</h3>
            <p class="text-muted small mb-0">Total Kelas</p>
        </div>
    </div>

</div>

<!-- SECOND ROW STATS -->
<div class="row g-3 mb-4">
    
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-primary bg-opacity-10 rounded p-3">
                        <i class='bx bx-group fs-3 text-primary'></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="fw-bold mb-0">{{ $stats['students'] }}</h4>
                    <p class="text-muted small mb-0">Total Siswa</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-success bg-opacity-10 rounded p-3">
                        <i class='bx bx-video fs-3 text-success'></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="fw-bold mb-0">{{ $stats['online_meetings'] }}</h4>
                    <p class="text-muted small mb-0">Meeting Mendatang</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-warning bg-opacity-10 rounded p-3">
                        <i class='bx bx-task fs-3 text-warning'></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="fw-bold mb-0">{{ $stats['tasks_pending'] }}</h4>
                    <p class="text-muted small mb-0">Tugas Masuk</p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MIDDLE SECTION -->
<div class="row g-3 mb-4">

    <!-- RECENT ACTIVITIES -->
    <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0">
            <h6 class="fw-bold mb-3"><i class='bx bx-time-five me-2'></i>Aktivitas Terbaru</h6>
            
            @if($recentActivities->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentActivities as $activity)
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($activity->student->name ?? 'Student') }}&background=random&size=40" 
                                     class="rounded-circle me-3" width="40" height="40">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $activity->student->name ?? 'Unknown' }}</h6>
                                    <p class="mb-1 text-muted small">Mengumpulkan tugas: {{ $activity->post->title ?? 'Untitled' }}</p>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                @if($activity->point)
                                    <span class="badge bg-success">{{ $activity->point }} poin</span>
                                @else
                                    <span class="badge bg-warning">Belum dinilai</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class='bx bx-info-circle fs-1'></i>
                    <p class="mt-2">Belum ada aktivitas terbaru</p>
                </div>
            @endif
        </div>
    </div>

    <!-- UPCOMING MEETINGS -->
    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0">
            <h6 class="fw-bold mb-3"><i class='bx bx-calendar me-2'></i>Meeting Mendatang</h6>

            @if($upcomingMeetings->count() > 0)
                @foreach($upcomingMeetings as $meeting)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                @if($meeting->platform == 'Zoom')
                                    <i class='bx bxl-zoom fs-4 text-primary'></i>
                                @elseif($meeting->platform == 'Google Meet')
                                    <i class='bx bxl-google fs-4 text-danger'></i>
                                @else
                                    <i class='bx bx-video fs-4 text-info'></i>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-1 fw-bold">{{ $meeting->title }}</h6>
                                <small class="text-muted d-block">{{ $meeting->classroom->name ?? 'All Classes' }}</small>
                                <small class="text-primary">
                                    <i class='bx bx-time-five'></i>
                                    {{ \Carbon\Carbon::parse($meeting->start_time)->format('d M, H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
                <a href="{{ route('guru.onlineclass', $serial->id) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                    Lihat Semua Meeting
                </a>
            @else
                <div class="text-center py-4 text-muted">
                    <i class='bx bx-calendar-x fs-1'></i>
                    <p class="mt-2 small">Tidak ada meeting terjadwal</p>
                    <a href="{{ route('guru.onlineclass.create', $serial->id) }}" class="btn btn-sm btn-primary mt-2">
                        <i class='bx bx-plus'></i> Buat Meeting
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- MENU GRID (Materi, Pembelajaran, Tugas...) -->
<div class="row g-3 mt-3">

    <div class="col-md-3">
        <a href="{{ route('guru.materi', $serial->id) }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm menu-card">
                <i class='bx bx-book-open fs-1 text-primary'></i>
                <h6 class="mt-2 fw-bold">Materi</h6>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('guru.soal', $serial->id) }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm menu-card">
                <i class='bx bx-file-blank fs-1 text-info'></i>
                <h6 class="mt-2 fw-bold">Soal</h6>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('guru.tugas', $serial->id) }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm menu-card">
                <i class='bx bx-edit fs-1 text-warning'></i>
                <h6 class="mt-2 fw-bold">Tugas</h6>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('guru.onlineclass', $serial->id) }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm menu-card">
                <i class='bx bx-laptop fs-1 text-success'></i>
                <h6 class="mt-2 fw-bold">Online Class</h6>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('guru.laporanharian', $serial->id) }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm menu-card">
                <i class='bx bx-file fs-1 text-danger'></i>
                <h6 class="mt-2 fw-bold">Laporan Harian</h6>
            </div>
        </a>
    </div>

</div>

</div>

<style>
.summary-box {
    border-radius: 15px;
}

.menu-card {
    border-radius: 15px;
    transition: .2s;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}
</style>

@endsection
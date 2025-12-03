@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('guru.dashboard', $serial->id) }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Online Class</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class='bx bx-video text-success me-2'></i>Online Class</h4>
            <p class="text-muted mb-0">Kelola jadwal kelas online Anda</p>
        </div>
        <a href="{{ route('guru.onlineclass.create', $serial->id) }}" class="btn btn-success">
            <i class='bx bx-plus me-1'></i>Tambah Jadwal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Upcoming Meetings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class='bx bx-calendar-event me-2'></i>Jadwal Mendatang</h5>
        </div>
        <div class="card-body">
            @forelse($upcomingMeetings as $meeting)
                <div class="d-flex mb-3 pb-3 border-bottom">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            @if($meeting->platform == 'zoom')
                                <i class='bx bxl-zoom fs-4'></i>
                            @elseif($meeting->platform == 'google-meet')
                                <i class='bx bxl-google fs-4'></i>
                            @elseif($meeting->platform == 'teams')
                                <i class='bx bxl-microsoft-teams fs-4'></i>
                            @else
                                <i class='bx bx-video fs-4'></i>
                            @endif
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">{{ $meeting->title }}</h6>
                                <span class="badge bg-label-primary">{{ $meeting->classroom->name ?? 'Semua Kelas' }}</span>
                                <span class="badge bg-label-{{ $meeting->platform == 'zoom' ? 'info' : 'secondary' }}">{{ ucfirst($meeting->platform) }}</span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                    <i class='bx bx-dots-vertical-rounded'></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.onlineclass.edit', [$serial->id, $meeting->id]) }}">
                                            <i class='bx bx-edit me-1'></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('guru.onlineclass.destroy', [$serial->id, $meeting->id]) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class='bx bx-trash me-1'></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class='bx bx-calendar me-1'></i>{{ \Carbon\Carbon::parse($meeting->start_time)->isoFormat('dddd, D MMMM YYYY') }}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class='bx bx-time me-1'></i>{{ \Carbon\Carbon::parse($meeting->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}
                            </small>
                        </div>
                        @if($meeting->description)
                            <p class="mb-2 small">{{ $meeting->description }}</p>
                        @endif
                        <div class="d-flex gap-2">
                            <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-sm btn-success">
                                <i class='bx bx-video me-1'></i>Buka Link
                            </a>
                            @if($meeting->meeting_code)
                                <button class="btn btn-sm btn-outline-secondary" onclick="copyCode('{{ $meeting->meeting_code }}')">
                                    <i class='bx bx-copy me-1'></i>Kode: {{ $meeting->meeting_code }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class='bx bx-calendar-x fs-1 text-muted mb-3 d-block'></i>
                    <p class="text-muted">Belum ada jadwal mendatang</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Past Meetings -->
    @if($pastMeetings->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class='bx bx-history me-2'></i>Riwayat</h5>
        </div>
        <div class="card-body">
            @foreach($pastMeetings as $meeting)
                <div class="d-flex mb-3 pb-3 border-bottom opacity-75">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-secondary">
                            @if($meeting->platform == 'zoom')
                                <i class='bx bxl-zoom fs-4'></i>
                            @else
                                <i class='bx bx-video fs-4'></i>
                            @endif
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $meeting->title }}</h6>
                        <span class="badge bg-label-secondary">{{ $meeting->classroom->name ?? 'Semua Kelas' }}</span>
                        <br>
                        <small class="text-muted">
                            <i class='bx bx-calendar me-1'></i>{{ \Carbon\Carbon::parse($meeting->start_time)->isoFormat('D MMM YYYY, HH:mm') }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code);
    alert('Kode berhasil disalin: ' + code);
}
</script>
@endsection
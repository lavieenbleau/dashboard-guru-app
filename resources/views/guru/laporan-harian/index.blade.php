@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('guru.dashboard', $serial->id) }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Laporan Harian</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h4 class="mb-1"><i class='bx bx-calendar-check text-primary me-2'></i>Laporan Harian</h4>
        <p class="text-muted mb-0">Aktivitas siswa tercatat otomatis saat mengumpulkan tugas</p>
    </div>

    <div class="row">
        <!-- Calendar Column -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">{{ \Carbon\Carbon::parse($selectedDate)->isoFormat('MMMM YYYY') }}</h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="changeMonth(-1)">
                                <i class='bx bx-chevron-left'></i>
                            </button>
                            <button class="btn btn-outline-secondary" onclick="changeMonth(1)">
                                <i class='bx bx-chevron-right'></i>
                            </button>
                        </div>
                    </div>
                    
                    @php
                        $date = \Carbon\Carbon::parse($selectedDate);
                        $firstDay = $date->copy()->startOfMonth();
                        $lastDay = $date->copy()->endOfMonth();
                        $startDay = $firstDay->copy()->startOfWeek();
                        $endDay = $lastDay->copy()->endOfWeek();
                        $today = \Carbon\Carbon::today()->format('Y-m-d');
                    @endphp
                    
                    <div class="calendar-grid">
                        <div class="calendar-header">
                            <div class="calendar-day-name">Min</div>
                            <div class="calendar-day-name">Sen</div>
                            <div class="calendar-day-name">Sel</div>
                            <div class="calendar-day-name">Rab</div>
                            <div class="calendar-day-name">Kam</div>
                            <div class="calendar-day-name">Jum</div>
                            <div class="calendar-day-name">Sab</div>
                        </div>
                        <div class="calendar-body">
                            @php
                                $current = $startDay->copy();
                            @endphp
                            @while($current <= $endDay)
                                @php
                                    $dateStr = $current->format('Y-m-d');
                                    $isCurrentMonth = $current->month == $firstDay->month;
                                    $isToday = $dateStr == $today;
                                    $isSelected = $dateStr == $selectedDate;
                                    $hasActivity = isset($datesWithActivities[$dateStr]);
                                    $activityCount = $datesWithActivities[$dateStr] ?? 0;
                                @endphp
                                <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }} {{ $isSelected ? 'selected' : '' }} {{ $hasActivity ? 'has-activity' : '' }}"
                                     onclick="selectDate('{{ $dateStr }}')">
                                    <span class="day-number">{{ $current->day }}</span>
                                    @if($hasActivity)
                                        <span class="activity-badge">{{ $activityCount }}</span>
                                    @endif
                                </div>
                                @php
                                    $current->addDay();
                                @endphp
                            @endwhile
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activities Column -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ \Carbon\Carbon::parse($selectedDate)->isoFormat('dddd, D MMMM YYYY') }}</h5>
                    <span class="badge bg-label-primary">{{ $activities->count() }} aktivitas</span>
                </div>
                <div class="card-body">
                    @forelse($activities as $activity)
                        @php
                            if ($activity->source_type === 'task') {
                                $url = route('guru.laporanharian.review', [$serial->id, $activity->id]);
                            } else {
                                $url = route('guru.soal.student-answer-detail', [$activity->lesson_id ?? 0, $activity->exercise_id ?? 0, $activity->student_id]);
                            }
                        @endphp
                        <a href="{{ $url }}" class="d-flex mb-3 pb-3 border-bottom text-decoration-none text-dark" style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-{{ $activity->badge_color }}">
                                    {{ strtoupper(substr($activity->student_name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 text-dark">{{ $activity->student_name }}</h6>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->isoFormat('HH:mm') }}</small>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge bg-{{ $activity->badge_color }}">{{ $activity->activity_type }}</span>
                                        @if($activity->point === null)
                                            <span class="badge bg-label-warning">Belum Dinilai</span>
                                        @else
                                            <span class="badge bg-label-success">Sudah Dinilai : {{ $activity->point }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-primary">{{ $activity->task_title }}</strong>
                                    @if($activity->lesson_name)
                                        <br><small class="text-muted">{{ $activity->lesson_name }}</small>
                                    @endif
                                </div>
                                @if($activity->submission_description)
                                    <p class="mb-1 small text-dark">{{ Str::limit($activity->submission_description, 150) }}</p>
                                @endif
                                @if($activity->attachment)
                                    <span class="btn btn-sm btn-outline-primary me-2 mt-2">
                                        <i class='bx bx-link-external me-1'></i>Ada Lampiran
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i class='bx bx-folder-open fs-1 text-muted mb-3 d-block'></i>
                            <p class="text-muted">Belum ada pengumpulan tugas pada tanggal ini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-grid {
    font-size: 14px;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    margin-bottom: 5px;
}

.calendar-day-name {
    text-align: center;
    font-weight: 600;
    padding: 8px 4px;
    color: #666;
    font-size: 12px;
}

.calendar-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4px;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.calendar-day:hover {
    background-color: #f5f5f5;
    border-color: #e0e0e0;
}

.calendar-day.other-month {
    opacity: 0.3;
}

.calendar-day.today {
    background-color: #e3f2fd;
    border-color: #2196F3;
}

.calendar-day.selected {
    background-color: #2196F3;
    color: white;
    font-weight: bold;
}

.calendar-day.selected .activity-badge {
    background-color: white;
    color: #2196F3;
}

.calendar-day.has-activity {
    font-weight: 600;
}

.day-number {
    font-size: 13px;
}

.activity-badge {
    position: absolute;
    bottom: 2px;
    font-size: 9px;
    background-color: #4CAF50;
    color: white;
    border-radius: 8px;
    padding: 1px 4px;
    min-width: 16px;
    text-align: center;
}
</style>

<script>
function selectDate(date) {
    window.location.href = '{{ route("guru.laporanharian", $serial->id) }}?date=' + date;
}

function changeMonth(direction) {
    const currentDate = new Date('{{ $selectedDate }}');
    currentDate.setMonth(currentDate.getMonth() + direction);
    const newDate = currentDate.toISOString().split('T')[0];
    selectDate(newDate);
}
</script>
@endsection

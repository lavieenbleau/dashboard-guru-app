@extends('layouts.sneat')

@section('content')
<style>
/* SaaS Modern Hero Section */
.hero-wrapper {
    position: relative;
    border-radius: 32px;
    background: #f4f6ff;
    overflow: hidden;
    padding: 3.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 32px rgba(0,0,0,0.03);
    border: 1px solid rgba(255,255,255,0.8);
    z-index: 1;
}

/* Soft Decorative Blobs */
.hero-wrapper::before {
    content: '';
    position: absolute;
    width: 800px;
    height: 800px;
    background: radial-gradient(circle, rgba(230,232,255,0.9) 0%, rgba(255,255,255,0) 65%);
    top: -300px;
    right: -200px;
    z-index: -1;
    border-radius: 50%;
}
.hero-wrapper::after {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(230,232,255,0.7) 0%, rgba(255,255,255,0) 65%);
    bottom: -200px;
    left: 15%;
    z-index: -1;
    border-radius: 50%;
}

.hero-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04), inset 0 0 0 1px rgba(0,0,0,0.02);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    font-weight: 700;
    color: #5c60f5;
    margin-right: 1.5rem;
}

.hero-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: #0F172A;
    margin-bottom: 0.35rem;
    letter-spacing: -0.02em;
}

.hero-subtitle {
    font-size: 0.95rem;
    color: #64748B;
    font-weight: 500;
    margin-bottom: 2rem;
}

/* Horizontal Stats */
.hero-stats {
    display: flex;
    align-items: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-item:not(:last-child) {
    padding-right: 2.5rem;
    margin-right: 2.5rem;
    border-right: 1px solid rgba(0,0,0,0.08);
}

.stat-label {
    font-size: 0.75rem;
    color: #64748B;
    font-weight: 600;
    margin-bottom: 0.4rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1;
}

/* Right Side CTA */
.hero-cta {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.9);
    color: #5c60f5 !important;
    border-radius: 99px;
    padding: 0.85rem 1.75rem;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 4px 16px rgba(92, 96, 245, 0.08);
    transition: all 0.3s ease;
    text-decoration: none;
}

.hero-cta:hover {
    transform: translateY(-2px);
    background: #ffffff;
    box-shadow: 0 8px 24px rgba(92, 96, 245, 0.12);
}

/* Mini Stats Cards (Glassmorphism) */
.stat-glass-card {
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.stat-glass-card:hover {
    transform: translateY(-4px);
    background: rgba(255, 255, 255, 0.8);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04), 0 4px 8px rgba(0, 0, 0, 0.02);
    border-color: rgba(255, 255, 255, 1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1.25rem;
    flex-shrink: 0;
}

.stat-icon i { width: 24px; height: 24px; }
.stat-icon.indigo { background: rgba(105, 108, 255, 0.1); color: #696CFF; }
.stat-icon.cyan { background: rgba(6, 182, 212, 0.1); color: #06B6D4; }
.stat-icon.emerald { background: rgba(16, 185, 129, 0.1); color: #10B981; }
.stat-icon.amber { background: rgba(245, 158, 11, 0.1); color: #F59E0B; }

.stat-meta h4 {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0;
    color: #0F172A;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.stat-meta p {
    margin: 4px 0 0 0;
    font-size: 0.85rem;
    color: #64748B;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Section Headers */
.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.01em;
}

/* Modern Timeline / Panel */
.modern-panel {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border-radius: var(--radius-xl);
    border: 1px solid rgba(255, 255, 255, 0.8);
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.02);
    height: 100%;
}

.timeline-item {
    position: relative;
    padding-left: 32px;
    padding-bottom: 24px;
}

.timeline-item:last-child { padding-bottom: 0; }

.timeline-item::before {
    content: '';
    position: absolute;
    left: 6px; top: 8px; bottom: -8px;
    width: 2px;
    background: rgba(105, 108, 255, 0.15);
}

.timeline-item:last-child::before { display: none; }

.timeline-dot {
    position: absolute;
    left: 0; top: 4px;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: #696CFF;
    border: 3px solid #FFFFFF;
    box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.2);
    transition: all 0.3s ease;
}

.timeline-item:hover .timeline-dot {
    transform: scale(1.2);
    box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.3);
}

.timeline-content {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.01);
}

.timeline-content:hover {
    background: #FFFFFF;
    border-color: rgba(105, 108, 255, 0.2);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.03);
    transform: translateX(4px);
}

.timeline-time {
    font-size: 0.75rem;
    color: #94A3B8;
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Agenda Card Glass */
.agenda-card {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.8);
    background: rgba(255, 255, 255, 0.5);
    margin-bottom: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 8px rgba(0,0,0,0.01);
}

.agenda-card:hover {
    border-color: rgba(105, 108, 255, 0.3);
    background: rgba(255, 255, 255, 0.9);
    transform: translateX(6px);
    box-shadow: 0 8px 16px rgba(105, 108, 255, 0.05);
}

.agenda-date {
    background: #FFFFFF;
    border-radius: 12px;
    text-align: center;
    min-width: 64px;
    padding: 0.75rem 0.5rem;
    border: 1px solid rgba(0,0,0,0.03);
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.agenda-date .day {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1;
}

.agenda-date .month {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: #696CFF;
    text-transform: uppercase;
    margin-top: 6px;
    letter-spacing: 0.05em;
}

.agenda-info h6 {
    font-weight: 700;
    margin: 0 0 6px 0;
    color: #0F172A;
    font-size: 1rem;
}

.agenda-info p {
    margin: 0;
    font-size: 0.85rem;
    color: #64748B;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

/* Empty State */
.empty-modern {
    text-align: center;
    padding: 3rem 1.5rem;
}

.empty-modern .empty-icon {
    width: 72px;
    height: 72px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 0 8px 16px rgba(0,0,0,0.03);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem auto;
    color: #94A3B8;
}
</style>

<div class="container-fluid py-4 ps-1 pe-2">

    <!-- SAAS MODERN HERO SECTION -->
    <div class="hero-wrapper">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center align-items-start position-relative" style="z-index: 2;">
            <div class="d-flex align-items-start w-100">
                @php
                    $nameParts = explode(' ', auth()->user()->name);
                    $firstName = $nameParts[0];
                    $initials = substr($firstName, 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '');
                @endphp
                <div class="hero-avatar flex-shrink-0">
                    {{ strtoupper($initials) }}
                </div>
                <div class="flex-grow-1">
                    <h1 class="hero-title">Selamat datang kembali, {{ $firstName }}! 👋</h1>
                    <p class="hero-subtitle">Pantau aktivitas kelas {{ $serial->product->name }} dan tingkatkan produktivitas hari ini.</p>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-label">Materi Guru</span>
                            <span class="stat-value">{{ $stats['materi_guru'] ?? 0 }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Total Kelas</span>
                            <span class="stat-value">{{ $stats['classrooms'] ?? 0 }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Tugas Aktif</span>
                            <span class="stat-value">{{ $stats['tugas'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 mt-lg-0 flex-shrink-0 ms-lg-4 text-end">
                <a href="{{ route('guru.materi', $serial->id) }}" class="hero-cta">
                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                    Buat Materi Baru
                </a>
            </div>
        </div>
    </div>

    <!-- STATS GRID (ONLY 4 RELEVANT CARDS) -->
    <h5 class="section-title"><i data-lucide="bar-chart-2" style="width: 22px; color: #696CFF;"></i> Ringkasan Pembelajaran</h5>
    <div class="row g-4 mb-5">
        @php
            $filteredCards = collect($statCards)->filter(function($card) {
                return in_array($card['key'], ['materi', 'soal', 'students', 'tasks_pending']);
            });
        @endphp

        @foreach($filteredCards as $card)
        <div class="col-sm-6 col-xl-3">
            <div class="stat-glass-card">
                <div class="stat-icon {{ $card['iconClass'] }}">
                    <i data-lucide="{{ $card['icon'] }}"></i>
                </div>
                <div class="stat-meta">
                    <h4>{{ $stats[$card['key']] ?? 0 }}</h4>
                    <p>{{ $card['label'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- MAIN CONTENT TWO COLUMNS -->
    <div class="row g-4">

        <!-- TIMELINE AKTIVITAS (Activity Feed) -->
        <div class="col-xl-8">
            <div class="modern-panel">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                    <h5 class="section-title mb-0" style="margin-bottom:0 !important;"><i data-lucide="activity" style="width: 22px; color: #696CFF;"></i>
                        Aktivitas Terbaru</h5>
                    <a href="{{ route('guru.laporanharian', $serial->id) }}" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                </div>

                @if(isset($recentActivities) && $recentActivities->count() > 0)
                <div class="timeline-container mt-4">
                    @foreach($recentActivities as $activity)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($activity->student->name ?? 'Student') }}&background=f1f5f9&color=0f172a"
                                        class="rounded-circle me-3 border border-white" width="40" height="40" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <div>
                                        <span class="timeline-time">{{ $activity->created_at->diffForHumans() }}</span>
                                        <div class="fw-bold text-dark fs-6">{{ $activity->student->name ?? 'Unknown' }}</div>
                                        <div class="text-muted mt-1" style="font-size: 0.85rem;">Mengumpulkan tugas: <strong
                                                class="text-dark">{{ $activity->post->title ?? 'Tugas' }}</strong></div>
                                    </div>
                                </div>
                                <div>
                                    @if($activity->point)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 8px 16px; border: 1px solid rgba(16,185,129,0.2);">
                                        <i data-lucide="check-circle" style="width:14px; margin-right:6px;"></i>{{ $activity->point }} Poin
                                    </span>
                                    @else
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #D97706; padding: 8px 16px; border: 1px solid rgba(245,158,11,0.2);">
                                        <i data-lucide="clock" style="width:14px; margin-right:6px;"></i>Pending Review
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-modern">
                    <div class="empty-icon"><i data-lucide="inbox" style="width: 32px; height: 32px;"></i></div>
                    <h6 class="fw-bold text-dark">Belum Ada Aktivitas</h6>
                    <p class="text-muted">Aktivitas pengumpulan tugas siswa akan otomatis muncul di sini.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- AGENDA KELAS -->
        <div class="col-xl-4">
            <div class="modern-panel">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                    <h5 class="section-title mb-0" style="margin-bottom:0 !important;"><i data-lucide="calendar" style="width: 22px; color: #696CFF;"></i>
                        Agenda Kelas</h5>
                    <a href="{{ route('guru.meeting', $serial->id) }}" class="btn btn-outline-primary btn-sm px-2">
                        <i data-lucide="plus" style="width:18px;"></i>
                    </a>
                </div>

                @if(isset($upcomingMeetings) && $upcomingMeetings->count() > 0)
                <div class="agenda-list mt-4">
                    @foreach($upcomingMeetings as $meeting)
                    @php
                    $start = \Carbon\Carbon::parse($meeting->start_time);
                    @endphp
                    <a href="{{ route('guru.meeting', $serial->id) }}" class="agenda-card">
                        <div class="agenda-date">
                            <span class="day">{{ $start->format('d') }}</span>
                            <span class="month">{{ $start->format('M') }}</span>
                        </div>
                        <div class="agenda-info w-100">
                            <h6>{{ $meeting->title }}</h6>
                            <p class="mb-1"><i data-lucide="clock" style="width: 14px;"></i> {{ $start->format('H:i') }} WIB</p>
                            <p><i data-lucide="map-pin" style="width: 14px;"></i> {{ $meeting->classroom->name ?? 'Semua Kelas' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="empty-modern">
                    <div class="empty-icon"><i data-lucide="calendar-x" style="width: 32px; height: 32px;"></i></div>
                    <h6 class="fw-bold text-dark">Agenda Kosong</h6>
                    <p class="text-muted">Tidak ada jadwal meeting kelas online dalam waktu dekat.</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection
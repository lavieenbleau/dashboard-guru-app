<div id="sidebar" class="layout-menu menu-vertical menu bg-menu-theme shadow collapsed">

    <!-- Brand + Collapse Button -->
    <div class="app-brand p-3 d-flex justify-content-between align-items-center">

        <span class="app-brand-text fw-bold text-primary">Guru Panel</span>

        <button id="btnCollapse" class="btn btn-sm btn-outline-primary">
            <i class='bx bx-chevron-left'></i>
        </button>
    </div>

    <ul class="menu-inner py-1">

        @php
        // Resolve serial ID
        $serialId = null;

        if (isset($classroom) && $classroom) {
        $serialId = $classroom->serial_id ?? ($classroom->serial->id ?? null);
        }

        if (!$serialId && isset($serial)) {
        $serialId = $serial;
        }

        if (!$serialId) {
        $serialId = request()->segment(2);
        }

        // Fallback to user's first serial if still no serialId
        if (!$serialId && auth()->check()) {
        $userSerial = \App\Models\Serial::where('user_id', auth()->id())->first();
        $serialId = $userSerial ? $userSerial->id : null;
        }

        // If still no serial, try to get any serial (for development)
        if (!$serialId) {
        $anySerial = \App\Models\Serial::first();
        $serialId = $anySerial ? $anySerial->id : null;
        }

        // Dashboard URL
        $dashboardUrl = $serialId ? route('guru.dashboard', ['serial' => $serialId]) : route('pilih.aplikasi');

        // Check if on pilih aplikasi page
        $isPilihAplikasi = request()->is('aplikasi') || request()->is('pilih-aplikasi');
        @endphp

        @if(!$isPilihAplikasi)
        <!-- Dashboard -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId) ? 'active' : '' }}">
            <a href="{{ $dashboardUrl }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-home'></i></span>
                <div class="menu-text">Dashboard</div>
            </a>
        </li>

        <!-- Materi -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/materi*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.materi', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-book-open'></i></span>
                <div class="menu-text">Materi</div>
            </a>
        </li>

        <!-- Soal -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/soal*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.soal', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-file-blank'></i></span>
                <div class="menu-text">Soal</div>
            </a>
        </li>

        <!-- Tugas -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/tugas*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.tugas', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-edit'></i></span>
                <div class="menu-text">Tugas</div>
            </a>
        </li>

        <!-- Laporan Harian -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/laporan-harian*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.laporanharian', $serialId) : route('pilih.aplikasi') }}"
                class="menu-link">
                <span class="menu-icon"><i class='bx bx-file'></i></span>
                <div class="menu-text">Laporan Harian</div>
            </a>
        </li>

        <!-- Rekap Nilai -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/rekap-nilai*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.rekapnilai', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-list-check'></i></span>
                <div class="menu-text">Rekap Nilai</div>
            </a>
        </li>

        <!-- Kelas Online (Jitsi Meet) -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/meeting*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.meeting', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-video'></i></span>
                <div class="menu-text">Kelas Online</div>
            </a>
        </li>

        <!-- Kelas -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/kelas*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.kelas.pilih', $serialId) : route('pilih.aplikasi') }}"
                class="menu-link">
                <span class="menu-icon"><i class='bx bx-group'></i></span>
                <div class="menu-text">Kelas</div>
            </a>
        </li>
        @endif

        <!-- Pengaturan -->
        @php
        $pengaturanSerial = $serialId;
        if (!$pengaturanSerial && auth()->check()) {
        $firstSerial = \App\Models\Serial::where('user_id', auth()->id())->first()
        ?? \App\Models\Serial::first();
        $pengaturanSerial = $firstSerial ? $firstSerial->id : 1;
        }
        @endphp
        <li class="menu-item {{ request()->is('aplikasi/*/pengaturan*') ? 'active' : '' }}">
            <a href="{{ route('guru.pengaturan', $pengaturanSerial ?? 1) }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-cog'></i></span>
                <div class="menu-text">Pengaturan</div>
            </a>
        </li>

    </ul>

    <!-- Bottom Actions -->
    <div class="menu-bottom px-3 py-3 border-top">
        <div class="d-flex flex-column gap-2">
            @if(!$isPilihAplikasi)
            <a href="{{ route('guru.aplikasi') }}" class="btn btn-sm btn-outline-primary w-100">
                <i class='bx bx-grid-alt me-1'></i>
                <span class="menu-text">Pilih Aplikasi</span>
            </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                    <i class='bx bx-log-out me-1'></i>
                    <span class="menu-text">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>


<!-- SIDEBAR COLLAPSE STYLE -->
<style>
#sidebar {
    transition: width 0.25s ease-in-out;
    overflow-x: hidden;
    overflow-y: auto;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1000;
}

#sidebar.collapsed {
    width: 70px !important;
}

#sidebar:not(.collapsed) {
    width: 250px !important;
}

/* hide text when collapsed */
#sidebar.collapsed .menu-text,
#sidebar.collapsed .app-brand-text {
    display: none !important;
}

/* hide buttons text when collapsed, keep icons */
#sidebar.collapsed .menu-bottom .btn span.menu-text {
    display: none !important;
}

#sidebar.collapsed .menu-bottom .btn {
    padding: 0.375rem 0.5rem;
}

#sidebar.collapsed .menu-bottom .btn i {
    margin: 0 !important;
}

/* rotate chevron */
#sidebar.collapsed #btnCollapse i {
    transform: rotate(180deg);
}

.menu-icon i {
    font-size: 20px;
}

/* Bottom actions fixed at bottom */
.menu-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: inherit;
}

/* Add padding to menu-inner to prevent overlap with bottom actions */
.menu-inner {
    padding-bottom: 120px !important;
}

/* shift content based on sidebar state */
.layout-menu-expanded .layout-page {
    margin-left: 250px !important;
    transition: margin-left .25s;
}

.layout-menu-collapsed .layout-page {
    margin-left: 70px !important;
    transition: margin-left .25s;
}
</style>


<!-- COLLAPSE SCRIPT -->
<script>
document.getElementById('btnCollapse')?.addEventListener('click', () => {
    const sidebar = document.getElementById('sidebar');
    const html = document.documentElement;

    sidebar.classList.toggle('collapsed');

    if (html.classList.contains('layout-menu-expanded')) {
        html.classList.remove('layout-menu-expanded');
        html.classList.add('layout-menu-collapsed');
    } else {
        html.classList.add('layout-menu-expanded');
        html.classList.remove('layout-menu-collapsed');
    }
});
</script>
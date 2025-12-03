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
        @endphp


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

        <!-- Online Class -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/online-class*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.onlineclass', $serialId) : route('pilih.aplikasi') }}"
                class="menu-link">
                <span class="menu-icon"><i class='bx bx-video'></i></span>
                <div class="menu-text">Online Class</div>
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

        <!-- Pengaduan -->
        @php
        $pengaduanUrl = $serialId && Route::has('guru.pengaduan')
        ? route('guru.pengaduan', $serialId)
        : route('pilih.aplikasi');
        @endphp
        <li class="menu-item">
            <a href="{{ $pengaduanUrl }}" class="menu-link">
                <span class="menu-icon"><i class='bx bx-help-circle'></i></span>
                <div class="menu-text">Pengaduan</div>
            </a>
        </li>

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
</div>


<!-- SIDEBAR COLLAPSE STYLE -->
<style>
#sidebar {
    transition: width 0.25s ease-in-out;
    overflow-x: hidden;
    overflow-y: auto;
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

/* rotate chevron */
#sidebar.collapsed #btnCollapse i {
    transform: rotate(180deg);
}

.menu-icon i {
    font-size: 20px;
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
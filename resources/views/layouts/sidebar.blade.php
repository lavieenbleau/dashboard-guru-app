<div id="sidebar" class="layout-menu menu-vertical menu bg-menu-theme shadow">

    <!-- Brand + Logo -->
    <div class="app-brand p-3">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/logo-sci.png') }}" alt="SCI Media" height="32" class="me-2">
            <span class="app-brand-text fw-bold text-primary">Guru Panel</span>
        </div>
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
        
        // Check if user has no serials
        $userSerialCount = auth()->check() ? \App\Models\Serial::where('user_id', auth()->id())->count() : 0;
        $hasNoSerial = auth()->check() && $userSerialCount == 0;
        @endphp

        <!-- Dashboard always shown if no serial -->
        @if(!$isPilihAplikasi || $hasNoSerial)
        <li
            class="menu-item {{ (request()->is('aplikasi') || request()->is('aplikasi/'.$serialId) && request()->path() == 'aplikasi/'.$serialId) || request()->routeIs('guru.dashboard') ? 'active' : '' }}">
            <a href="{{ $hasNoSerial ? route('guru.aplikasi') : $dashboardUrl }}" class="menu-link">
                <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                <div>Dashboard</div>
            </a>
        </li>
        @endif

        @if(!$isPilihAplikasi && !$hasNoSerial)
        <!-- Materi -->
        <li class="menu-item {{ request()->routeIs('guru.materi*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.materi', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i data-lucide="library"></i></span>
                <div>Materi</div>
            </a>
        </li>

        <!-- Soal -->
        <li class="menu-item {{ request()->routeIs('guru.soal*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.soal', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i data-lucide="file-text"></i></span>
                <div>Soal</div>
            </a>
        </li>

        <!-- Tugas -->
        <li class="menu-item {{ request()->routeIs('guru.tugas*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.tugas', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i data-lucide="edit-3"></i></span>
                <div>Tugas</div>
            </a>
        </li>

        <!-- Laporan Harian -->
        <li
            class="menu-item {{ request()->routeIs('guru.laporanharian*') || request()->routeIs('guru.laporan*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.laporanharian', $serialId) : route('pilih.aplikasi') }}"
                class="menu-link">
                <span class="menu-icon"><i data-lucide="activity"></i></span>
                <div>Laporan Harian</div>
            </a>
        </li>

        <!-- Rekap Nilai -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/rekap-nilai*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.rekapnilai', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i data-lucide="check-square"></i></span>
                <div>Rekap Nilai</div>
            </a>
        </li>

        <!-- Kelas Online (Jitsi Meet) -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/meeting*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.meeting', $serialId) : route('pilih.aplikasi') }}" class="menu-link">
                <span class="menu-icon"><i data-lucide="video"></i></span>
                <div>Kelas Online</div>
            </a>
        </li>

        <!-- Kelas -->
        <li class="menu-item {{ request()->is('aplikasi/'.$serialId.'/kelas*') ? 'active' : '' }}">
            <a href="{{ $serialId ? route('guru.kelas.pilih', $serialId) : route('pilih.aplikasi') }}"
                class="menu-link">
                <span class="menu-icon"><i data-lucide="users"></i></span>
                <div>Kelas</div>
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
                <span class="menu-icon"><i data-lucide="settings"></i></span>
                <div class="menu-text">Profil & Pengaturan</div>
            </a>
        </li>

        @if($hasNoSerial)
        <!-- Hubungi Admin -->
        <li class="menu-item">
            <a href="https://tak-scimediaonline.my.id/layanan-pelanggan-pelapor" target="_blank" class="menu-link">
                <span class="menu-icon"><i data-lucide="help-circle"></i></span>
                <div class="menu-text">Hubungi Admin</div>
            </a>
        </li>
        @endif

    </ul>

    <!-- Bottom Actions -->
    <div class="menu-bottom px-3 py-3 border-top">
        <div class="d-flex flex-column gap-2">
            @if(!$isPilihAplikasi && !$hasNoSerial)
            <a href="{{ route('guru.aplikasi') }}" class="btn btn-sm btn-outline-primary w-100">
                <i data-lucide="grid" class="me-2" style="width:16px;"></i>
                <span class="menu-text">Pilih Aplikasi</span>
            </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                    <i data-lucide="log-out" class="me-2" style="width:16px;"></i>
                    <span class="menu-text">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>


<!-- SIDEBAR STYLE -->
<style>
#sidebar {
    overflow-x: hidden;
    overflow-y: auto;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1000;
    width: 250px !important;
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

/* shift content based on sidebar */
.layout-page {
    margin-left: 250px !important;
    padding: 0;
}

.layout-container {
    padding: 0;
    margin: 0;
}

.layout-wrapper {
    padding: 0;
    margin: 0;
}

/* Active menu item styling is handled in sneat.blade.php */
.menu-item .menu-link {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.menu-item:hover:not(.active) {
    background-color: rgba(255, 255, 255, 0.5);
}
</style>

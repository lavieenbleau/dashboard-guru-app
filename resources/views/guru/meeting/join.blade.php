<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meeting->title }} - Kelas Online</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        overflow: hidden;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    .meeting-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    #jitsi-container {
        background: #f5f5f5;
        transition: height 0.3s ease;
    }

    .btn-meeting {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-meeting:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    </style>
</head>

<body>
    <div class="container-fluid p-0" style="height: 100vh;">
        <!-- Meeting Header -->
        <div class="meeting-header text-white p-3" id="meetingHeader">
            <div class="container-xxl">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $meeting->title }}</h5>
                        <small class="opacity-75">
                            <i class='bx bx-calendar me-1'></i>
                            {{ $meeting->start_time->format('d M Y, H:i') }} WIB
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light btn-meeting" onclick="toggleHeader()"
                            title="Sembunyikan Header">
                            <i class='bx bx-chevron-up' id="toggleIcon"></i>
                        </button>
                        <form action="{{ route('guru.meeting.end', [$serial->id, $meeting->id]) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger btn-meeting"
                                onclick="return confirm('Akhiri meeting ini untuk semua peserta?')"
                                title="Akhiri Meeting">
                                <i class='bx bx-stop-circle me-1'></i>Akhiri
                            </button>
                        </form>
                        <a href="{{ route('guru.meeting', $serial->id) }}" class="btn btn-sm btn-secondary btn-meeting"
                            title="Keluar dari Meeting">
                            <i class='bx bx-exit me-1'></i>Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jitsi Meet Container -->
        <div id="jitsi-container" style="height: calc(100vh - 68px);"></div>

        <!-- Loading Indicator -->
        <div id="loading-indicator"
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat meeting room...</p>
        </div>
    </div>

    <!-- Jitsi Meet External API -->
    <script src='https://meet.jit.si/external_api.js'></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Wait for page to fully load
    window.addEventListener('load', function() {
        console.log('Page loaded, checking Jitsi API...');

        // Check if JitsiMeetExternalAPI is available
        if (typeof JitsiMeetExternalAPI === 'undefined') {
            console.error('JitsiMeetExternalAPI not loaded!');
            document.getElementById('loading-indicator').innerHTML =
                '<div class="alert alert-danger">Gagal memuat Jitsi Meet API. <a href="" class="alert-link">Refresh halaman</a></div>';
            return;
        }

        console.log('Jitsi API loaded successfully');

        // Jitsi Meet Configuration
        const domain = 'meet.jit.si';
        const options = {
            roomName: '{{ $meeting->meeting_code }}',
            width: '100%',
            height: '100%',
            parentNode: document.querySelector('#jitsi-container'),
            configOverwrite: {
                startWithAudioMuted: false,
                startWithVideoMuted: false,
                enableWelcomePage: false,
                prejoinPageEnabled: false,
                disableDeepLinking: true,
                defaultLanguage: 'id',
                requireDisplayName: false,
            },
            interfaceConfigOverwrite: {
                TOOLBAR_BUTTONS: [
                    'microphone', 'camera', 'desktop', 'fullscreen',
                    'fodeviceselection', 'hangup', 'chat', 'settings', 'raisehand',
                    'videoquality', 'filmstrip', 'tileview', 'download'
                ],
                SHOW_JITSI_WATERMARK: false,
                SHOW_WATERMARK_FOR_GUESTS: false,
                SHOW_BRAND_WATERMARK: false,
                SHOW_POWERED_BY: false,
                DEFAULT_REMOTE_DISPLAY_NAME: 'Peserta',
                DEFAULT_LOCAL_DISPLAY_NAME: '{{ $userName }}',
                APP_NAME: 'Kelas Online',
                MOBILE_APP_PROMO: false,
            },
            userInfo: {
                email: '{{ $userEmail }}',
                displayName: '{{ $userName }} (Guru)',
            }
        };

        console.log('Initializing Jitsi with room:', '{{ $meeting->meeting_code }}');

        try {
            // Initialize Jitsi Meet
            const api = new JitsiMeetExternalAPI(domain, options);

            // Hide loading indicator after a short delay
            setTimeout(() => {
                const loadingEl = document.getElementById('loading-indicator');
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }
            }, 2000);

            console.log('Jitsi Meet initialized successfully');

            // Event: Conference Joined
            api.addEventListener('videoConferenceJoined', function(event) {
                console.log('✅ Joined conference:', event);
                // Hide loading completely
                const loadingEl = document.getElementById('loading-indicator');
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }
            });

            // Don't auto-redirect on leave - let user click exit button manually
            console.log('Meeting room ready. Click "Keluar" button to exit.');

        } catch (error) {
            console.error('❌ Error initializing Jitsi Meet:', error);
            document.getElementById('loading-indicator').innerHTML =
                '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        }
    });

    // Toggle header visibility
    function toggleHeader() {
        const header = document.getElementById('meetingHeader');
        const icon = document.getElementById('toggleIcon');
        const container = document.getElementById('jitsi-container');

        if (header.style.display === 'none') {
            header.style.display = 'block';
            container.style.height = 'calc(100vh - 68px)';
            icon.className = 'bx bx-chevron-up';
        } else {
            header.style.display = 'none';
            container.style.height = '100vh';
            icon.className = 'bx bx-chevron-down';
        }
    }
    </script>
</body>

</html>
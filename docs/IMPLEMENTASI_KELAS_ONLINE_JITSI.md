# Panduan Implementasi Kelas Online dengan Jitsi Meets
## Step-by-Step Development Guide

---

## 📋 Daftar Isi
1. [Persiapan & Requirements](#persiapan)
2. [Setup Jitsi Server](#setup-jitsi)
3. [Database Migration](#database)
4. [Model & Relationships](#model)
5. [Controller Implementation](#controller)
6. [Routes](#routes)
7. [Views (Blade Templates)](#views)
8. [JavaScript Integration](#javascript)
9. [JWT Token Generation](#jwt)
10. [Testing](#testing)

---

## 🎯 Persiapan & Requirements {#persiapan}

### Tech Stack
- **Backend**: Laravel 11
- **Database**: MySQL 8.0+
- **Frontend**: Blade + Vanilla JS / Vue.js / React
- **Video Conference**: Jitsi Meet (Self-hosted atau Jitsi.org)
- **JWT**: firebase/php-jwt package

### Install Dependencies

```bash
# Install JWT package
composer require firebase/php-jwt

# Install Guzzle untuk HTTP requests (jika belum ada)
composer require guzzlehttp/guzzle
```

### Environment Variables

Tambahkan ke file `.env`:

```env
# Jitsi Configuration
JITSI_SERVER_URL=https://meet.jit.si
JITSI_APP_ID=your_app_id
JITSI_SECRET_KEY=your_secret_key
JITSI_TOKEN_EXPIRATION=86400

# Atau untuk self-hosted
# JITSI_SERVER_URL=https://your-jitsi-server.com
```

---

## 🖥️ Setup Jitsi Server {#setup-jitsi}

### Opsi 1: Gunakan Jitsi.org (Free, Public)

**Kelebihan**:
- ✅ Gratis
- ✅ Tidak perlu setup server
- ✅ Langsung pakai

**Kekurangan**:
- ❌ Tidak bisa custom branding
- ❌ Limited control
- ❌ Public server (semua orang bisa akses room jika tahu nama)

```php
// config/jitsi.php
return [
    'server_url' => env('JITSI_SERVER_URL', 'https://meet.jit.si'),
    'app_id' => env('JITSI_APP_ID', ''),
    'secret_key' => env('JITSI_SECRET_KEY', ''),
    'token_expiration' => env('JITSI_TOKEN_EXPIRATION', 86400), // 24 hours
];
```

### Opsi 2: Self-Hosted Jitsi Server (Recommended untuk Production)

**Requirements**:
- VPS/Cloud Server (minimal 2 CPU, 4GB RAM)
- Ubuntu 20.04/22.04 LTS
- Domain dengan SSL certificate
- Port 443, 4443, 10000 UDP terbuka

**Install Jitsi di Ubuntu Server**:

```bash
# 1. Update system
sudo apt update
sudo apt upgrade -y

# 2. Set hostname
sudo hostnamectl set-hostname meet.yourdomain.com

# 3. Add Jitsi repository
curl https://download.jitsi.org/jitsi-key.gpg.key | sudo sh -c 'gpg --dearmor > /usr/share/keyrings/jitsi-keyring.gpg'
echo 'deb [signed-by=/usr/share/keyrings/jitsi-keyring.gpg] https://download.jitsi.org stable/' | sudo tee /etc/apt/sources.list.d/jitsi-stable.list > /dev/null

# 4. Update package list
sudo apt update

# 5. Install Jitsi Meet
sudo apt install jitsi-meet -y

# 6. Setup SSL dengan Let's Encrypt
sudo /usr/share/jitsi-meet/scripts/install-letsencrypt-cert.sh
```

**Enable JWT Authentication** (agar hanya user terautentikasi yang bisa join):

```bash
# Edit config
sudo nano /etc/jitsi/meet/meet.yourdomain.com-config.js
```

Tambahkan:
```javascript
var config = {
    hosts: {
        domain: 'meet.yourdomain.com',
        muc: 'conference.meet.yourdomain.com'
    },
    // Enable authentication
    enableUserRolesBasedOnToken: true,
    // ...
};
```

```bash
# Edit prosody config
sudo nano /etc/prosody/conf.avail/meet.yourdomain.com.cfg.lua
```

Ubah authentication menjadi:
```lua
VirtualHost "meet.yourdomain.com"
    authentication = "token"
    app_id = "your_app_id"
    app_secret = "your_secret_key"
    
    -- ...
```

```bash
# Restart services
sudo systemctl restart prosody
sudo systemctl restart jicofo
sudo systemctl restart jitsi-videobridge2
```

---

## 🗄️ Database Migration {#database}

### Create Migration

```bash
php artisan make:migration create_online_meetings_table
```

### Migration File

```php
<?php
// database/migrations/xxxx_xx_xx_create_online_meetings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Guru yang buat
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('room_name')->unique(); // Jitsi room name
            
            $table->enum('status', ['scheduled', 'active', 'ended'])->default('scheduled');
            
            $table->timestamp('scheduled_at')->nullable(); // Untuk scheduled meeting
            $table->timestamp('started_at')->nullable();   // Actual start time
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable(); // Expected duration
            
            // Optional: Track participants
            $table->json('participants')->nullable(); // Array of student IDs yang join
            
            $table->timestamps();
            
            // Indexes
            $table->index(['serial_id', 'classroom_id', 'status']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_meetings');
    }
};
```

### Run Migration

```bash
php artisan migrate
```

---

## 📦 Model & Relationships {#model}

### Create Model

```bash
php artisan make:model OnlineMeeting
```

### Model Implementation

```php
<?php
// app/Models/OnlineMeeting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_id',
        'classroom_id',
        'user_id',
        'title',
        'description',
        'room_name',
        'status',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'participants',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'participants' => 'array',
    ];

    // Relationships
    public function serial(): BelongsTo
    {
        return $this->belongsTo(Serial::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function canJoin(): bool
    {
        if ($this->isEnded()) {
            return false;
        }

        // Bisa join jika active atau scheduled dengan waktu sudah dekat (10 menit sebelum)
        if ($this->isActive()) {
            return true;
        }

        if ($this->scheduled_at) {
            return now()->diffInMinutes($this->scheduled_at, false) <= 10;
        }

        return false;
    }

    public function generateRoomName(): string
    {
        return sprintf(
            '%s-%s-%s',
            $this->serial->slug ?? 'room',
            $this->classroom_id ?? 'general',
            now()->timestamp
        );
    }
}
```

---

## 🎮 Controller Implementation {#controller}

### Create Controller

```bash
php artisan make:controller OnlineMeetingController
```

### Controller Code

```php
<?php
// app/Http/Controllers/OnlineMeetingController.php

namespace App\Http\Controllers;

use App\Models\OnlineMeeting;
use App\Models\Classroom;
use App\Services\JitsiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineMeetingController extends Controller
{
    protected $jitsiService;

    public function __construct(JitsiService $jitsiService)
    {
        $this->jitsiService = $jitsiService;
    }

    /**
     * Display list of meetings
     */
    public function index(Request $request, $serial, $classroom = null)
    {
        $query = OnlineMeeting::with(['classroom', 'user'])
            ->where('serial_id', $serial);

        if ($classroom) {
            $query->where('classroom_id', $classroom);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show active & scheduled only
            $query->whereIn('status', ['scheduled', 'active']);
        }

        $meetings = $query->orderBy('scheduled_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('meetings.index', compact('meetings'));
    }

    /**
     * Quick start meeting (langsung mulai tanpa schedule)
     */
    public function quickStart(Request $request, $serial, $classroom)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $classroomModel = Classroom::findOrFail($classroom);

        // Create meeting
        $meeting = OnlineMeeting::create([
            'serial_id' => $serial,
            'classroom_id' => $classroom,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'room_name' => $this->generateRoomName($serial, $classroom),
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Redirect to join page
        return redirect()->route('meetings.join', [
            'serial' => $serial,
            'meeting' => $meeting->id,
        ]);
    }

    /**
     * Schedule a meeting
     */
    public function store(Request $request, $serial, $classroom)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
        ]);

        $meeting = OnlineMeeting::create([
            'serial_id' => $serial,
            'classroom_id' => $classroom,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'room_name' => $this->generateRoomName($serial, $classroom),
            'status' => 'scheduled',
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
        ]);

        return redirect()->route('meetings.index', ['serial' => $serial, 'classroom' => $classroom])
            ->with('success', 'Meeting berhasil dijadwalkan');
    }

    /**
     * Join meeting (guru & siswa)
     */
    public function join(Request $request, $serial, $meeting)
    {
        $meeting = OnlineMeeting::findOrFail($meeting);
        $user = Auth::user();

        // Check if meeting can be joined
        if ($meeting->isEnded()) {
            return redirect()->back()->withErrors('Meeting sudah berakhir');
        }

        if (!$meeting->canJoin()) {
            return redirect()->back()->withErrors(
                'Meeting belum bisa dimulai. Silakan tunggu hingga 10 menit sebelum jadwal.'
            );
        }

        // Update status to active if scheduled
        if ($meeting->status === 'scheduled') {
            $meeting->update([
                'status' => 'active',
                'started_at' => now(),
            ]);
        }

        // Determine if user is moderator (guru)
        $isModerator = $meeting->user_id === $user->id || $user->hasRole('guru');

        // Generate JWT token
        $token = $this->jitsiService->generateToken(
            $meeting->room_name,
            $user,
            $isModerator
        );

        // Build Jitsi URL
        $jitsiUrl = $this->jitsiService->buildMeetingUrl($meeting->room_name, $token);

        return view('meetings.join', compact('meeting', 'jitsiUrl', 'token', 'isModerator'));
    }

    /**
     * End meeting (guru only)
     */
    public function end(Request $request, $serial, $meeting)
    {
        $meeting = OnlineMeeting::findOrFail($meeting);

        // Only owner can end meeting
        if ($meeting->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $meeting->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return redirect()->route('meetings.index', ['serial' => $serial])
            ->with('success', 'Meeting berhasil diakhiri');
    }

    /**
     * Delete meeting (scheduled only)
     */
    public function destroy($serial, $meeting)
    {
        $meeting = OnlineMeeting::findOrFail($meeting);

        // Only owner can delete
        if ($meeting->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Cannot delete active meeting
        if ($meeting->isActive()) {
            return redirect()->back()->withErrors('Meeting yang sedang berlangsung tidak bisa dihapus');
        }

        $meeting->delete();

        return redirect()->route('meetings.index', ['serial' => $serial])
            ->with('success', 'Meeting berhasil dihapus');
    }

    /**
     * Generate unique room name
     */
    private function generateRoomName($serialId, $classroomId)
    {
        return sprintf(
            'room-%s-%s-%s',
            $serialId,
            $classroomId,
            now()->timestamp
        );
    }
}
```

---

## 🔐 JWT Token Generation Service {#jwt}

### Create Service

```bash
php artisan make:service JitsiService
```

Atau buat manual:

```php
<?php
// app/Services/JitsiService.php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Config;

class JitsiService
{
    /**
     * Generate JWT token for Jitsi meeting
     */
    public function generateToken(string $roomName, $user, bool $isModerator = false): string
    {
        $appId = Config::get('jitsi.app_id');
        $secretKey = Config::get('jitsi.secret_key');
        $serverUrl = Config::get('jitsi.server_url');
        $expiration = Config::get('jitsi.token_expiration', 86400);

        // Parse domain from server URL
        $domain = parse_url($serverUrl, PHP_URL_HOST);

        $payload = [
            // Context
            'context' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email ?? '',
                    'avatar' => $user->avatar ?? '',
                ],
                'features' => [
                    'livestreaming' => $isModerator,
                    'recording' => $isModerator,
                    'transcription' => false,
                    'screen-sharing' => true,
                ],
            ],

            // Moderator status
            'moderator' => $isModerator,

            // Room
            'room' => $roomName,

            // Standard JWT claims
            'iss' => $appId,
            'aud' => $appId,
            'sub' => $domain,
            'iat' => time(),
            'exp' => time() + $expiration,
            'nbf' => time() - 10, // 10 seconds before to account for clock skew
        ];

        return JWT::encode($payload, $secretKey, 'HS256');
    }

    /**
     * Build Jitsi meeting URL
     */
    public function buildMeetingUrl(string $roomName, string $token = null): string
    {
        $serverUrl = Config::get('jitsi.server_url');
        $url = rtrim($serverUrl, '/') . '/' . $roomName;

        if ($token) {
            $url .= '?jwt=' . $token;
        }

        return $url;
    }

    /**
     * Verify if Jitsi server is reachable
     */
    public function checkServerHealth(): bool
    {
        try {
            $serverUrl = Config::get('jitsi.server_url');
            $response = \Http::timeout(5)->get($serverUrl);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
```

### Register Service Provider

```php
// app/Providers/AppServiceProvider.php

public function register(): void
{
    $this->app->singleton(\App\Services\JitsiService::class, function ($app) {
        return new \App\Services\JitsiService();
    });
}
```

---

## 🛣️ Routes {#routes}

```php
<?php
// routes/web.php

use App\Http\Controllers\OnlineMeetingController;

Route::middleware(['auth'])->group(function () {
    
    // Meeting routes
    Route::prefix('aplikasi/{serial}')->group(function () {
        
        // List meetings
        Route::get('/meetings', [OnlineMeetingController::class, 'index'])
            ->name('meetings.index');
        
        Route::get('/kelas/{classroom}/meetings', [OnlineMeetingController::class, 'index'])
            ->name('meetings.classroom');
        
        // Quick start
        Route::post('/kelas/{classroom}/meetings/quick-start', [OnlineMeetingController::class, 'quickStart'])
            ->name('meetings.quick-start');
        
        // Schedule meeting
        Route::post('/kelas/{classroom}/meetings', [OnlineMeetingController::class, 'store'])
            ->name('meetings.store');
        
        // Join meeting
        Route::get('/meetings/{meeting}/join', [OnlineMeetingController::class, 'join'])
            ->name('meetings.join');
        
        // End meeting (guru only)
        Route::post('/meetings/{meeting}/end', [OnlineMeetingController::class, 'end'])
            ->name('meetings.end');
        
        // Delete meeting
        Route::delete('/meetings/{meeting}', [OnlineMeetingController::class, 'destroy'])
            ->name('meetings.destroy');
    });
});
```

---

## 🎨 Views (Blade Templates) {#views}

### 1. Meeting List Page

```blade
{{-- resources/views/meetings/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelas Online</h2>
        <div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickStartModal">
                <i class="fas fa-play"></i> Mulai Sekarang
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                <i class="fas fa-calendar"></i> Jadwalkan Meeting
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ request('status') != 'ended' ? 'active' : '' }}" 
               href="{{ route('meetings.index', ['serial' => $serial]) }}">
                Aktif & Terjadwal
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'ended' ? 'active' : '' }}" 
               href="{{ route('meetings.index', ['serial' => $serial, 'status' => 'ended']) }}">
                Riwayat
            </a>
        </li>
    </ul>

    {{-- Meeting List --}}
    <div class="row">
        @forelse($meetings as $meeting)
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title">{{ $meeting->title }}</h5>
                        @if($meeting->status === 'active')
                            <span class="badge bg-danger">
                                <i class="fas fa-circle"></i> LIVE
                            </span>
                        @elseif($meeting->status === 'scheduled')
                            <span class="badge bg-info">Scheduled</span>
                        @else
                            <span class="badge bg-secondary">Ended</span>
                        @endif
                    </div>

                    <p class="text-muted small mb-2">
                        <i class="fas fa-user"></i> {{ $meeting->user->name }}
                        @if($meeting->classroom)
                            | <i class="fas fa-class"></i> {{ $meeting->classroom->name }}
                        @endif
                    </p>

                    @if($meeting->scheduled_at)
                        <p class="mb-2">
                            <i class="fas fa-clock"></i> 
                            {{ $meeting->scheduled_at->format('d M Y, H:i') }}
                        </p>
                    @endif

                    @if($meeting->description)
                        <p class="text-muted small">{{ Str::limit($meeting->description, 100) }}</p>
                    @endif

                    <div class="d-flex gap-2 mt-3">
                        @if($meeting->canJoin())
                            <a href="{{ route('meetings.join', ['serial' => $serial, 'meeting' => $meeting->id]) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-video"></i> Join Meeting
                            </a>
                        @endif

                        @if($meeting->user_id === auth()->id())
                            @if($meeting->isActive())
                                <form action="{{ route('meetings.end', ['serial' => $serial, 'meeting' => $meeting->id]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm" 
                                            onclick="return confirm('Akhiri meeting ini?')">
                                        <i class="fas fa-stop"></i> End Meeting
                                    </button>
                                </form>
                            @endif

                            @if(!$meeting->isActive())
                                <form action="{{ route('meetings.destroy', ['serial' => $serial, 'meeting' => $meeting->id]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Hapus meeting ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                Belum ada meeting. Klik "Mulai Sekarang" untuk memulai kelas online.
            </div>
        </div>
        @endforelse
    </div>

    {{ $meetings->links() }}
</div>

{{-- Quick Start Modal --}}
<div class="modal fade" id="quickStartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('meetings.quick-start', ['serial' => $serial, 'classroom' => $classroom ?? '']) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mulai Kelas Sekarang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Meeting *</label>
                        <input type="text" name="title" class="form-control" required 
                               placeholder="Contoh: Matematika Kelas 7A">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Mulai Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Schedule Modal --}}
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('meetings.store', ['serial' => $serial, 'classroom' => $classroom ?? '']) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Jadwalkan Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal & Waktu *</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required 
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durasi (menit)</label>
                        <input type="number" name="duration_minutes" class="form-control" 
                               min="15" max="480" placeholder="60">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Jadwalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

### 2. Join Meeting Page

```blade
{{-- resources/views/meetings/join.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="meeting-container" style="height: 100vh;">
        {{-- Meeting Header --}}
        <div class="meeting-header bg-dark text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $meeting->title }}</h5>
                    <small>{{ $meeting->classroom->name ?? 'General Meeting' }}</small>
                </div>
                <div>
                    @if($isModerator)
                        <span class="badge bg-warning">Moderator</span>
                    @endif
                    <a href="{{ route('meetings.index', ['serial' => $serial ?? '']) }}" 
                       class="btn btn-sm btn-outline-light ms-2">
                        <i class="fas fa-times"></i> Keluar
                    </a>
                </div>
            </div>
        </div>

        {{-- Jitsi Meet Container --}}
        <div id="jitsi-container" style="height: calc(100vh - 60px);"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://meet.jit.si/external_api.js"></script>
<script>
    const domain = "{{ parse_url(config('jitsi.server_url'), PHP_URL_HOST) }}";
    const roomName = "{{ $meeting->room_name }}";
    const jwt = "{{ $token }}";
    const userName = "{{ auth()->user()->name }}";
    const isModerator = {{ $isModerator ? 'true' : 'false' }};

    const options = {
        roomName: roomName,
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-container'),
        jwt: jwt,
        userInfo: {
            displayName: userName,
            email: "{{ auth()->user()->email ?? '' }}"
        },
        configOverwrite: {
            startWithAudioMuted: !isModerator,
            startWithVideoMuted: !isModerator,
            enableWelcomePage: false,
            prejoinPageEnabled: true,
            disableDeepLinking: true,
            toolbarButtons: [
                'microphone',
                'camera',
                'desktop',
                'fullscreen',
                'fodeviceselection',
                'hangup',
                'profile',
                'chat',
                'recording',
                'livestreaming',
                'etherpad',
                'sharedvideo',
                'settings',
                'raisehand',
                'videoquality',
                'filmstrip',
                'feedback',
                'stats',
                'shortcuts',
                'tileview',
                'download',
                'help',
                'mute-everyone'
            ].filter(btn => {
                // Remove moderator-only buttons for non-moderators
                if (!isModerator) {
                    return !['recording', 'livestreaming', 'mute-everyone'].includes(btn);
                }
                return true;
            })
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            DEFAULT_REMOTE_DISPLAY_NAME: 'Peserta',
            MOBILE_APP_PROMO: false
        }
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    // Event listeners
    api.addEventListener('readyToClose', () => {
        window.location.href = "{{ route('meetings.index', ['serial' => $serial ?? '']) }}";
    });

    api.addEventListener('participantJoined', (participant) => {
        console.log('Participant joined:', participant);
    });

    api.addEventListener('participantLeft', (participant) => {
        console.log('Participant left:', participant);
    });

    // Jika meeting ended oleh moderator
    @if($isModerator)
    api.addEventListener('readyToClose', () => {
        // Call API to update meeting status
        fetch("{{ route('meetings.end', ['serial' => $serial ?? '', 'meeting' => $meeting->id]) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            window.location.href = "{{ route('meetings.index', ['serial' => $serial ?? '']) }}";
        });
    });
    @endif
</script>
@endpush
@endsection
```

---

## ✅ Testing {#testing}

### 1. Manual Testing Checklist

```
□ Quick Start Meeting
  □ Buat meeting langsung tanpa schedule
  □ Auto redirect ke meeting room
  □ Token JWT ter-generate dengan benar
  □ Guru masuk sebagai moderator
  
□ Schedule Meeting
  □ Set meeting di masa depan
  □ Validasi tanggal harus > sekarang
  □ Simpan ke database dengan status 'scheduled'
  
□ Join Meeting
  □ Guru bisa join sebagai moderator
  □ Siswa bisa join sebagai participant
  □ Meeting scheduled bisa join 10 menit sebelum jadwal
  □ Meeting ended tidak bisa di-join
  
□ Meeting Features
  □ Audio/video berfungsi
  □ Screen sharing berfungsi
  □ Chat berfungsi
  □ Moderator bisa mute/kick participant
  □ Participant tidak bisa mute others
  
□ End Meeting
  □ Guru bisa end meeting
  □ Status update ke 'ended'
  □ Siswa auto disconnect
  
□ Delete Meeting
  □ Hanya meeting yang tidak active bisa dihapus
  □ Guru owner bisa delete meeting
```

### 2. Unit Test Example

```php
<?php
// tests/Feature/OnlineMeetingTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\OnlineMeeting;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OnlineMeetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_quick_start_meeting()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $classroom = Classroom::factory()->create();

        $response = $this->actingAs($guru)
            ->post(route('meetings.quick-start', [
                'serial' => 1,
                'classroom' => $classroom->id
            ]), [
                'title' => 'Test Meeting'
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('online_meetings', [
            'title' => 'Test Meeting',
            'user_id' => $guru->id,
            'status' => 'active'
        ]);
    }

    public function test_student_cannot_join_ended_meeting()
    {
        $student = User::factory()->create(['role' => 'siswa']);
        $meeting = OnlineMeeting::factory()->create([
            'status' => 'ended'
        ]);

        $response = $this->actingAs($student)
            ->get(route('meetings.join', [
                'serial' => 1,
                'meeting' => $meeting->id
            ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_meeting_status_updates_when_scheduled_meeting_joined()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $meeting = OnlineMeeting::factory()->create([
            'status' => 'scheduled',
            'scheduled_at' => now()->subMinutes(5),
            'user_id' => $guru->id
        ]);

        $response = $this->actingAs($guru)
            ->get(route('meetings.join', [
                'serial' => 1,
                'meeting' => $meeting->id
            ]));

        $meeting->refresh();
        $this->assertEquals('active', $meeting->status);
        $this->assertNotNull($meeting->started_at);
    }
}
```

Run tests:
```bash
php artisan test --filter OnlineMeetingTest
```

---

## 🚀 Deployment Checklist

### Production Environment

```bash
# 1. Set environment variables
JITSI_SERVER_URL=https://meet.yourdomain.com
JITSI_APP_ID=your_production_app_id
JITSI_SECRET_KEY=your_production_secret_key
JITSI_TOKEN_EXPIRATION=86400

# 2. Run migrations
php artisan migrate --force

# 3. Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Set permissions
chmod -R 755 storage bootstrap/cache
```

### Security Considerations

1. **JWT Secret**: Gunakan secret key yang kuat dan random
2. **HTTPS Only**: Jitsi harus di-serve melalui HTTPS
3. **CORS**: Set CORS policy dengan benar
4. **Rate Limiting**: Batasi request ke endpoint meeting
5. **Authorization**: Pastikan hanya user yang berhak bisa join meeting

---

## 📚 Resources

- [Jitsi Meet Documentation](https://jitsi.github.io/handbook/docs/intro)
- [Jitsi JWT Authentication](https://github.com/jitsi/lib-jitsi-meet/blob/master/doc/tokens.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Firebase PHP-JWT](https://github.com/firebase/php-jwt)

---

**Selamat mengembangkan fitur Kelas Online!** 🎉

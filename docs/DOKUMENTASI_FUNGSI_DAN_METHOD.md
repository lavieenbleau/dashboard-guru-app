# Dokumentasi Fungsi dan Method - DashboardGuru System

Dokumentasi ini menjelaskan implementasi MVC (Model-View-Controller) per halaman/fitur dari sistem DashboardGuru. Setiap bagian berisi potongan kode lengkap dari Route, Controller, Model, dan View untuk memudahkan pemahaman.

---

## Penjelasan Arsitektur MVC

Sistem DashboardGuru menggunakan pola arsitektur **MVC (Model-View-Controller)**:

```
┌─────────┐      ┌────────────┐      ┌───────┐      ┌──────┐
│ Browser │─────>│   Route    │─────>│ Ctrl  │─────>│ Model│
│ (User)  │      │ web.php    │      │ .php  │      │ .php │
└─────────┘      └────────────┘      └───────┘      └──────┘
     ▲                                    │              │
     │                                    ▼              │
     │                              ┌──────────┐        │
     └──────────────────────────────│   View   │◄───────┘
                                    │ .blade   │
                                    └──────────┘
```

### Komponen MVC:

1. **Route** (`routes/web.php`) - Memetakan URL ke Controller
2. **Controller** (`app/Http/Controllers/`) - Memproses logic bisnis
3. **Model** (`app/Models/`) - Mengelola data dan relasi database
4. **View** (`resources/views/`) - Template tampilan UI (Blade)

---

## Daftar Isi

### AUTENTIKASI

1. [Halaman Login](#1-halaman-login)
2. [Proses Login](#2-proses-login)
3. [Logout](#3-logout)

### DASHBOARD

4. [Dashboard Guru](#4-dashboard-guru)

### KELOLA KELAS

5. [Halaman Pilih Kelas](#5-halaman-pilih-kelas)
6. [Tambah Kelas Baru](#6-tambah-kelas-baru)
7. [Hapus Kelas](#7-hapus-kelas)
8. [Dashboard Kelas (Daftar Siswa)](#8-dashboard-kelas-daftar-siswa)
9. [Tambah Siswa](#9-tambah-siswa)
10. [Hapus Siswa](#10-hapus-siswa)
11. [Import Siswa dari CSV](#11-import-siswa-dari-csv)

### KELOLA MATERI

12. [Halaman Utama Materi](#12-halaman-utama-materi)
13. [Materi dari Admin](#13-materi-dari-admin)
14. [Bagikan Materi Admin ke Kelas](#14-bagikan-materi-admin-ke-kelas)
15. [Materi Custom (Buatan Guru)](#15-materi-custom-buatan-guru)

### KELOLA TUGAS

16. [Halaman Pilih Mata Pelajaran (Tugas)](#16-halaman-pilih-mata-pelajaran-tugas)
17. [Daftar Tugas per Mapel](#17-daftar-tugas-per-mapel)
18. [Form Tambah Tugas](#18-form-tambah-tugas)
19. [Simpan Tugas Baru](#19-simpan-tugas-baru)
20. [Detail Tugas](#20-detail-tugas)
21. [Edit Tugas](#21-edit-tugas)

### KELAS ONLINE

22. [Halaman Kelas Online](#22-halaman-kelas-online)
23. [Quick Start Meeting](#23-quick-start-meeting)
24. [Jadwal Meeting](#24-jadwal-meeting)
25. [Join Meeting](#25-join-meeting)

### KELOLA SOAL

26. [Halaman Pilih Kategori Soal](#26-halaman-pilih-kategori-soal)
27. [Daftar Soal per Kategori](#27-daftar-soal-per-kategori)
28. [Buat Soal Custom](#28-buat-soal-custom)
29. [Simpan Soal Custom](#29-simpan-soal-custom)

### PROFILE

30. [Edit Profile](#30-edit-profile)
31. [Update Profile](#31-update-profile)

---

## AUTENTIKASI

### 1. Halaman Login

**Deskripsi:** Menampilkan form login untuk guru/admin

#### 📍 ROUTE

```php
// File: routes/auth.php
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/Auth/AuthenticatedSessionController.php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }
}
```

#### 📦 MODEL

```php
// File: app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

#### 🎨 VIEW

```blade
{{-- File: resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="email" name="email"
                          :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember">
                <span class="ms-2 text-sm">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
```

#### 🔄 FLOW DATA

```
1. User akses URL: /login
2. Route mengarahkan ke AuthenticatedSessionController@create
3. Controller return view 'auth.login'
4. View ditampilkan dengan form login
```

---

### 2. Proses Login

**Deskripsi:** Memproses autentikasi dan validasi kredensial user

#### 📍 ROUTE

```php
// File: routes/auth.php
Route::middleware('guest')->group(function () {
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/Auth/AuthenticatedSessionController.php
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate and authenticate user
        $request->authenticate();

        // Regenerate session untuk security
        $request->session()->regenerate();

        // Redirect ke dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
```

#### 📝 REQUEST VALIDATION

```php
// File: app/Http/Requests/Auth/LoginRequest.php
namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Coba login menggunakan User Model
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
}
```

#### 📦 MODEL

```php
// Model User sudah dijelaskan di bagian sebelumnya
// Laravel menggunakan User Model untuk Auth::attempt()
```

#### 🔄 FLOW DATA

```
1. User submit form login (POST /login)
2. Route mengarahkan ke AuthenticatedSessionController@store
3. LoginRequest memvalidasi input (email & password)
4. LoginRequest->authenticate() menggunakan Auth::attempt()
5. Auth::attempt() query User Model di database
6. Jika cocok: regenerate session & redirect ke dashboard
7. Jika gagal: throw ValidationException
```

---

### 3. Logout

**Deskripsi:** Menghapus session dan logout user

#### 📍 ROUTE

```php
// File: routes/auth.php
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/Auth/AuthenticatedSessionController.php
class AuthenticatedSessionController extends Controller
{
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout dari guard 'web'
        Auth::guard('web')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

#### 🎨 VIEW (Button Logout)

```blade
{{-- File: resources/views/layouts/navigation.blade.php --}}
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-link">
        <i class='bx bx-power-off'></i> Logout
    </button>
</form>
```

#### 🔄 FLOW DATA

```
1. User klik button logout (POST /logout)
2. Route mengarahkan ke AuthenticatedSessionController@destroy
3. Controller logout user dan hapus session
4. Redirect ke halaman utama (/)
```

---

## DASHBOARD

### 4. Dashboard Guru

**Deskripsi:** Menampilkan daftar aplikasi/serial yang dimiliki guru

#### 📍 ROUTE

```php
// File: routes/web.php
use App\Http\Controllers\GuruDashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])
        ->name('dashboard');
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/GuruDashboardController.php
namespace App\Http\Controllers;

use App\Models\Serial;
use Illuminate\Support\Facades\Schema;

class GuruDashboardController extends Controller
{
    public function index()
    {
        // Check if serials table exists (development safety)
        if (!Schema::hasTable('serials')) {
            $serials = collect();
        } else {
            // Ambil serials milik user yang sedang login
            $serials = Serial::with(['product', 'classrooms'])
                        ->where('user_id', auth()->id())
                        ->get();
        }

        return view('guru.dashboard', compact('serials'));
    }
}
```

#### 📦 MODEL

```php
// File: app/Models/Serial.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serial extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'serial_number',
        'is_active',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Serial dimiliki oleh user (guru)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Serial mengacu ke produk tertentu
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Serial memiliki banyak classrooms
     */
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
```

```php
// File: app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'type'];

    public function serials()
    {
        return $this->hasMany(Serial::class);
    }
}
```

#### 🎨 VIEW

```blade
{{-- File: resources/views/guru/dashboard.blade.php --}}
@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dashboard /</span> Aplikasi Saya
    </h4>

    <div class="row">
        @forelse($serials as $serial)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            {{ $serial->product->name ?? 'Produk Tanpa Nama' }}
                        </h5>

                        <p class="mb-1">
                            <i class='bx bx-group'></i>
                            {{ $serial->classrooms->count() }} Kelas
                        </p>

                        <p class="text-muted small">
                            <i class='bx bx-calendar'></i>
                            Aktif sejak {{ $serial->created_at->format('d M Y') }}
                        </p>

                        <a href="{{ route('guru.aplikasi', $serial->id) }}"
                           class="btn btn-primary w-100 mt-2">
                            <i class='bx bx-right-arrow-alt'></i>
                            Buka Aplikasi
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class='bx bx-info-circle'></i>
                    Belum ada aplikasi yang aktif untuk akun Anda.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
```

#### 🔄 FLOW DATA

```
1. User akses URL: /dashboard (setelah login)
2. Middleware 'auth' cek apakah user sudah login
3. Route mengarahkan ke GuruDashboardController@index
4. Controller query Serial Model:
   - WHERE user_id = auth()->id()
   - WITH product & classrooms (eager loading)
5. Data $serials dikirim ke view
6. View loop data dan tampilkan card untuk setiap serial
7. User bisa klik "Buka Aplikasi" untuk masuk ke aplikasi
```

---

## KELOLA KELAS

### 5. Halaman Pilih Kelas

**Deskripsi:** Menampilkan daftar kelas dalam satu serial/aplikasi

#### 📍 ROUTE

```php
// File: routes/web.php
use App\Http\Controllers\Guru\KelasController;

Route::middleware(['auth'])->prefix('guru')->group(function () {
    Route::get('/aplikasi/{serial}/kelas/pilih', [KelasController::class, 'pilihKelas'])
        ->name('guru.kelas.pilih');
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/Guru/KelasController.php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Serial;
use App\Models\Classroom;

class KelasController extends Controller
{
    /**
     * Menampilkan halaman pilih kelas
     */
    public function pilihKelas($serial)
    {
        // Ambil serial berdasarkan ID
        $serial = Serial::findOrFail($serial);

        // Ambil semua classrooms dengan count students
        $classrooms = Classroom::where('serial_id', $serial->id)
            ->withCount('students')  // Hitung jumlah siswa
            ->get();

        return view('guru.kelas.pilih', compact('serial', 'classrooms'));
    }
}
```

#### 📦 MODEL

```php
// File: app/Models/Classroom.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'serial_id',
        'name',
        'grade',
        'code',
    ];

    /**
     * Get serial yang memiliki classroom
     */
    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    /**
     * Get students dalam classroom
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
```

#### 🎨 VIEW

```blade
{{-- File: resources/views/guru/kelas/pilih.blade.php --}}
@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-group text-info me-2'></i>Kelola Kelas
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahKelas">
            <i class='bx bx-plus me-1'></i>Tambah Kelas
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($classrooms as $classroom)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-1">{{ $classroom->name }}</h5>
                        @if($classroom->grade)
                            <small class="text-muted">Tingkat {{ $classroom->grade }}</small>
                        @endif

                        <div class="mt-2">
                            <span class="badge bg-label-primary">
                                <i class='bx bx-user me-1'></i>
                                {{ $classroom->students_count }} Siswa
                            </span>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('guru.kelas.dashboard', [$serial->id, $classroom->id]) }}"
                               class="btn btn-sm btn-info flex-grow-1">
                                <i class='bx bx-user-circle me-1'></i>Kelola Siswa
                            </a>
                            <form method="POST"
                                  action="{{ route('guru.kelas.destroy', [$serial->id, $classroom->id]) }}"
                                  onsubmit="return confirm('Hapus kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class='bx bx-group display-1 text-muted'></i>
                        <h5 class="mt-3">Belum Ada Kelas</h5>
                        <p class="text-muted">Klik tombol "Tambah Kelas" untuk memulai</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('guru.kelas.store', $serial->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Contoh: Kelas 4A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat / Grade</label>
                        <input type="text" name="grade" class="form-control"
                               placeholder="Contoh: 4">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

#### 🔄 FLOW DATA

```
1. User akses URL: /guru/aplikasi/{serial}/kelas/pilih
2. Route mengarahkan ke KelasController@pilihKelas
3. Controller:
   - Query Serial::findOrFail($serial)
   - Query Classroom WHERE serial_id dengan withCount('students')
4. Data dikirim ke view: $serial, $classrooms
5. View menampilkan:
   - Loop classrooms dengan card
   - Badge jumlah siswa (dari students_count)
   - Button "Kelola Siswa" & "Hapus"
   - Modal form tambah kelas
```

---

### 6. Tambah Kelas Baru

**Deskripsi:** Menyimpan kelas baru ke database

#### 📍 ROUTE

```php
// File: routes/web.php
Route::middleware(['auth'])->prefix('guru')->group(function () {
    Route::post('/aplikasi/{serial}/kelas', [KelasController::class, 'store'])
        ->name('guru.kelas.store');
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/Guru/KelasController.php
class KelasController extends Controller
{
    /**
     * Simpan kelas baru
     */
    public function store(Request $request, $serial)
    {
        $serial = Serial::findOrFail($serial);

        // Validasi input
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
        ]);

        // Generate unique classroom code
        $code = $this->generateClassroomCode();

        // Buat classroom baru
        $classroom = new Classroom();
        $classroom->serial_id = $serial->id;
        $classroom->name = $data['name'];
        $classroom->grade = $data['grade'] ?? null;
        $classroom->code = $code;
        $classroom->save();

        return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Generate unique classroom code
     */
    private function generateClassroomCode()
    {
        do {
            // Generate random code: format CLS-XXXXXX
            $code = 'CLS-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (Classroom::where('code', $code)->exists());

        return $code;
    }
}
```

#### 📦 MODEL

```php
// Model Classroom sudah dijelaskan sebelumnya
// Eloquent otomatis handle INSERT ke database saat ->save()
```

#### 🔄 FLOW DATA

```
1. User submit form tambah kelas (POST /guru/aplikasi/{serial}/kelas)
2. Route mengarahkan ke KelasController@store
3. Controller:
   - Validasi input (name required, grade nullable)
   - Generate kode kelas unik (CLS-XXXXXX)
   - Buat instance Classroom baru
   - Set properties & save ke database
4. Redirect ke halaman pilih kelas dengan flash message
5. View menampilkan alert success
```

---

### 7. Hapus Kelas

**Deskripsi:** Menghapus kelas dari database

#### 📍 ROUTE

```php
// File: routes/web.php
Route::middleware(['auth'])->prefix('guru')->group(function () {
    Route::delete('/aplikasi/{serial}/kelas/{classroom}', [KelasController::class, 'destroy'])
        ->name('guru.kelas.destroy');
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/Guru/KelasController.php
class KelasController extends Controller
{
    /**
     * Hapus kelas
     */
    public function destroy($serial, $classroom)
    {
        $serial = Serial::findOrFail($serial);

        // Cari classroom yang sesuai dengan serial
        $c = Classroom::where('serial_id', $serial->id)
            ->where('id', $classroom)
            ->firstOrFail();

        // Hapus dari database
        $c->delete();

        return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
```

#### 📦 MODEL

```php
// Model Classroom
// Method ->delete() adalah Eloquent method untuk soft/hard delete
```

#### 🎨 VIEW (Button Delete)

```blade
{{-- Sudah ada di view pilih kelas --}}
<form method="POST"
      action="{{ route('guru.kelas.destroy', [$serial->id, $classroom->id]) }}"
      onsubmit="return confirm('Hapus kelas ini?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">
        <i class='bx bx-trash'></i>
    </button>
</form>
```

#### 🔄 FLOW DATA

```
1. User klik button delete & confirm (DELETE /guru/aplikasi/{serial}/kelas/{classroom})
2. Route mengarahkan ke KelasController@destroy
3. Controller:
   - Query classroom WHERE serial_id AND id
   - Call ->delete() untuk hapus dari database
4. Redirect ke halaman pilih kelas dengan flash message
```

---

### 8. Dashboard Kelas (Daftar Siswa)

**Deskripsi:** Menampilkan dashboard kelas dengan daftar siswa yang terdaftar

**Fitur yang diimplementasikan:**

- Melihat daftar siswa dalam kelas
- Informasi lengkap siswa (nama, NIS, username, password)
- Tambah siswa baru (manual)
- Import siswa dari CSV
- Hapus siswa
- Copy password untuk dibagikan ke siswa

#### 📍 ROUTE

```php
// File: routes/web.php
Route::middleware(['auth'])->prefix('guru')->group(function () {
    Route::get('/aplikasi/{serial}/kelas/{classroom}/dashboard',
        [KelasController::class, 'dashboard'])
        ->name('guru.kelas.dashboard');
});
```

#### 🎮 CONTROLLER

```php
// File: app/Http/Controllers/Guru/KelasController.php
namespace App\Http\Controllers\Guru;

use App\Models\Serial;
use App\Models\Classroom;
use App\Models\Student;

class KelasController extends Controller
{
    /**
     * Dashboard kelas (menampilkan siswa)
     */
    public function dashboard($serial, $classroom)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroom);

        // Get students in this classroom, sorted by name
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('guru.kelas.dashboard', compact('serial', 'classroom', 'students'));
    }
}
```

#### 📦 MODEL

```php
// File: app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'serial_id',
        'user_id',
        'classroom_id',
        'name',
        'username',
        'password',
        'password_text',  // Plain text password untuk ditampilkan ke guru
        'nis',
        'email',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Relasi: Student belongs to Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relasi: Student belongs to Serial
     */
    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    /**
     * Relasi: Student belongs to User (guru yang membuat)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

#### 🔄 FLOW DATA

```
1. User akses URL: /guru/aplikasi/{serial}/kelas/{classroom}/dashboard
2. Route mengarahkan ke KelasController@dashboard
3. Controller query Student WHERE classroom_id, ORDER BY name
4. View menampilkan tabel siswa dengan username & password
```

---

## REFERENSI MODEL

### Model User

{
return $this->hasMany(Serial::class);
}

/\*\*

- Get students yang dibuat oleh user
  \*/
  public function students()
  {
  return $this->hasMany(Student::class);
  }

/\*\*

- Get online meetings yang dibuat oleh user
  \*/
  public function onlineMeetings()
  {
  return $this->hasMany(OnlineMeeting::class);
  }

/\*\*

- Get posts (materi/tugas) yang dibuat user
  \*/
  public function posts()
  {
  return $this->hasMany(Post::class);
  }

````

---

### 2. Serial Model
**Lokasi:** `app/Models/Serial.php`

**Deskripsi:** Model untuk tabel serials (lisensi aplikasi)

**Properties:**

```php
protected $fillable = [
    'user_id',
    'product_id',
    'serial_number',
    'is_active',
    'expired_at',
];

protected $casts = [
    'expired_at' => 'datetime',
    'is_active' => 'boolean',
];
````

**Relationships:**

```php
/**
 * Serial dimiliki oleh user (guru)
 */
public function user()
{
    return $this->belongsTo(User::class);
}

/**
 * Serial mengacu ke produk tertentu
 */
public function product()
{
    return $this->belongsTo(Product::class);
}

/**
 * Serial memiliki banyak classrooms
 */
public function classrooms()
{
    return $this->hasMany(Classroom::class);
}

/**
 * Serial memiliki banyak students
 */
public function students()
{
    return $this->hasMany(Student::class);
}
```

---

### 3. Classroom Model

**Lokasi:** `app/Models/Classroom.php`

**Deskripsi:** Model untuk tabel classrooms (kelas)

**Properties:**

```php
protected $fillable = [
    'serial_id',
    'name',
    'grade',
    'code',
];
```

**Relationships:**

```php
/**
 * Get serial yang memiliki classroom
 */
public function serial()
{
    return $this->belongsTo(Serial::class);
}

/**
 * Get students dalam classroom
 */
public function students()
{
    return $this->hasMany(Student::class);
}

/**
 * Get lessons yang dibagikan ke classroom (many-to-many)
 */
public function lessons()
{
    return $this->belongsToMany(Lesson::class, 'lesson_classroom');
}

/**
 * Get exercises yang dibagikan ke classroom (many-to-many)
 */
public function exercises()
{
    return $this->belongsToMany(Exercise::class, 'exercise_classroom');
}

/**
 * Get online meetings di classroom ini
 */
public function onlineMeetings()
{
    return $this->hasMany(OnlineMeeting::class);
}
```

---

### 4. Student Model

**Lokasi:** `app/Models/Student.php`

**Deskripsi:** Model untuk tabel students (siswa)

**Properties:**

```php
protected $fillable = [
    'serial_id',
    'user_id',
    'classroom_id',
    'name',
    'username',
    'password',
    'password_text',
    'nis',
    'email',
    'phone',
];

protected $hidden = [
    'password',
];

protected $casts = [
    'password' => 'hashed',
];
```

**Relationships:**

```php
/**
 * Get serial yang memiliki student
 */
public function serial()
{
    return $this->belongsTo(Serial::class);
}

/**
 * Get classroom tempat student belajar
 */
public function classroom()
{
    return $this->belongsTo(Classroom::class);
}

/**
 * Get user (guru) yang membuat student
 */
public function user()
{
    return $this->belongsTo(User::class);
}
```

---

### 5. Lesson Model

**Lokasi:** `app/Models/Lesson.php`

**Deskripsi:** Model untuk tabel lessons (materi pembelajaran)

**Constants:**

```php
const CATEGORY_MATERI = 1; // Materi pembelajaran
const CATEGORY_SOAL = 2;   // Bank soal
```

**Properties:**

```php
protected $fillable = [
    'mapel_id',
    'theme_id',
    'subtheme_id',
    'name',
    'description',
    'file',
    'link',
    'category',
    'grade',
    'semester',
];

protected $casts = [
    'category' => 'integer',
    'grade' => 'string',
    'semester' => 'integer',
];
```

**Relationships:**

```php
/**
 * Lesson belongs to Mapel
 */
public function mapel()
{
    return $this->belongsTo(Mapel::class);
}

/**
 * Lesson belongs to Theme
 */
public function theme()
{
    return $this->belongsTo(Theme::class);
}

/**
 * Lesson belongs to Subtheme
 */
public function subtheme()
{
    return $this->belongsTo(Subtheme::class);
}

/**
 * Lesson has many exercises
 */
public function exercises()
{
    return $this->hasMany(Exercise::class);
}

/**
 * Lesson has many items (materi items)
 */
public function lessonItems()
{
    return $this->hasMany(LessonItem::class);
}

/**
 * Lesson dibagikan ke many classrooms
 */
public function classrooms()
{
    return $this->belongsToMany(Classroom::class, 'lesson_classroom');
}
```

---

### 6. Exercise Model

**Lokasi:** `app/Models/Exercise.php`

**Deskripsi:** Model untuk tabel exercises (soal/latihan)

**Properties:**

```php
protected $fillable = [
    'lesson_id',
    'serial_id',
    'exercise_type_id',
    'title',
    'description',
    'is_admin',
    'shared_to_classes',
];

protected $casts = [
    'is_admin' => 'boolean',
    'shared_to_classes' => 'array',
];
```

**Relationships:**

```php
/**
 * Exercise belongs to Lesson
 */
public function lesson()
{
    return $this->belongsTo(Lesson::class);
}

/**
 * Exercise belongs to ExerciseType
 */
public function exerciseType()
{
    return $this->belongsTo(ExerciseType::class);
}

/**
 * Exercise belongs to Serial (untuk custom exercise)
 */
public function serial()
{
    return $this->belongsTo(Serial::class);
}

/**
 * Exercise has many items (soal)
 */
public function exerciseItems()
{
    return $this->hasMany(ExerciseItem::class);
}

/**
 * Exercise dibagikan ke many classrooms
 */
public function classrooms()
{
    return $this->belongsToMany(Classroom::class, 'exercise_classroom');
}
```

**Methods:**

```php
/**
 * Scope untuk soal admin
 */
public function scopeAdmin($query)
{
    return $query->where('is_admin', 1)->whereNull('serial_id');
}

/**
 * Scope untuk soal custom guru
 */
public function scopeCustom($query, $serialId)
{
    return $query->where('is_admin', 0)->where('serial_id', $serialId);
}
```

---

### 7. Post Model

**Lokasi:** `app/Models/Post.php`

**Deskripsi:** Model untuk tabel posts (materi custom & tugas)

**Properties:**

```php
protected $fillable = [
    'serial_id',
    'user_id',
    'mapel_id',
    'title',
    'description',
    'slug',
    'link',
    'file',
    'attachment',
    'category',
    'shared_to_classes',
    'deadline',
    'is_task',
];

protected $casts = [
    'category' => 'array',
    'shared_to_classes' => 'array',
    'deadline' => 'datetime',
    'is_task' => 'boolean',
];
```

**Relationships:**

```php
/**
 * Post belongs to Serial
 */
public function serial()
{
    return $this->belongsTo(Serial::class);
}

/**
 * Post belongs to User (guru)
 */
public function user()
{
    return $this->belongsTo(User::class);
}

/**
 * Post belongs to Mapel
 */
public function mapel()
{
    return $this->belongsTo(Mapel::class);
}
```

**Methods:**

```php
/**
 * Scope untuk materi (bukan tugas)
 */
public function scopeMateri($query)
{
    return $query->where('is_task', 0);
}

/**
 * Scope untuk tugas
 */
public function scopeTugas($query)
{
    return $query->where('is_task', 1);
}

/**
 * Check apakah deadline sudah lewat
 */
public function isOverdue()
{
    return $this->deadline && $this->deadline->isPast();
}
```

---

### 8. OnlineMeeting Model

**Lokasi:** `app/Models/OnlineMeeting.php`

**Deskripsi:** Model untuk tabel online_meetings (kelas online)

**Properties:**

```php
protected $fillable = [
    'serial_id',
    'user_id',
    'classroom_id',
    'mapel_id',
    'title',
    'description',
    'meeting_code',
    'meeting_link',
    'platform',
    'start_time',
    'end_time',
    'status',
    'room_id',
    'is_internal',
];

protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'is_internal' => 'boolean',
];
```

**Relationships:**

```php
/**
 * Meeting belongs to Serial
 */
public function serial()
{
    return $this->belongsTo(Serial::class);
}

/**
 * Meeting belongs to User (guru yang membuat)
 */
public function user()
{
    return $this->belongsTo(User::class);
}

/**
 * Meeting belongs to Classroom
 */
public function classroom()
{
    return $this->belongsTo(Classroom::class);
}

/**
 * Meeting belongs to Mapel
 */
public function mapel()
{
    return $this->belongsTo(Mapel::class);
}
```

**Static Methods:**

```php
/**
 * Generate unique meeting code
 */
public static function generateMeetingCode()
{
    do {
        $code = 'MTG-' . strtoupper(\Str::random(8));
    } while (self::where('meeting_code', $code)->exists());

    return $code;
}
```

**Instance Methods:**

```php
/**
 * Check apakah meeting sedang aktif
 */
public function isActive()
{
    $now = now();
    return $now->between($this->start_time, $this->end_time);
}

/**
 * Check apakah meeting sudah dimulai
 */
public function hasStarted()
{
    return now()->greaterThan($this->start_time);
}

/**
 * Check apakah meeting sudah berakhir
 */
public function hasEnded()
{
    return now()->greaterThan($this->end_time);
}
```

---

### 9. Mapel Model

**Lokasi:** `app/Models/Mapel.php`

**Deskripsi:** Model untuk tabel mapels (mata pelajaran)

**Properties:**

```php
protected $fillable = [
    'name',
    'code',
    'description',
];
```

**Relationships:**

```php
/**
 * Mapel has many lessons
 */
public function lessons()
{
    return $this->hasMany(Lesson::class);
}

/**
 * Mapel has many posts
 */
public function posts()
{
    return $this->hasMany(Post::class);
}

/**
 * Mapel has many online meetings
 */
public function onlineMeetings()
{
    return $this->hasMany(OnlineMeeting::class);
}
```

---

## CONTROLLERS

### AUTH CONTROLLERS

#### 1. AuthenticatedSessionController.php

**Lokasi:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

**Deskripsi:** Menangani proses login dan logout pengguna

**Methods:**

```php
/**
 * Display the login view.
 */
public function create(): View
{
    return view('auth.login');
}
```

- **Fungsi:** Menampilkan halaman login
- **Return:** View login
- **Route:** GET /login

```php
/**
 * Handle an incoming authentication request.
 */
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();
    return redirect()->intended(route('dashboard', absolute: false));
}
```

- **Fungsi:** Memproses login pengguna
- **Parameter:** LoginRequest (berisi username/email dan password)
- **Return:** Redirect ke dashboard
- **Route:** POST /login

```php
/**
 * Destroy an authenticated session.
 */
public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
}
```

- **Fungsi:** Logout pengguna
- **Return:** Redirect ke halaman utama
- **Route:** POST /logout

---

### GURU CONTROLLERS

#### 2. GuruDashboardController.php

**Lokasi:** `app/Http/Controllers/GuruDashboardController.php`

**Deskripsi:** Menangani dashboard guru

**Methods:**

```php
public function index()
{
    // If the serials table doesn't exist (development state), return an empty collection
    if (! Schema::hasTable('serials')) {
        $serials = collect();
    } else {
        $serials = Serial::with(['product','classrooms'])
                    ->where('user_id', auth()->id())
                    ->get();
    }

    return view('guru.dashboard', compact('serials'));
}
```

- **Fungsi:** Menampilkan dashboard guru dengan daftar serial/lisensi yang dimiliki
- **Return:** View dashboard dengan data serials
- **Route:** GET /dashboard

---

#### 3. KelasController.php

**Lokasi:** `app/Http/Controllers/Guru/KelasController.php`

**Deskripsi:** Mengelola kelas dan siswa

**Methods:**

```php
/**
 * Menampilkan halaman pilih kelas
 */
public function pilihKelas($serial)
{
    $serial = Serial::findOrFail($serial);
    $classrooms = Classroom::where('serial_id', $serial->id)
        ->withCount('students')
        ->get();

    return view('guru.kelas.pilih', compact('serial', 'classrooms'));
}
```

- **Fungsi:** Menampilkan daftar kelas berdasarkan serial
- **Parameter:** $serial (ID serial)
- **Return:** View dengan daftar kelas
- **Route:** GET /aplikasi/{serial}/kelas/pilih

```php
/**
 * Simpan kelas baru
 */
public function store(Request $request, $serial)
{
    $serial = Serial::findOrFail($serial);

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'grade' => 'nullable|string|max:50',
    ]);

    // Generate unique classroom code
    $code = $this->generateClassroomCode();

    $classroom = new Classroom();
    $classroom->serial_id = $serial->id;
    $classroom->name = $data['name'];
    $classroom->grade = $data['grade'] ?? null;
    $classroom->code = $code;
    $classroom->save();

    return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])
        ->with('success', 'Kelas berhasil ditambahkan.');
}
```

- **Fungsi:** Menambahkan kelas baru
- **Parameter:** Request (name, grade), $serial
- **Return:** Redirect ke halaman pilih kelas
- **Route:** POST /aplikasi/{serial}/kelas

```php
/**
 * Generate unique classroom code
 */
private function generateClassroomCode()
{
    do {
        // Generate random code: format CLS-XXXXXX (CLS + 6 random chars)
        $code = 'CLS-' . strtoupper(\Illuminate\Support\Str::random(6));
    } while (Classroom::where('code', $code)->exists());

    return $code;
}
```

- **Fungsi:** Generate kode kelas unik dengan format CLS-XXXXXX
- **Return:** String kode kelas
- **Visibility:** Private

```php
/**
 * Hapus kelas
 */
public function destroy($serial, $classroom)
{
    $serial = Serial::findOrFail($serial);
    $c = Classroom::where('serial_id', $serial->id)
        ->where('id', $classroom)
        ->firstOrFail();
    $c->delete();

    return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])
        ->with('success', 'Kelas berhasil dihapus.');
}
```

- **Fungsi:** Menghapus kelas
- **Parameter:** $serial, $classroom (ID kelas)
- **Return:** Redirect ke halaman pilih kelas
- **Route:** DELETE /aplikasi/{serial}/kelas/{classroom}

```php
/**
 * Dashboard kelas (menampilkan siswa)
 */
public function dashboard($serial, $classroom)
{
    $serial = Serial::findOrFail($serial);
    $classroom = Classroom::findOrFail($classroom);

    // Get students in this classroom
    $students = \App\Models\Student::where('classroom_id', $classroom->id)
        ->orderBy('name', 'asc')
        ->get();

    return view('guru.kelas.dashboard', compact('serial', 'classroom', 'students'));
}
```

- **Fungsi:** Menampilkan dashboard kelas dengan daftar siswa
- **Parameter:** $serial, $classroom
- **Return:** View dashboard kelas
- **Route:** GET /aplikasi/{serial}/kelas/{classroom}/dashboard

```php
/**
 * Simpan siswa baru
 */
public function storeStudent(Request $request, $serial, $classroom)
{
    $request->validate([
        'name' => 'required|string|max:200',
        'nis' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:100',
        'phone' => 'nullable|string|max:20',
    ]);

    $serial = Serial::findOrFail($serial);
    $classroom = Classroom::findOrFail($classroom);

    // Generate username from NIS or name
    $username = $request->nis ?? strtolower(str_replace(' ', '', $request->name));

    // Default password
    $defaultPassword = '12345678';

    \App\Models\Student::create([
        'serial_id' => $serial->id,
        'user_id' => auth()->id(),
        'classroom_id' => $classroom->id,
        'name' => $request->name,
        'username' => $username,
        'password' => bcrypt($defaultPassword),
        'password_text' => $defaultPassword,
        'nis' => $request->nis,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
        ->with('success', 'Siswa berhasil ditambahkan!');
}
```

- **Fungsi:** Menambahkan siswa baru ke kelas
- **Parameter:** Request (name, nis, email, phone), $serial, $classroom
- **Return:** Redirect ke dashboard kelas
- **Route:** POST /aplikasi/{serial}/kelas/{classroom}/siswa

```php
/**
 * Hapus siswa
 */
public function destroyStudent($serial, $classroom, $student)
{
    $student = \App\Models\Student::findOrFail($student);
    $student->delete();

    return redirect()->route('guru.kelas.dashboard', [$serial, $classroom])
        ->with('success', 'Siswa berhasil dihapus!');
}
```

- **Fungsi:** Menghapus siswa dari kelas
- **Parameter:** $serial, $classroom, $student (ID siswa)
- **Return:** Redirect ke dashboard kelas
- **Route:** DELETE /aplikasi/{serial}/kelas/{classroom}/siswa/{student}

```php
/**
 * Import siswa dari CSV
 */
public function importStudents(Request $request, $serial, $classroom)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt|max:2048',
    ]);

    $serial = Serial::findOrFail($serial);
    $classroom = Classroom::findOrFail($classroom);

    $file = $request->file('csv_file');
    $path = $file->getRealPath();

    // Read CSV file
    $csv = array_map('str_getcsv', file($path));

    // Get header row
    $header = array_shift($csv);

    // Normalize header (trim and lowercase)
    $header = array_map(function($h) {
        return strtolower(trim($h));
    }, $header);

    // Process CSV data...
    // (kode dilanjutkan untuk parsing CSV)
}
```

- **Fungsi:** Import data siswa dari file CSV
- **Parameter:** Request (csv_file), $serial, $classroom
- **Return:** Redirect ke dashboard kelas
- **Route:** POST /aplikasi/{serial}/kelas/{classroom}/siswa/import

---

#### 4. MateriController.php

**Lokasi:** `app/Http/Controllers/Guru/MateriController.php`

**Deskripsi:** Mengelola materi pembelajaran

**Methods:**

```php
/**
 * Halaman utama materi
 */
public function index($serial)
{
    $serial = Serial::findOrFail($serial);
    return view('guru.materi.index', compact('serial'));
}
```

- **Fungsi:** Menampilkan halaman utama materi
- **Parameter:** $serial
- **Return:** View index materi
- **Route:** GET /aplikasi/{serial}/materi

```php
/**
 * Materi dari Admin
 */
public function admin($serial)
{
    $serial = Serial::findOrFail($serial);

    // Get all mapels that have admin materials (category = 1)
    $mapels = Mapel::whereHas('lessons', function($query) {
        $query->where('category', Lesson::CATEGORY_MATERI);
    })->get();

    return view('guru.materi.admin', compact('serial', 'mapels'));
}
```

- **Fungsi:** Menampilkan daftar materi dari admin
- **Parameter:** $serial
- **Return:** View dengan daftar mapel yang memiliki materi admin
- **Route:** GET /aplikasi/{serial}/materi/admin

```php
/**
 * Tampilkan materi admin berdasarkan mapel
 */
public function adminLessons($serial, $mapel)
{
    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);

    // Get all admin lessons for this mapel with classrooms relationship
    $lessons = Lesson::where('mapel_id', $mapel->id)
        ->where('category', Lesson::CATEGORY_MATERI)
        ->with('classrooms')
        ->get();

    return view('guru.materi.admin-lessons', compact('serial', 'mapel', 'lessons'));
}
```

- **Fungsi:** Menampilkan daftar lesson/materi admin berdasarkan mata pelajaran
- **Parameter:** $serial, $mapel (ID mapel)
- **Return:** View dengan daftar lessons
- **Route:** GET /aplikasi/{serial}/materi/admin/{mapel}

```php
/**
 * Bagikan materi admin ke kelas
 */
public function shareAdminLesson(Request $request, $serial, $lessonId)
{
    $serial = Serial::findOrFail($serial);
    $lesson = Lesson::findOrFail($lessonId);

    // Sync classrooms (remove old, add new)
    $classrooms = $request->classrooms ?? [];
    $lesson->classrooms()->sync($classrooms);

    // If share as task is enabled, create posts for each classroom
    if ($request->has('as_task') && $request->as_task == 1 && count($classrooms) > 0) {
        $deadline = $request->deadline ? \Carbon\Carbon::parse($request->deadline) : null;

        foreach ($classrooms as $classroomId) {
            // Check if post already exists for this lesson and classroom
            $existingPost = Post::where('serial_id', $serial->id)
                ->where('mapel_id', $lesson->mapel_id)
                ->where('title', $lesson->name)
                ->whereJsonContains('shared_to_classes', (string)$classroomId)
                ->first();

            if (!$existingPost) {
                // Create new post as task
                Post::create([
                    'serial_id' => $serial->id,
                    'user_id' => auth()->id(),
                    'mapel_id' => $lesson->mapel_id,
                    'title' => $lesson->name,
                    'description' => $lesson->description ?? 'Tugas dari materi: ' . $lesson->name,
                    'slug' => \Str::slug($lesson->name) . '-' . time(),
                    'category' => json_encode(['lesson_id' => $lesson->id]),
                    'shared_to_classes' => json_encode([$classroomId]),
                    'deadline' => $deadline,
                    'is_task' => 1,
                    'file' => $lesson->file,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return back()->with('success', 'Materi berhasil dibagikan sebagai tugas ke ' . count($classrooms) . ' kelas!');
    }

    return back()->with('success', 'Materi berhasil dibagikan ke ' . count($classrooms) . ' kelas!');
}
```

- **Fungsi:** Membagikan materi admin ke kelas tertentu, bisa sebagai tugas atau materi biasa
- **Parameter:** Request (classrooms, as_task, deadline), $serial, $lessonId
- **Return:** Redirect back dengan pesan sukses
- **Route:** POST /aplikasi/{serial}/materi/admin/share/{lesson}

```php
/**
 * Bagikan materi custom ke kelas
 */
public function shareCustomMateri(Request $request, $serial, $postId)
{
    $serial = Serial::findOrFail($serial);
    $post = Post::findOrFail($postId);

    // Update shared classrooms
    $classrooms = $request->classrooms ?? [];
    $post->shared_to_classes = json_encode($classrooms);

    // Update task settings
    $post->is_task = $request->has('as_task') && $request->as_task == 1 ? 1 : 0;
    $post->deadline = $request->deadline ? \Carbon\Carbon::parse($request->deadline) : null;

    $post->save();

    $message = $post->is_task
        ? 'Materi berhasil dibagikan sebagai tugas ke ' . count($classrooms) . ' kelas!'
        : 'Materi berhasil dibagikan ke ' . count($classrooms) . ' kelas!';

    return back()->with('success', $message);
}
```

- **Fungsi:** Membagikan materi custom/buatan guru ke kelas
- **Parameter:** Request (classrooms, as_task, deadline), $serial, $postId
- **Return:** Redirect back dengan pesan sukses
- **Route:** POST /aplikasi/{serial}/materi/custom/share/{post}

```php
/**
 * Materi Tambahan (Custom)
 */
public function custom($serial)
{
    $serial = Serial::findOrFail($serial);

    // Get all mapels
    $mapels = Mapel::all();

    return view('guru.materi.custom', compact('serial', 'mapels'));
}
```

- **Fungsi:** Menampilkan halaman materi custom/tambahan
- **Parameter:** $serial
- **Return:** View materi custom
- **Route:** GET /aplikasi/{serial}/materi/custom

```php
/**
 * List materi by mapel
 */
public function listByMapel($serial, $mapel)
{
    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);

    // Get all posts (materi) for this mapel and serial
    $materis = Post::where('serial_id', $serial->id)
        ->where('mapel_id', $mapel->id)
        ->where('is_task', 0)
        ->latest()
        ->get();

    return view('guru.materi.list-simple', compact('serial', 'mapel', 'materis'));
}
```

- **Fungsi:** Menampilkan daftar materi berdasarkan mata pelajaran
- **Parameter:** $serial, $mapel
- **Return:** View dengan daftar materi
- **Route:** GET /aplikasi/{serial}/materi/{mapel}

---

#### 5. TugasController.php

**Lokasi:** `app/Http/Controllers/Guru/TugasController.php`

**Deskripsi:** Mengelola tugas siswa

**Methods:**

```php
/**
 * Halaman utama tugas
 */
public function index($serial)
{
    $serial = Serial::findOrFail($serial);

    // Get all mapels
    $mapels = Mapel::all();

    return view('guru.tugas.index', compact('serial', 'mapels'));
}
```

- **Fungsi:** Menampilkan halaman utama tugas
- **Parameter:** $serial
- **Return:** View index tugas dengan daftar mapel
- **Route:** GET /aplikasi/{serial}/tugas

```php
/**
 * List tugas berdasarkan mapel
 */
public function listByMapel($serial, $mapel)
{
    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);

    // Get all posts (tugas) for this mapel and serial
    $tugas = Post::where('serial_id', $serial->id)
        ->where('mapel_id', $mapel->id)
        ->where('is_task', 1)
        ->latest()
        ->get();

    return view('guru.tugas.list', compact('serial', 'mapel', 'tugas'));
}
```

- **Fungsi:** Menampilkan daftar tugas berdasarkan mata pelajaran
- **Parameter:** $serial, $mapel
- **Return:** View dengan daftar tugas
- **Route:** GET /aplikasi/{serial}/tugas/{mapel}

```php
/**
 * Halaman buat tugas baru
 */
public function create($serial, $mapel)
{
    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);
    $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();

    return view('guru.tugas.create', compact('serial', 'mapel', 'classrooms'));
}
```

- **Fungsi:** Menampilkan form membuat tugas baru
- **Parameter:** $serial, $mapel
- **Return:** View create tugas
- **Route:** GET /aplikasi/{serial}/tugas/{mapel}/create

```php
/**
 * Simpan tugas baru
 */
public function store(Request $request, $serial, $mapel)
{
    $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'link' => 'nullable|url',
        'deadline' => 'nullable|date',
        'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png',
        'classrooms' => 'nullable|array',
        'classrooms.*' => 'exists:classrooms,id',
    ]);

    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);

    // Handle file upload
    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $attachmentPath = $file->storeAs('tugas', $filename, 'public');
    }

    // Create tugas post
    Post::create([
        'serial_id' => $serial->id,
        'user_id' => auth()->id(),
        'mapel_id' => $mapel->id,
        'title' => $request->title,
        'description' => $request->description,
        'slug' => Str::slug($request->title) . '-' . time(),
        'link' => $request->link,
        'attachment' => $attachmentPath,
        'deadline' => $request->deadline,
        'category' => null,
        'shared_to_classes' => $request->classrooms,
        'is_task' => 1,
    ]);

    return redirect()->route('guru.tugas.mapel', [$serial->id, $mapel->id])
        ->with('success', 'Tugas berhasil ditambahkan!');
}
```

- **Fungsi:** Menyimpan tugas baru ke database
- **Parameter:** Request (title, description, link, deadline, attachment, classrooms), $serial, $mapel
- **Return:** Redirect ke list tugas
- **Route:** POST /aplikasi/{serial}/tugas/{mapel}

```php
/**
 * Tampilkan detail tugas
 */
public function show($serial, $mapel, $id)
{
    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);
    $task = Post::findOrFail($id);

    return view('guru.tugas.show', compact('serial', 'mapel', 'task'));
}
```

- **Fungsi:** Menampilkan detail tugas
- **Parameter:** $serial, $mapel, $id (ID tugas)
- **Return:** View detail tugas
- **Route:** GET /aplikasi/{serial}/tugas/{mapel}/{id}

```php
/**
 * Halaman edit tugas
 */
public function edit($serial, $mapel, $id)
{
    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);
    $task = Post::findOrFail($id);
    $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();

    $sharedClasses = $task->shared_to_classes ?? [];

    return view('guru.tugas.edit', compact('serial', 'mapel', 'task', 'classrooms', 'sharedClasses'));
}
```

- **Fungsi:** Menampilkan form edit tugas
- **Parameter:** $serial, $mapel, $id
- **Return:** View edit tugas
- **Route:** GET /aplikasi/{serial}/tugas/{mapel}/{id}/edit

```php
/**
 * Update tugas
 */
public function update(Request $request, $serial, $mapel, $id)
{
    $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'link' => 'nullable|url',
        'deadline' => 'nullable|date',
        'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png',
        'remove_attachment' => 'nullable|boolean',
        'classrooms' => 'nullable|array',
        'classrooms.*' => 'exists:classrooms,id',
    ]);

    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);
    $task = Post::findOrFail($id);

    // Handle attachment
    $attachmentPath = $task->attachment;

    if ($request->hasFile('attachment')) {
        // Delete old file if exists
        if ($attachmentPath && \Storage::disk('public')->exists($attachmentPath)) {
            \Storage::disk('public')->delete($attachmentPath);
        }

        $file = $request->file('attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $attachmentPath = $file->storeAs('tugas', $filename, 'public');
    } elseif ($request->remove_attachment) {
        // Remove attachment if requested
        if ($attachmentPath && \Storage::disk('public')->exists($attachmentPath)) {
            \Storage::disk('public')->delete($attachmentPath);
        }
        $attachmentPath = null;
    }

    // Update task...
    // (kode dilanjutkan untuk update)
}
```

- **Fungsi:** Update data tugas
- **Parameter:** Request (title, description, link, deadline, attachment, classrooms), $serial, $mapel, $id
- **Return:** Redirect ke list tugas
- **Route:** PUT /aplikasi/{serial}/tugas/{mapel}/{id}

---

#### 6. OnlineMeetingController.php

**Lokasi:** `app/Http/Controllers/Guru/OnlineMeetingController.php`

**Deskripsi:** Mengelola kelas online/meeting

**Methods:**

```php
/**
 * Halaman utama meeting
 */
public function index($serial)
{
    $serial = Serial::findOrFail($serial);

    // Get all meetings for this serial
    $meetings = OnlineMeeting::where('serial_id', $serial->id)
        ->where('user_id', auth()->id())
        ->with(['classroom', 'mapel'])
        ->orderBy('start_time', 'desc')
        ->get();

    // Separate by status
    $upcomingMeetings = $meetings->where('status', 'scheduled')->sortBy('start_time');
    $ongoingMeetings = $meetings->where('status', 'ongoing');
    $endedMeetings = $meetings->whereIn('status', ['ended', 'cancelled']);

    // Get classrooms and mapels for quick create
    $classrooms = Classroom::where('serial_id', $serial->id)->get();
    $mapels = Mapel::all();

    return view('guru.meeting.index', compact('serial', 'upcomingMeetings', 'ongoingMeetings', 'endedMeetings', 'classrooms', 'mapels'));
}
```

- **Fungsi:** Menampilkan daftar meeting (dijadwalkan, sedang berlangsung, selesai)
- **Parameter:** $serial
- **Return:** View dengan daftar meeting
- **Route:** GET /aplikasi/{serial}/meeting

```php
/**
 * Quick start meeting
 */
public function quickStart(Request $request, $serial)
{
    $serial = Serial::findOrFail($serial);

    $request->validate([
        'title' => 'required|max:255',
        'mapel_id' => 'nullable|exists:mapels,id',
        'classroom_id' => 'nullable|exists:classrooms,id',
        'duration' => 'nullable|integer|min:15|max:480', // 15 min - 8 hours
    ]);

    $duration = (int)($request->duration ?? 60); // Default 60 minutes, cast to int
    $now = now();

    // Generate meeting code
    $meetingCode = OnlineMeeting::generateMeetingCode();

    // Generate Jitsi meeting link
    $meetingLink = 'https://meet.jit.si/' . $meetingCode;

    // Create instant meeting
    $meeting = OnlineMeeting::create([
        'serial_id' => $serial->id,
        'user_id' => auth()->id(),
        'classroom_id' => $request->classroom_id,
        'mapel_id' => $request->mapel_id,
        'title' => $request->title,
        'description' => 'Instant meeting - ' . $now->format('d M Y H:i'),
        'meeting_code' => $meetingCode,
        'meeting_link' => $meetingLink,
        'platform' => 'jitsi',
        'start_time' => $now,
        'end_time' => $now->copy()->addMinutes($duration),
        'status' => 'ongoing', // Langsung ongoing
        'room_id' => $meetingCode,
        'is_internal' => true,
    ]);

    // Redirect langsung ke meeting room
    return redirect()->route('guru.meeting.join', [$serial->id, $meeting->id]);
}
```

- **Fungsi:** Membuat dan langsung memulai meeting instant
- **Parameter:** Request (title, mapel_id, classroom_id, duration), $serial
- **Return:** Redirect ke halaman join meeting
- **Route:** POST /aplikasi/{serial}/meeting/quick-start

```php
/**
 * Halaman buat meeting terjadwal
 */
public function create($serial)
{
    $serial = Serial::findOrFail($serial);

    // Get classrooms and mapels
    $classrooms = Classroom::where('serial_id', $serial->id)->get();
    $mapels = Mapel::all();

    return view('guru.meeting.create', compact('serial', 'classrooms', 'mapels'));
}
```

- **Fungsi:** Menampilkan form membuat meeting terjadwal
- **Parameter:** $serial
- **Return:** View create meeting
- **Route:** GET /aplikasi/{serial}/meeting/create

```php
/**
 * Simpan meeting terjadwal
 */
public function store(Request $request, $serial)
{
    $serial = Serial::findOrFail($serial);

    $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'mapel_id' => 'nullable|exists:mapels,id',
        'classroom_id' => 'nullable|exists:classrooms,id',
        'start_time' => 'required|date|after:now',
        'end_time' => 'required|date|after:start_time',
        'platform' => 'required|in:jitsi,zoom,gmeet,other',
        'meeting_link' => 'nullable|url',
    ]);

    // Generate meeting code for Jitsi
    $meetingCode = OnlineMeeting::generateMeetingCode();

    // For Jitsi, room_id is the meeting code
    $roomId = $request->platform === 'jitsi' ? $meetingCode : null;

    OnlineMeeting::create([
        'serial_id' => $serial->id,
        'user_id' => auth()->id(),
        'classroom_id' => $request->classroom_id,
        'mapel_id' => $request->mapel_id,
        'title' => $request->title,
        'description' => $request->description,
        'meeting_code' => $meetingCode,
        'meeting_link' => $request->meeting_link,
        'platform' => $request->platform,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'status' => 'scheduled',
        'room_id' => $roomId,
        'is_internal' => $request->platform === 'jitsi',
    ]);

    return redirect()->route('guru.meeting', $serial->id)
        ->with('success', 'Meeting berhasil dijadwalkan!');
}
```

- **Fungsi:** Menyimpan meeting terjadwal baru
- **Parameter:** Request (title, description, mapel_id, classroom_id, start_time, end_time, platform, meeting_link), $serial
- **Return:** Redirect ke halaman meeting
- **Route:** POST /aplikasi/{serial}/meeting

```php
/**
 * Tampilkan detail meeting
 */
public function show($serial, $id)
{
    $serial = Serial::findOrFail($serial);
    $meeting = OnlineMeeting::with(['classroom', 'mapel', 'user'])->findOrFail($id);

    // Check authorization
    if ($meeting->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    return view('guru.meeting.show', compact('serial', 'meeting'));
}
```

- **Fungsi:** Menampilkan detail meeting
- **Parameter:** $serial, $id (ID meeting)
- **Return:** View detail meeting
- **Route:** GET /aplikasi/{serial}/meeting/{id}

```php
/**
 * Join meeting
 */
public function join($serial, $id)
{
    $serial = Serial::findOrFail($serial);
    $meeting = OnlineMeeting::findOrFail($id);

    // Check if meeting is active
    if (!$meeting->isActive()) {
        return redirect()->route('guru.meeting.show', [$serial->id, $meeting->id])
            ->with('error', 'Meeting belum dimulai atau sudah berakhir!');
    }

    // Update status to ongoing if scheduled
    if ($meeting->status === 'scheduled') {
        $meeting->update(['status' => 'ongoing']);
    }

    // Get user name for Jitsi
    $userName = auth()->user()->name;
    $userEmail = auth()->user()->email;

    return view('guru.meeting.join', compact('serial', 'meeting', 'userName', 'userEmail'));
}
```

- **Fungsi:** Join/masuk ke meeting room
- **Parameter:** $serial, $id
- **Return:** View meeting room (Jitsi iframe)
- **Route:** GET /aplikasi/{serial}/meeting/{id}/join

```php
/**
 * Halaman edit meeting
 */
public function edit($serial, $id)
{
    $serial = Serial::findOrFail($serial);
    $meeting = OnlineMeeting::findOrFail($id);

    // Check authorization
    if ($meeting->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    $classrooms = Classroom::where('serial_id', $serial->id)->get();
    $mapels = Mapel::all();

    return view('guru.meeting.edit', compact('serial', 'meeting', 'classrooms', 'mapels'));
}
```

- **Fungsi:** Menampilkan form edit meeting
- **Parameter:** $serial, $id
- **Return:** View edit meeting
- **Route:** GET /aplikasi/{serial}/meeting/{id}/edit

```php
/**
 * Update meeting
 */
public function update(Request $request, $serial, $id)
{
    $meeting = OnlineMeeting::findOrFail($id);

    // Check authorization
    if ($meeting->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'mapel_id' => 'nullable|exists:mapels,id',
        'classroom_id' => 'nullable|exists:classrooms,id',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
        'meeting_link' => 'nullable|url',
    ]);

    // Update meeting...
    // (kode dilanjutkan untuk update)
}
```

- **Fungsi:** Update data meeting
- **Parameter:** Request, $serial, $id
- **Return:** Redirect ke halaman meeting
- **Route:** PUT /aplikasi/{serial}/meeting/{id}

---

#### 7. SoalController.php

**Lokasi:** `app/Http/Controllers/Guru/SoalController.php`

**Deskripsi:** Mengelola soal latihan dan ujian

**Methods:**

```php
/**
 * Halaman utama soal
 */
public function index($serial)
{
    $serial = Serial::findOrFail($serial);

    // Define soal categories (all from admin, can be shared)
    $categories = [
        ['id' => 'ulangan-harian', 'name' => 'Ulangan Harian', 'icon' => 'bx-edit', 'color' => 'primary', 'type_id' => 1],
        ['id' => 'pts', 'name' => 'Penilaian Tengah Semester', 'icon' => 'bx-file', 'color' => 'warning', 'type_id' => 2],
        ['id' => 'pas', 'name' => 'Penilaian Akhir Semester', 'icon' => 'bx-book', 'color' => 'danger', 'type_id' => 3],
        ['id' => 'tambahan', 'name' => 'Soal Tambahan', 'icon' => 'bx-plus-circle', 'color' => 'success', 'type_id' => null],
    ];

    return view('guru.soal.index', compact('serial', 'categories'));
}
```

- **Fungsi:** Menampilkan halaman utama soal dengan kategori
- **Parameter:** $serial
- **Return:** View index soal
- **Route:** GET /aplikasi/{serial}/soal

```php
/**
 * List soal berdasarkan kategori
 */
public function listByCategory($serial, $category)
{
    $serial = Serial::findOrFail($serial);

    // Get category info
    $categoryMap = [
        'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary', 'type_id' => 1],
        'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning', 'type_id' => 2],
        'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger', 'type_id' => 3],
        'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success', 'type_id' => null],
    ];

    $categoryInfo = $categoryMap[$category] ?? ['name' => 'Soal', 'color' => 'info', 'type_id' => null];
    $exerciseTypeId = $categoryInfo['type_id'];

    // Get exercises based on category
    if ($category === 'tambahan') {
        // Soal Tambahan: custom exercises from teacher (is_admin = 0)
        $exercises = Exercise::where('serial_id', $serial->id)
            ->where('is_admin', 0)
            ->with(['lesson.mapel', 'exerciseItems', 'exerciseType'])
            ->orderBy('created_at', 'desc')
            ->get();
    } else {
        // Admin exercises (UH, PTS, PAS) - is_admin = 1 and serial_id is null
        $query = Exercise::where('is_admin', 1)
            ->whereNull('serial_id'); // Soal admin tidak punya serial_id

        if ($exerciseTypeId) {
            $query->where('exercise_type_id', $exerciseTypeId);
        }

        $exercises = $query->with(['lesson.mapel', 'exerciseItems', 'exerciseType', 'classrooms'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    return view('guru.soal.list-direct', compact('serial', 'category', 'categoryInfo', 'exercises'));
}
```

- **Fungsi:** Menampilkan daftar soal berdasarkan kategori (UH/PTS/PAS/Tambahan)
- **Parameter:** $serial, $category
- **Return:** View dengan daftar soal
- **Route:** GET /aplikasi/{serial}/soal/{category}

```php
/**
 * Halaman buat soal custom
 */
public function createCustom($serial)
{
    $serial = Serial::findOrFail($serial);
    $category = 'tambahan'; // Fixed category for custom exercises

    // Get all mapels (mata pelajaran) untuk dipilih
    $mapels = Mapel::orderBy('name')->get();

    // Get exercise types
    $exerciseTypes = ExerciseType::all();

    // Get classrooms untuk dibagikan
    $classrooms = Classroom::where('serial_id', $serial->id)->get();

    $categoryInfo = ['name' => 'Soal Tambahan', 'color' => 'success'];

    return view('guru.soal.create-custom', compact('serial', 'category', 'categoryInfo', 'mapels', 'exerciseTypes', 'classrooms'));
}
```

- **Fungsi:** Menampilkan form membuat soal custom
- **Parameter:** $serial
- **Return:** View create soal custom
- **Route:** GET /aplikasi/{serial}/soal/tambahan/create

```php
/**
 * Simpan soal custom
 */
public function storeCustom(Request $request, $serial)
{
    $serial = Serial::findOrFail($serial);
    $category = 'tambahan';

    $request->validate([
        'exercise_type_id' => 'required|exists:exercise_types,id',
        'question_type' => 'required|in:pilihan_ganda,essai,jawaban_singkat',
        'mapel_id' => 'required|exists:mapels,id',
        'questions' => 'required|array|min:1',
        'questions.*.title' => 'required|max:255',
        'questions.*.question' => 'required',
        'questions.*.answer' => 'nullable',
        'questions.*.options' => 'nullable|array',
        'classrooms' => 'nullable|array',
    ]);

    // Find or create base lesson for this mapel
    $lesson = Lesson::firstOrCreate([
        'mapel_id' => $request->mapel_id,
        'category' => Lesson::CATEGORY_SOAL,
        'name' => 'Base Lesson',
    ], [
        'grade' => '1',
        'semester' => 1,
    ]);

    // Prepare shared_to_classes
    $sharedToClasses = null;
    if ($request->classrooms) {
        $sharedToClasses = json_encode($request->classrooms);
    }

    // Loop through all questions and create exercises
    $createdCount = 0;
    foreach ($request->questions as $index => $questionData) {
        // Create exercise header
        $exercise = Exercise::create([
            'lesson_id' => $lesson->id,
            'serial_id' => $serial->id,
            'exercise_type_id' => $request->exercise_type_id,
            'title' => $questionData['title'],
            'description' => null,
            'is_admin' => 0, // Custom dari guru
            'shared_to_classes' => $sharedToClasses,
        ]);

        // Create exercise item with question details
        // Map question_type to exercise_model_id
        $exerciseModelId = 1; // Default: Pilihan Ganda
        if ($request->question_type === 'essai') {
            $exerciseModelId = 2;
        } elseif ($request->question_type === 'jawaban_singkat') {
            $exerciseModelId = 3;
        }

        // Create exercise item...
        // (kode dilanjutkan untuk create item)

        $createdCount++;
    }

    return redirect()->route('guru.soal.category', [$serial->id, $category])
        ->with('success', "$createdCount soal berhasil ditambahkan!");
}
```

- **Fungsi:** Menyimpan soal custom yang dibuat guru
- **Parameter:** Request (exercise_type_id, question_type, mapel_id, questions, classrooms), $serial
- **Return:** Redirect ke list soal tambahan
- **Route:** POST /aplikasi/{serial}/soal/tambahan

---

### MAIN CONTROLLERS

#### 8. AppController.php

**Lokasi:** `app/Http/Controllers/AppController.php`

**Deskripsi:** Controller untuk aplikasi/serial

**Methods:**

```php
/**
 * Halaman utama aplikasi berdasarkan serial
 */
public function index($serial)
{
    $serial = Serial::with(['product','classrooms'])
                ->where('user_id', auth()->id())
                ->findOrFail($serial);

    return view('guru.app.index', compact('serial'));
}
```

- **Fungsi:** Menampilkan halaman aplikasi berdasarkan serial
- **Parameter:** $serial (ID serial)
- **Return:** View aplikasi
- **Route:** GET /aplikasi/{serial}

---

#### 9. ProfileController.php

**Lokasi:** `app/Http/Controllers/ProfileController.php`

**Deskripsi:** Mengelola profil pengguna

**Methods:**

```php
/**
 * Display the user's profile form.
 */
public function edit(Request $request): View
{
    return view('profile.edit', [
        'user' => $request->user(),
    ]);
}
```

- **Fungsi:** Menampilkan form edit profil
- **Parameter:** Request
- **Return:** View profile edit
- **Route:** GET /profile

```php
/**
 * Update the user's profile information.
 */
public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $request->user()->fill($request->validated());

    if ($request->user()->isDirty('email')) {
        $request->user()->email_verified_at = null;
    }

    $request->user()->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}
```

- **Fungsi:** Update data profil pengguna
- **Parameter:** ProfileUpdateRequest
- **Return:** Redirect ke profile edit dengan status
- **Route:** PATCH /profile

```php
/**
 * Delete the user's account.
 */
public function destroy(Request $request): RedirectResponse
{
    $request->validateWithBag('userDeletion', [
        'password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/');
}
```

- **Fungsi:** Hapus akun pengguna
- **Parameter:** Request (dengan password)
- **Return:** Redirect ke home
- **Route:** DELETE /profile

---

## ROUTES

### Web Routes

**Lokasi:** `routes/web.php`

**Route Utama:**

```php
// Redirect ke dashboard jika sudah login
Route::middleware('auth')->get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard Guru
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});
```

**Route Aplikasi/Serial:**

```php
Route::middleware(['auth'])->prefix('guru')->group(function () {
    // Aplikasi routes
    Route::get('/aplikasi/{serial}', [AppController::class, 'index'])
        ->name('guru.aplikasi');

    // Kelas routes
    Route::prefix('/aplikasi/{serial}/kelas')->group(function () {
        Route::get('/pilih', [KelasController::class, 'pilihKelas'])
            ->name('guru.kelas.pilih');
        Route::post('/', [KelasController::class, 'store'])
            ->name('guru.kelas.store');
        Route::delete('/{classroom}', [KelasController::class, 'destroy'])
            ->name('guru.kelas.destroy');

        Route::get('/{classroom}/dashboard', [KelasController::class, 'dashboard'])
            ->name('guru.kelas.dashboard');
        Route::post('/{classroom}/siswa', [KelasController::class, 'storeStudent'])
            ->name('guru.kelas.siswa.store');
        Route::delete('/{classroom}/siswa/{student}', [KelasController::class, 'destroyStudent'])
            ->name('guru.kelas.siswa.destroy');
    });

    // Materi routes
    Route::get('/aplikasi/{serial}/materi', [MateriController::class, 'index'])
        ->name('guru.materi');
    Route::get('/aplikasi/{serial}/materi/admin', [MateriController::class, 'admin'])
        ->name('guru.materi.admin');

    // Tugas routes
    Route::get('/aplikasi/{serial}/tugas', [TugasController::class, 'index'])
        ->name('guru.tugas');
    Route::get('/aplikasi/{serial}/tugas/{mapel}', [TugasController::class, 'listByMapel'])
        ->name('guru.tugas.mapel');

    // Meeting routes
    Route::get('/aplikasi/{serial}/meeting', [OnlineMeetingController::class, 'index'])
        ->name('guru.meeting');
    Route::post('/aplikasi/{serial}/meeting/quick-start', [OnlineMeetingController::class, 'quickStart'])
        ->name('guru.meeting.quick-start');

    // Soal routes
    Route::get('/aplikasi/{serial}/soal', [SoalController::class, 'index'])
        ->name('guru.soal');
    Route::get('/aplikasi/{serial}/soal/{category}', [SoalController::class, 'listByCategory'])
        ->name('guru.soal.category');
});
```

**Auth Routes:**

```php
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
```

---

## CONTOH FLOW MVC

### Flow Login User

**1. User mengakses URL `/login`**

**Route** (`routes/auth.php`):

```php
Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
```

**2. Controller menerima request dan menampilkan view**

**Controller** (`app/Http/Controllers/Auth/AuthenticatedSessionController.php`):

```php
public function create(): View
{
    return view('auth.login');
}
```

**3. View ditampilkan ke user**

**View** (`resources/views/auth/login.blade.php`):

```blade
<form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" />
    <input type="password" name="password" />
    <button type="submit">Login</button>
</form>
```

**4. User submit form, data dikirim ke Controller**

**Route**:

```php
Route::post('login', [AuthenticatedSessionController::class, 'store']);
```

**5. Controller memproses login dan menggunakan Model User**

**Controller**:

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate(); // Menggunakan User Model untuk validasi
    $request->session()->regenerate();
    return redirect()->route('dashboard');
}
```

**Model** (`app/Models/User.php`) - digunakan oleh authentication:

```php
// Laravel otomatis menggunakan User model untuk Auth
Auth::attempt(['email' => $email, 'password' => $password])
```

**6. Redirect ke dashboard jika berhasil**

---

### Flow Kelola Kelas

**Skenario: Guru menambah kelas baru**

**1. User membuka halaman pilih kelas**

**Route**:

```php
Route::get('/aplikasi/{serial}/kelas/pilih', [KelasController::class, 'pilihKelas'])
    ->name('guru.kelas.pilih');
```

**2. Controller mengambil data dari Model**

**Controller** (`app/Http/Controllers/Guru/KelasController.php`):

```php
public function pilihKelas($serial)
{
    // Ambil data serial dari Model
    $serial = Serial::findOrFail($serial);

    // Ambil data classrooms dari Model dengan relasi
    $classrooms = Classroom::where('serial_id', $serial->id)
        ->withCount('students') // Menggunakan relasi di Model
        ->get();

    // Kirim data ke View
    return view('guru.kelas.pilih', compact('serial', 'classrooms'));
}
```

**Model** (`app/Models/Classroom.php`):

```php
// Relasi untuk withCount
public function students()
{
    return $this->hasMany(Student::class);
}
```

**3. View menampilkan data**

**View** (`resources/views/guru/kelas/pilih.blade.php`):

```blade
@foreach($classrooms as $classroom)
    <div class="card">
        <h5>{{ $classroom->name }}</h5>
        <span>{{ $classroom->students_count }} Siswa</span>
    </div>
@endforeach

<!-- Form tambah kelas -->
<form method="POST" action="{{ route('guru.kelas.store', $serial->id) }}">
    @csrf
    <input name="name" placeholder="Nama Kelas" />
    <input name="grade" placeholder="Tingkat" />
    <button type="submit">Simpan</button>
</form>
```

**4. User submit form tambah kelas**

**Route**:

```php
Route::post('/aplikasi/{serial}/kelas', [KelasController::class, 'store'])
    ->name('guru.kelas.store');
```

**5. Controller memproses dan menyimpan ke database via Model**

**Controller**:

```php
public function store(Request $request, $serial)
{
    $serial = Serial::findOrFail($serial);

    // Validasi input
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'grade' => 'nullable|string|max:50',
    ]);

    // Generate kode unik
    $code = $this->generateClassroomCode();

    // Simpan ke database via Model
    $classroom = new Classroom();
    $classroom->serial_id = $serial->id;
    $classroom->name = $data['name'];
    $classroom->grade = $data['grade'] ?? null;
    $classroom->code = $code;
    $classroom->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])
        ->with('success', 'Kelas berhasil ditambahkan.');
}
```

**Model** (`app/Models/Classroom.php`):

```php
protected $fillable = [
    'serial_id', 'name', 'grade', 'code'
];

// Model otomatis handle penyimpanan ke database
```

**6. Redirect kembali ke halaman pilih kelas dengan data terbaru**

---

### Flow Buat Tugas

**Skenario: Guru membuat tugas baru**

**1. User membuka form buat tugas**

**Route**:

```php
Route::get('/aplikasi/{serial}/tugas/{mapel}/create', [TugasController::class, 'create'])
    ->name('guru.tugas.create');
```

**2. Controller menyiapkan data untuk form**

**Controller** (`app/Http/Controllers/Guru/TugasController.php`):

```php
public function create($serial, $mapel)
{
    // Ambil data dari Model
    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);
    $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();

    // Kirim ke View
    return view('guru.tugas.create', compact('serial', 'mapel', 'classrooms'));
}
```

**Model** - Multiple models digunakan:

- `Serial::findOrFail($serial)` - Query ke tabel serials
- `Mapel::findOrFail($mapel)` - Query ke tabel mapels
- `Classroom::where()...` - Query ke tabel classrooms

**3. View menampilkan form**

**View** (`resources/views/guru/tugas/create.blade.php`):

```blade
<form action="{{ route('guru.tugas.store', [$serial->id, $mapel->id]) }}"
      method="POST" enctype="multipart/form-data">
    @csrf

    <input type="text" name="title" value="{{ old('title') }}" />
    @error('title')
        <span class="error">{{ $message }}</span>
    @enderror

    <textarea name="description">{{ old('description') }}</textarea>

    <input type="file" name="attachment" />

    <!-- Pilih kelas -->
    @foreach($classrooms as $classroom)
        <input type="checkbox" name="classrooms[]" value="{{ $classroom->id }}" />
        {{ $classroom->name }}
    @endforeach

    <input type="datetime-local" name="deadline" />

    <button type="submit">Simpan Tugas</button>
</form>
```

**4. User submit form**

**Route**:

```php
Route::post('/aplikasi/{serial}/tugas/{mapel}', [TugasController::class, 'store'])
    ->name('guru.tugas.store');
```

**5. Controller memproses dan menyimpan via Model**

**Controller**:

```php
public function store(Request $request, $serial, $mapel)
{
    // Validasi
    $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'attachment' => 'nullable|file|max:10240',
        'classrooms' => 'nullable|array',
        'deadline' => 'nullable|date',
    ]);

    $serial = Serial::findOrFail($serial);
    $mapel = Mapel::findOrFail($mapel);

    // Handle file upload
    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $attachmentPath = $file->storeAs('tugas', $filename, 'public');
    }

    // Simpan ke database via Model Post
    Post::create([
        'serial_id' => $serial->id,
        'user_id' => auth()->id(),
        'mapel_id' => $mapel->id,
        'title' => $request->title,
        'description' => $request->description,
        'slug' => Str::slug($request->title) . '-' . time(),
        'attachment' => $attachmentPath,
        'deadline' => $request->deadline,
        'shared_to_classes' => $request->classrooms,
        'is_task' => 1,
    ]);

    return redirect()->route('guru.tugas.mapel', [$serial->id, $mapel->id])
        ->with('success', 'Tugas berhasil ditambahkan!');
}
```

**Model** (`app/Models/Post.php`):

```php
protected $fillable = [
    'serial_id', 'user_id', 'mapel_id', 'title', 'description',
    'slug', 'attachment', 'deadline', 'shared_to_classes', 'is_task',
];

protected $casts = [
    'shared_to_classes' => 'array', // Otomatis convert ke array
    'deadline' => 'datetime',       // Otomatis convert ke Carbon
    'is_task' => 'boolean',
];

// Relasi
public function serial() {
    return $this->belongsTo(Serial::class);
}

public function mapel() {
    return $this->belongsTo(Mapel::class);
}
```

**6. Redirect ke halaman list tugas**

**Controller** mengambil data terbaru:

```php
public function listByMapel($serial, $mapel)
{
    $tugas = Post::where('serial_id', $serial->id)
        ->where('mapel_id', $mapel->id)
        ->where('is_task', 1)
        ->with(['mapel', 'serial']) // Eager loading relasi
        ->latest()
        ->get();

    return view('guru.tugas.list', compact('tugas'));
}
```

**View** menampilkan list:

```blade
@foreach($tugas as $t)
    <div class="card">
        <h5>{{ $t->title }}</h5>
        <p>{{ $t->mapel->name }}</p>
        <span>Deadline: {{ $t->deadline->format('d M Y') }}</span>
    </div>
@endforeach
```

---

## Catatan Penting

### Konvensi Penamaan

1. **Controllers**: PascalCase dengan suffix "Controller"

   - Contoh: `KelasController`, `TugasController`

2. **Models**: PascalCase singular

   - Contoh: `User`, `Classroom`, `Student`
   - Laravel otomatis mapping ke tabel plural (users, classrooms, students)

3. **Methods**: camelCase

   - Contoh: `index()`, `pilihKelas()`, `storeStudent()`

4. **Routes**: kebab-case dengan prefix grup

   - Contoh: `guru.kelas.pilih`, `guru.tugas.mapel`

5. **Views**: kebab-case
   - Contoh: `guru/kelas/pilih.blade.php`

### Pattern CRUD Standard (RESTful)

Untuk setiap resource, umumnya menggunakan method:

- `index()` - GET - Menampilkan daftar
- `create()` - GET - Form tambah baru
- `store()` - POST - Simpan data baru
- `show()` - GET - Detail satu item
- `edit()` - GET - Form edit
- `update()` - PUT/PATCH - Update data
- `destroy()` - DELETE - Hapus data

### Middleware

- `auth`: Harus login (cek via User Model)
- `guest`: Hanya untuk yang belum login
- `verified`: Email harus terverifikasi

### Validasi

Menggunakan `$request->validate()` dengan rules:

- `required`: Wajib diisi
- `nullable`: Boleh kosong
- `max:255`: Maksimal 255 karakter
- `email`: Format email valid
- `exists:table,column`: Cek data ada di tabel (query via Model)
- `in:val1,val2`: Hanya boleh nilai tertentu
- `unique:table,column`: Harus unik di tabel

### Authorization

Menggunakan pengecekan:

```php
if ($model->user_id !== auth()->id()) {
    abort(403, 'Unauthorized');
}
```

### Eloquent Relationships (Model)

- `hasMany()` - One to Many
- `belongsTo()` - Inverse of hasMany
- `belongsToMany()` - Many to Many (butuh pivot table)
- `hasOne()` - One to One

### Query Builder Methods (Model)

- `find($id)` - Cari by primary key
- `findOrFail($id)` - Cari atau throw 404
- `where()` - Filter data
- `get()` - Execute query, return collection
- `first()` - Ambil satu data pertama
- `create()` - Insert data baru
- `update()` - Update existing data
- `delete()` - Hapus data
- `with()` - Eager loading relasi
- `withCount()` - Hitung jumlah relasi

### Blade Directives (View)

- `@extends` - Extends layout
- `@section` - Define section
- `@yield` - Output section
- `@foreach` - Loop array
- `@forelse` - Loop dengan empty condition
- `@if`, `@else`, `@endif` - Conditional
- `@csrf` - CSRF token
- `@method` - HTTP method spoofing
- `{{ }}` - Echo escaped
- `{!! !!}` - Echo unescaped (hati-hati XSS)
- `@error` - Show validation error

---

## VIEWS (BLADE TEMPLATES)

### AUTH VIEWS

#### 1. login.blade.php

**Lokasi:** `resources/views/auth/login.blade.php`

**Deskripsi:** Halaman login pengguna

**Komponen Utama:**

```blade
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                          name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember">
                <span class="ms-2">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <x-primary-button class="ms-3">
            {{ __('Log in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
```

**Fitur:**

- Form login dengan email dan password
- Checkbox "Remember Me"
- Link lupa password
- Validasi error real-time
- Layout menggunakan component `x-guest-layout`

---

### GURU VIEWS

#### 2. guru/dashboard.blade.php

**Lokasi:** `resources/views/guru/dashboard.blade.php`

**Deskripsi:** Dashboard utama guru menampilkan daftar aplikasi/serial

**Query Blade:**

```blade
@extends('layouts.guru')

@section('content')
<h3 class="mb-4">Daftar Aplikasi</h3>

<div class="row">
    @forelse($serials as $s)
    <div class="col-md-6 mb-3">
        <a href="/app/{{ $s->id }}" class="card shadow-sm p-3">
            <h5>{{ $s->product->name ?? 'Produk Tanpa Nama' }}</h5>
            <p>{{ $s->classrooms->count() }} Kelas</p>
            <p class="text-muted">
                {{ $s->created_at ? $s->created_at->format('d M Y') : '-' }}
            </p>
        </a>
    </div>
    @empty
    <p class="text-muted">Belum ada aplikasi yang aktif untuk akun Anda.</p>
    @endforelse
</div>
@endsection
```

**Data yang Digunakan:**

- `$serials` - Collection dari model Serial dengan relasi product dan classrooms

**Blade Directives:**

- `@forelse` - Loop dengan kondisi empty
- `@empty` - Tampilkan jika data kosong
- `{{ }}` - Echo data (escaped)
- `->count()` - Method Laravel Collection untuk hitung jumlah
- `->format()` - Format tanggal Carbon

---

#### 3. guru/kelas/pilih.blade.php

**Lokasi:** `resources/views/guru/kelas/pilih.blade.php`

**Deskripsi:** Halaman pilih dan kelola kelas

**Query Blade:**

```blade
@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-group text-info me-2'></i>Kelola Kelas
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahKelas">
            <i class='bx bx-plus me-1'></i>Tambah Kelas
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($classrooms as $classroom)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <h5 class="mb-0">{{ $classroom->name }}</h5>
                        @if($classroom->grade)
                            <small class="text-muted">Tingkat {{ $classroom->grade }}</small>
                        @endif

                        <span class="badge bg-label-primary">
                            <i class='bx bx-user me-1'></i>{{ $classroom->students_count }} Siswa
                        </span>

                        <div class="d-flex gap-2">
                            <a href="{{ route('guru.kelas.dashboard', [$serial->id, $classroom->id]) }}"
                               class="btn btn-sm btn-info flex-grow-1">
                                <i class='bx bx-user-circle me-1'></i>Kelola Siswa
                            </a>
                            <form method="POST"
                                  action="{{ route('guru.kelas.destroy', [$serial->id, $classroom->id]) }}"
                                  onsubmit="return confirm('Hapus kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class='bx bx-group display-1 text-muted'></i>
                        <h5 class="mt-3">Belum Ada Kelas</h5>
                        <p class="text-muted mb-3">Tambahkan kelas untuk mulai mengelola siswa</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('guru.kelas.store', $serial->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Contoh: Kelas 4A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat / Grade</label>
                        <input type="text" name="grade" class="form-control"
                               placeholder="Contoh: 4">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

**Data yang Digunakan:**

- `$classrooms` - Collection kelas dengan `students_count` (withCount)
- `$serial` - Model Serial untuk routing

**Fitur:**

- Loop daftar kelas dengan card
- Modal Bootstrap untuk tambah kelas
- Form hapus kelas dengan konfirmasi JavaScript
- Badge jumlah siswa
- Session flash message untuk notifikasi

**Blade Directives:**

- `@csrf` - CSRF Token protection
- `@method('DELETE')` - Method spoofing untuk DELETE request
- `@if`, `@endif` - Conditional rendering
- `route()` - Helper untuk generate URL dari nama route

---

#### 4. guru/kelas/dashboard.blade.php

**Lokasi:** `resources/views/guru/kelas/dashboard.blade.php`

**Deskripsi:** Dashboard kelas menampilkan daftar siswa

**Query Blade:**

```blade
@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('guru.kelas.pilih', $serial->id) }}">Kelas</a>
            </li>
            <li class="breadcrumb-item active">{{ $classroom->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $classroom->name }}</h4>
            <p class="text-muted mb-0">{{ $students->count() }} Siswa</p>
        </div>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal"
                    data-bs-target="#modalImportSiswa">
                <i class='bx bx-upload me-1'></i>Import CSV
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modalTambahSiswa">
                <i class='bx bx-user-plus me-1'></i>Tambah Siswa
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Siswa</h5>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->nis ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    {{ substr($student->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <strong>{{ $student->name }}</strong>
                                        </div>
                                    </td>
                                    <td><code>{{ $student->username }}</code></td>
                                    <td>{{ $student->email ?? '-' }}</td>
                                    <td><code>{{ $student->password_text ?? '********' }}</code></td>
                                    <td>
                                        <form method="POST"
                                              action="{{ route('guru.kelas.siswa.destroy', [$serial->id, $classroom->id, $student->id]) }}"
                                              onsubmit="return confirm('Hapus siswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-4">Belum ada siswa di kelas ini</p>
            @endif
        </div>
    </div>
</div>
@endsection
```

**Data yang Digunakan:**

- `$students` - Collection siswa di kelas
- `$classroom` - Model Classroom
- `$serial` - Model Serial

**Fitur:**

- Breadcrumb navigasi
- Tabel daftar siswa dengan avatar initial
- Button import CSV dan tambah siswa manual
- Tampilkan password plain text untuk info guru
- Hapus siswa dengan konfirmasi

**Helper Functions:**

- `substr()` - Ambil karakter pertama untuk avatar
- `?? '-'` - Null coalescing operator untuk default value

---

#### 5. guru/materi/index.blade.php

**Lokasi:** `resources/views/guru/materi/index.blade.php`

**Deskripsi:** Halaman utama materi dengan 2 kategori

**Query Blade:**

```blade
@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-1">Materi – {{ $serial->product->name }}</h4>
            <p class="text-muted mb-0">Pilih kategori materi yang ingin diakses</p>
        </div>
    </div>

    <!-- Category Cards -->
    <div class="row g-4 mb-4">
        <!-- Materi dari Admin -->
        <div class="col-md-6">
            <a href="{{ route('guru.materi.admin', $serial->id) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition border-primary">
                    <div class="card-body text-center py-5">
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            <span class="avatar-initial rounded bg-label-primary"
                                  style="width: 80px; height: 80px;">
                                <i class='bx bx-book-bookmark' style="font-size: 48px;"></i>
                            </span>
                        </div>
                        <h4 class="mb-2">Materi</h4>
                        <p class="text-muted mb-0">Materi yang disediakan oleh admin</p>
                        <span class="badge bg-label-primary mt-2">Dari Admin</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Materi Tambahan (Buatan Guru) -->
        <div class="col-md-6">
            <a href="{{ route('guru.materi.custom', $serial->id) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-shadow-lg transition border-success">
                    <div class="card-body text-center py-5">
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            <span class="avatar-initial rounded bg-label-success"
                                  style="width: 80px; height: 80px;">
                                <i class='bx bx-book-add' style="font-size: 48px;"></i>
                            </span>
                        </div>
                        <h4 class="mb-2">Materi Tambahan</h4>
                        <p class="text-muted mb-0">Materi yang Anda buat sendiri</p>
                        <span class="badge bg-label-success mt-2">Buatan Guru</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.hover-shadow-lg {
    transition: all 0.3s ease;
}

.hover-shadow-lg:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-5px);
}
</style>
@endsection
```

**Data yang Digunakan:**

- `$serial` - Model Serial dengan relasi product

**Fitur:**

- 2 Card kategori materi (Admin dan Custom)
- Hover effect dengan CSS transform
- Icon Boxicons
- Bootstrap grid responsive

---

#### 6. guru/tugas/index.blade.php

**Lokasi:** `resources/views/guru/tugas/index.blade.php`

**Deskripsi:** Halaman pilih mata pelajaran untuk tugas

**Query Blade:**

```blade
@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Tugas /</span> Pilih Mata Pelajaran
    </h4>

    <div class="row">
        @forelse($mapels as $mapel)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class='bx bx-task fs-4'></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $mapel->name }}</h5>
                            </div>
                        </div>
                        <a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}"
                           class="btn btn-warning btn-sm w-100">
                            <i class='bx bx-right-arrow-alt me-1'></i>Lihat Tugas
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Belum ada mata pelajaran.</div>
            </div>
        @endforelse
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endsection
```

**Data yang Digunakan:**

- `$mapels` - Collection mata pelajaran
- `$serial` - Model Serial

---

#### 7. guru/tugas/create.blade.php

**Lokasi:** `resources/views/guru/tugas/create.blade.php`

**Deskripsi:** Form tambah tugas baru

**Query Blade:**

```blade
@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}">
                    {{ $mapel->name }}
                </a>
            </li>
            <li class="breadcrumb-item active">Tambah Tugas</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class='bx bx-task text-warning me-2'></i>Tambah Tugas Baru
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.tugas.store', [$serial->id, $mapel->id]) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Judul -->
                <div class="mb-3">
                    <label class="form-label">
                        Judul Tugas <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="mb-3">
                    <label class="form-label">Deskripsi Tugas</label>
                    <textarea name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Link -->
                <div class="mb-3">
                    <label class="form-label">Link Materi</label>
                    <input type="url" name="link"
                           class="form-control @error('link') is-invalid @enderror"
                           value="{{ old('link') }}" placeholder="https://example.com">
                    @error('link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- File Upload -->
                <div class="mb-3">
                    <label class="form-label">Lampiran File</label>
                    <input type="file" name="attachment"
                           class="form-control @error('attachment') is-invalid @enderror"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png">
                    @error('attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Max 10MB</small>
                </div>

                <!-- Deadline -->
                <div class="mb-3">
                    <label class="form-label">Deadline Pengumpulan</label>
                    <input type="datetime-local" name="deadline"
                           class="form-control @error('deadline') is-invalid @enderror"
                           value="{{ old('deadline') }}">
                    @error('deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class='bx bx-save me-1'></i>Simpan Tugas
                    </button>
                    <a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}"
                       class="btn btn-secondary">
                        <i class='bx bx-x me-1'></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

**Data yang Digunakan:**

- `$serial` - Model Serial
- `$mapel` - Model Mapel
- `$classrooms` - Collection kelas untuk sharing

**Fitur:**

- Form dengan validasi Laravel
- Error handling dengan `@error` directive
- File upload dengan accept types
- `old()` helper untuk retain input after validation fails
- Breadcrumb navigation

**Blade Directives:**

- `@error('field')` - Show validation error for specific field
- `old('field')` - Get old input value
- `enctype="multipart/form-data"` - Required untuk file upload

---

#### 8. guru/meeting/index.blade.php

**Lokasi:** `resources/views/guru/meeting/index.blade.php`

**Deskripsi:** Halaman kelas online dengan Jitsi Meet

**Query Blade:**

```blade
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
            <button type="button" class="btn btn-success"
                    data-bs-toggle="modal" data-bs-target="#quickStartModal">
                <i class='bx bx-play-circle me-1'></i>Quick Start Meeting
            </button>
            <a href="{{ route('guru.meeting.create', $serial->id) }}" class="btn btn-primary">
                <i class='bx bx-plus me-1'></i>Jadwalkan Meeting
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#ongoing" role="tab">
                <i class='bx bx-video me-1'></i>Sedang Berlangsung
                @if($ongoingMeetings->count() > 0)
                <span class="badge bg-danger ms-1">{{ $ongoingMeetings->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#upcoming" role="tab">
                <i class='bx bx-calendar me-1'></i>Akan Datang
                @if($upcomingMeetings->count() > 0)
                <span class="badge bg-primary ms-1">{{ $upcomingMeetings->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab"
                    data-bs-target="#ended" role="tab">
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

                            @if($meeting->mapel)
                            <span class="badge bg-label-primary me-1">
                                <i class='bx bx-book'></i> {{ $meeting->mapel->name }}
                            </span>
                            @endif

                            @if($meeting->classroom)
                            <span class="badge bg-label-info">
                                <i class='bx bx-group'></i> {{ $meeting->classroom->name }}
                            </span>
                            @endif

                            <div class="text-muted small mb-3">
                                <div><i class='bx bx-time'></i>
                                    {{ $meeting->start_time->format('H:i') }} -
                                    {{ $meeting->end_time->format('H:i') }}
                                </div>
                            </div>

                            <div class="d-grid">
                                <a href="{{ route('guru.meeting.join', [$serial->id, $meeting->id]) }}"
                                   class="btn btn-danger">
                                    <i class='bx bx-video me-1'></i>Gabung Meeting
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">Tidak ada meeting yang sedang berlangsung</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
```

**Data yang Digunakan:**

- `$ongoingMeetings` - Collection meeting yang sedang berlangsung
- `$upcomingMeetings` - Collection meeting yang dijadwalkan
- `$endedMeetings` - Collection meeting yang sudah selesai
- `$classrooms` - Collection kelas
- `$mapels` - Collection mata pelajaran

**Fitur:**

- Tab navigation (Ongoing, Upcoming, Ended)
- Badge counter untuk jumlah meeting
- Quick start button untuk instant meeting
- Card dengan status live indicator
- Format waktu dengan Carbon

---

#### 9. guru/meeting/join.blade.php

**Lokasi:** `resources/views/guru/meeting/join.blade.php`

**Deskripsi:** Halaman join meeting dengan Jitsi Meet iframe

**Query Blade:**

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meeting->title }} - Kelas Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
    body {
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }

    .meeting-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    #jitsi-container {
        background: #f5f5f5;
    }
    </style>
</head>

<body>
    <div class="container-fluid p-0" style="height: 100vh;">
        <!-- Meeting Header -->
        <div class="meeting-header text-white p-3">
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
                        <form action="{{ route('guru.meeting.end', [$serial->id, $meeting->id]) }}"
                              method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Akhiri meeting ini untuk semua peserta?')">
                                <i class='bx bx-stop-circle me-1'></i>Akhiri
                            </button>
                        </form>
                        <a href="{{ route('guru.meeting', $serial->id) }}"
                           class="btn btn-sm btn-secondary">
                            <i class='bx bx-exit me-1'></i>Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jitsi Meet Container -->
        <div id="jitsi-container" style="height: calc(100vh - 68px);"></div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" style="position: absolute; top: 50%; left: 50%;
             transform: translate(-50%, -50%); text-align: center;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat meeting room...</p>
        </div>
    </div>

    <script src='https://meet.jit.si/external_api.js'></script>
    <script>
    const domain = 'meet.jit.si';
    const options = {
        roomName: '{{ $meeting->room_id }}',
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-container'),
        userInfo: {
            displayName: '{{ $userName }}',
            email: '{{ $userEmail }}'
        },
        configOverwrite: {
            startWithAudioMuted: false,
            startWithVideoMuted: false
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'chat', 'raisehand',
                'videoquality', 'tileview', 'settings'
            ]
        }
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    api.addEventListener('readyToClose', function() {
        window.location.href = "{{ route('guru.meeting', $serial->id) }}";
    });

    api.addEventListener('videoConferenceJoined', function() {
        document.getElementById('loading-indicator').style.display = 'none';
    });
    </script>
</body>
</html>
```

**Data yang Digunakan:**

- `$meeting` - Model OnlineMeeting
- `$userName` - Nama user untuk display di Jitsi
- `$userEmail` - Email user
- `$serial` - Model Serial

**Fitur:**

- Full screen Jitsi Meet iframe
- Header dengan info meeting
- Button akhiri meeting dan keluar
- Loading indicator
- Jitsi External API configuration
- Auto redirect ketika meeting ditutup
- Custom toolbar buttons

**JavaScript:**

- Jitsi Meet External API integration
- Event listeners untuk join dan close
- Dynamic room configuration

---

#### 10. guru/soal/index.blade.php

**Lokasi:** `resources/views/guru/soal/index.blade.php`

**Deskripsi:** Halaman pilih kategori soal

**Query Blade:**

```blade
@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Materi / Soal /</span> Pilih Kategori
    </h4>

    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card h-100 hover-card border-{{ $category['color'] }}">
                    <div class="card-body text-center">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-{{ $category['color'] }}">
                                <i class='bx {{ $category['icon'] }} fs-1'></i>
                            </span>
                        </div>
                        <h5 class="card-title mb-3">{{ $category['name'] }}</h5>
                        <a href="{{ route('guru.soal.list-direct', [$serial->id, $category['id']]) }}"
                           class="btn btn-{{ $category['color'] }} btn-sm w-100">
                            <i class='bx bx-right-arrow-alt me-1'></i>Lihat Soal
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endsection
```

**Data yang Digunakan:**

- `$categories` - Array kategori soal (UH, PTS, PAS, Tambahan)
- `$serial` - Model Serial

**Struktur Categories:**

```php
$categories = [
    [
        'id' => 'ulangan-harian',
        'name' => 'Ulangan Harian',
        'icon' => 'bx-edit',
        'color' => 'primary',
        'type_id' => 1
    ],
    // ... dst
];
```

**Fitur:**

- Dynamic card generation dari array
- Color coding per kategori
- Icon Boxicons
- Hover effect

---

## BLADE SYNTAX REFERENCE

### Directives

| Directive  | Fungsi                 | Contoh                        |
| ---------- | ---------------------- | ----------------------------- |
| `@extends` | Extend layout parent   | `@extends('layouts.guru')`    |
| `@section` | Definisikan section    | `@section('content')`         |
| `@yield`   | Tampilkan section      | `@yield('content')`           |
| `@include` | Include partial view   | `@include('partials.header')` |
| `@if`      | Conditional            | `@if($user->isAdmin())`       |
| `@foreach` | Loop array/collection  | `@foreach($items as $item)`   |
| `@forelse` | Loop dengan fallback   | `@forelse($items as $item)`   |
| `@empty`   | Fallback untuk forelse | `@empty`                      |
| `@csrf`    | CSRF token             | `@csrf`                       |
| `@method`  | Method spoofing        | `@method('DELETE')`           |
| `@error`   | Show validation error  | `@error('field')`             |

### Echo Data

| Syntax                    | Fungsi               | Escape |
| ------------------------- | -------------------- | ------ |
| `{{ $var }}`              | Echo with escape     | Ya     |
| `{!! $var !!}`            | Echo without escape  | Tidak  |
| `{{ $var ?? 'default' }}` | With null coalescing | Ya     |

### Helper Functions

| Helper      | Fungsi                       | Contoh                    |
| ----------- | ---------------------------- | ------------------------- |
| `route()`   | Generate URL from route name | `route('user.show', $id)` |
| `url()`     | Generate full URL            | `url('/home')`            |
| `asset()`   | URL to asset                 | `asset('css/app.css')`    |
| `old()`     | Get old input                | `old('name')`             |
| `session()` | Get session value            | `session('success')`      |

### Components

```blade
<!-- Component -->
<x-alert type="success" :message="$message" />

<!-- Slot -->
<x-card>
    <x-slot name="header">Title</x-slot>
    Content here
</x-card>
```

---

**Dokumen dibuat:** 11 Januari 2026
**Versi:** 1.1
**Sistem:** DashboardGuru Application

<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Guru\AplikasiController;
use App\Http\Controllers\Guru\MateriController;
use App\Http\Controllers\Guru\SoalController;
use App\Http\Controllers\Guru\OnlineClassController;
use App\Http\Controllers\Guru\TugasController;
use App\Http\Controllers\Guru\LaporanHarianController;
use App\Http\Controllers\Guru\KelasController;


/*
|--------------------------------------------------------------------------
| Default Redirect — After Login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/', function () {
    return redirect()->route('guru.aplikasi');
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| MAIN GURU ROUTES (Tanpa prefix /guru)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /* ------------------------------------------------------
     * 1. PILIH APLIKASI
     * ------------------------------------------------------ */
    Route::get('/aplikasi', [AplikasiController::class, 'index'])
        ->name('guru.aplikasi');

    Route::get('/pilih-aplikasi', [AplikasiController::class, 'index'])
        ->name('pilih.aplikasi');

    /* ------------------------------------------------------
     * 2. DASHBOARD APLIKASI
     * ------------------------------------------------------ */
    Route::get('/aplikasi/{serial}', [AplikasiController::class, 'dashboard'])
        ->name('guru.dashboard');


    /* ------------------------------------------------------
     * 3. FITUR DI DALAM APLIKASI
     * Semua akses menggunakan prefix: /aplikasi/{serial}/...
     * ------------------------------------------------------ */

    // Materi
    Route::get('/aplikasi/{serial}/materi', [MateriController::class, 'index'])
        ->name('guru.materi');

    // Soal
    Route::get('/aplikasi/{serial}/soal', [SoalController::class, 'index'])
        ->name('guru.soal');

    // Tugas
    Route::get('/aplikasi/{serial}/tugas', [TugasController::class, 'index'])
        ->name('guru.tugas');

    // Online Class
    Route::get('/aplikasi/{serial}/online-class', [OnlineClassController::class, 'index'])
        ->name('guru.onlineclass');
    Route::get('/aplikasi/{serial}/online-class/create', [OnlineClassController::class, 'create'])
        ->name('guru.onlineclass.create');
    Route::post('/aplikasi/{serial}/online-class', [OnlineClassController::class, 'store'])
        ->name('guru.onlineclass.store');
    Route::get('/aplikasi/{serial}/online-class/{id}/edit', [OnlineClassController::class, 'edit'])
        ->name('guru.onlineclass.edit');
    Route::put('/aplikasi/{serial}/online-class/{id}', [OnlineClassController::class, 'update'])
        ->name('guru.onlineclass.update');
    Route::delete('/aplikasi/{serial}/online-class/{id}', [OnlineClassController::class, 'destroy'])
        ->name('guru.onlineclass.destroy');

    // Laporan Harian - Automatic from task submissions
    Route::get('/aplikasi/{serial}/laporan-harian', [LaporanHarianController::class, 'index'])
        ->name('guru.laporanharian');
    Route::get('/aplikasi/{serial}/laporan-harian/{date}', [LaporanHarianController::class, 'show'])
        ->name('guru.laporanharian.show');
    Route::post('/aplikasi/{serial}/laporan-harian/grade/{taskId}', [LaporanHarianController::class, 'grade'])
        ->name('guru.laporan.grade');

    // Pengaturan - Profile and account settings
    Route::get('/aplikasi/{serial}/pengaturan', [\App\Http\Controllers\Guru\PengaturanController::class, 'index'])
        ->name('guru.pengaturan');
    Route::put('/aplikasi/{serial}/pengaturan/profile', [\App\Http\Controllers\Guru\PengaturanController::class, 'updateProfile'])
        ->name('guru.pengaturan.profile');
    Route::put('/aplikasi/{serial}/pengaturan/password', [\App\Http\Controllers\Guru\PengaturanController::class, 'updatePassword'])
        ->name('guru.pengaturan.password');


    /* ------------------------------------------------------
     * 4. KELAS (List kelas, tambah, hapus, dashboard kelas)
     * ------------------------------------------------------ */
    Route::prefix('/aplikasi/{serial}/kelas')->group(function () {

        Route::get('/', [KelasController::class, 'pilihKelas'])
            ->name('guru.kelas.pilih');

        Route::post('/', [KelasController::class, 'store'])
            ->name('guru.kelas.store');

        Route::delete('/{classroom}', [KelasController::class, 'destroy'])
            ->name('guru.kelas.destroy');

        Route::get('/{classroom}', [KelasController::class, 'dashboard'])
            ->name('guru.kelas.dashboard');
            
        // Student management
        Route::post('/{classroom}/siswa', [KelasController::class, 'storeStudent'])
            ->name('guru.kelas.siswa.store');
        Route::delete('/{classroom}/siswa/{student}', [KelasController::class, 'destroyStudent'])
            ->name('guru.kelas.siswa.destroy');
    });
    
    // =========================
//  MATERI (POST)
// =========================
// MATERI
Route::prefix('aplikasi/{serial}/materi')->group(function() {

    Route::get('/', [MateriController::class, 'index'])->name('guru.materi'); 
    Route::get('/tema/{tema}', [MateriController::class, 'subtema'])->name('guru.materi.tema');
    Route::get('/tema/{tema}/subtema/{subtema}', [MateriController::class, 'list'])->name('guru.materi.list');
    
    // CRUD Operations
    Route::get('/tema/{tema}/subtema/{subtema}/create', [MateriController::class, 'create'])->name('guru.materi.create');
    Route::post('/tema/{tema}/subtema/{subtema}', [MateriController::class, 'store'])->name('guru.materi.store');
    Route::get('/tema/{tema}/subtema/{subtema}/{id}/edit', [MateriController::class, 'edit'])->name('guru.materi.edit');
    Route::put('/tema/{tema}/subtema/{subtema}/{id}', [MateriController::class, 'update'])->name('guru.materi.update');
    Route::delete('/tema/{tema}/subtema/{subtema}/{id}', [MateriController::class, 'destroy'])->name('guru.materi.destroy');

});

// =========================
//  TUGAS (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/tugas')->group(function() {

    Route::get('/', [TugasController::class, 'index'])->name('guru.tugas'); 
    Route::get('/tema/{tema}', [TugasController::class, 'subtema'])->name('guru.tugas.tema');
    Route::get('/tema/{tema}/subtema/{subtema}', [TugasController::class, 'list'])->name('guru.tugas.list');
    
    // CRUD Operations
    Route::get('/tema/{tema}/subtema/{subtema}/create', [TugasController::class, 'create'])->name('guru.tugas.create');
    Route::post('/tema/{tema}/subtema/{subtema}', [TugasController::class, 'store'])->name('guru.tugas.store');
    Route::get('/tema/{tema}/subtema/{subtema}/{id}', [TugasController::class, 'show'])->name('guru.tugas.show');
    Route::get('/tema/{tema}/subtema/{subtema}/{id}/edit', [TugasController::class, 'edit'])->name('guru.tugas.edit');
    Route::put('/tema/{tema}/subtema/{subtema}/{id}', [TugasController::class, 'update'])->name('guru.tugas.update');
    Route::delete('/tema/{tema}/subtema/{subtema}/{id}', [TugasController::class, 'destroy'])->name('guru.tugas.destroy');

});

// =========================
//  SOAL (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/soal')->group(function() {

    Route::get('/', [SoalController::class, 'index'])->name('guru.soal'); 
    Route::get('/{category}', [SoalController::class, 'subtema'])->name('guru.soal.tema');
    Route::get('/{category}/{tema}', [SoalController::class, 'list'])->name('guru.soal.list');
    
    // CRUD Operations
    Route::get('/{category}/{tema}/create', [SoalController::class, 'create'])->name('guru.soal.create');
    Route::post('/{category}/{tema}', [SoalController::class, 'store'])->name('guru.soal.store');
    Route::get('/{category}/{tema}/{id}', [SoalController::class, 'show'])->name('guru.soal.show');
    Route::get('/{category}/{tema}/{id}/edit', [SoalController::class, 'edit'])->name('guru.soal.edit');
    Route::put('/{category}/{tema}/{id}', [SoalController::class, 'update'])->name('guru.soal.update');
    Route::delete('/{category}/{tema}/{id}', [SoalController::class, 'destroy'])->name('guru.soal.destroy');
    
    // Grading
    Route::post('/grade/{taskId}', [SoalController::class, 'grade'])->name('guru.soal.grade');

});

// =========================
//  ONLINE CLASS (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/online-class')->group(function() {

    Route::get('/', [OnlineClassController::class, 'index'])->name('guru.onlineclass'); 
    Route::get('/mapel/{mapel}', [OnlineClassController::class, 'tema'])->name('guru.onlineclass.tema');
    Route::get('/mapel/{mapel}/tema/{tema}', [OnlineClassController::class, 'subtema'])->name('guru.onlineclass.subtema');
    Route::get('/mapel/{mapel}/tema/{tema}/subtema/{subtema}', [OnlineClassController::class, 'list'])->name('guru.onlineclass.list');
    
    // CRUD Operations
    Route::get('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/create', [OnlineClassController::class, 'create'])->name('guru.onlineclass.create');
    Route::post('/mapel/{mapel}/tema/{tema}/subtema/{subtema}', [OnlineClassController::class, 'store'])->name('guru.onlineclass.store');
    Route::get('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/{id}/edit', [OnlineClassController::class, 'edit'])->name('guru.onlineclass.edit');
    Route::put('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/{id}', [OnlineClassController::class, 'update'])->name('guru.onlineclass.update');
    Route::delete('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/{id}', [OnlineClassController::class, 'destroy'])->name('guru.onlineclass.destroy');

});

// =========================
//  LAPORAN HARIAN (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/laporan-harian')->group(function() {

    Route::get('/', [LaporanHarianController::class, 'index'])->name('guru.laporanharian'); 
    Route::get('/mapel/{mapel}', [LaporanHarianController::class, 'tema'])->name('guru.laporanharian.tema');
    Route::get('/mapel/{mapel}/tema/{tema}', [LaporanHarianController::class, 'subtema'])->name('guru.laporanharian.subtema');
    Route::get('/mapel/{mapel}/tema/{tema}/subtema/{subtema}', [LaporanHarianController::class, 'list'])->name('guru.laporanharian.list');
    
    // CRUD Operations
    Route::get('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/create', [LaporanHarianController::class, 'create'])->name('guru.laporanharian.create');
    Route::post('/mapel/{mapel}/tema/{tema}/subtema/{subtema}', [LaporanHarianController::class, 'store'])->name('guru.laporanharian.store');
    Route::get('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/{id}/edit', [LaporanHarianController::class, 'edit'])->name('guru.laporanharian.edit');
    Route::put('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/{id}', [LaporanHarianController::class, 'update'])->name('guru.laporanharian.update');
    Route::delete('/mapel/{mapel}/tema/{tema}/subtema/{subtema}/{id}', [LaporanHarianController::class, 'destroy'])->name('guru.laporanharian.destroy');

});




});


require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| BACKWARD COMPATIBILITY ROUTES (/guru/aplikasi/*)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('guru')->group(function () {

    // Lama: /guru/aplikasi → Baru: /aplikasi
    Route::get('aplikasi', fn() => redirect('/aplikasi'));

    // Lama: /guru/aplikasi/{serial} → Baru: /aplikasi/{serial}
    Route::get('aplikasi/{serial}', fn($serial) => redirect("/aplikasi/$serial"));

    // Lama: /guru/aplikasi/{serial}/xxx → Baru: /aplikasi/{serial}/xxx
    $submenus = [
        'materi',
        'soal',
        'tugas',
        'online-class',
        'laporan-harian',
        'kelas'
    ];

    foreach ($submenus as $m) {
        Route::get("aplikasi/{serial}/{$m}/{extra?}", function ($serial, $extra = null) use ($m) {
            $url = "/aplikasi/$serial/$m" . ($extra ? "/$extra" : "");
            return redirect($url);
        });
    }
});
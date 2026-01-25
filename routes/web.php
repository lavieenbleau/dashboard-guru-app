<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Guru\AplikasiController;
use App\Http\Controllers\Guru\MateriController;
use App\Http\Controllers\Guru\SoalController;
use App\Http\Controllers\Guru\OnlineClassController;
use App\Http\Controllers\Guru\OnlineMeetingController;
use App\Http\Controllers\Guru\TugasController;
use App\Http\Controllers\Guru\LaporanHarianController;
use App\Http\Controllers\Guru\KelasController;
use App\Http\Controllers\Guru\RekapNilaiController;


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

    // Laporan Harian - Automatic from task submissions
    Route::get('/aplikasi/{serial}/laporan-harian', [LaporanHarianController::class, 'index'])
        ->name('guru.laporanharian');
    Route::get('/aplikasi/{serial}/laporan-harian/{date}', [LaporanHarianController::class, 'show'])
        ->name('guru.laporanharian.show');
    Route::post('/aplikasi/{serial}/laporan-harian/grade/{taskId}', [LaporanHarianController::class, 'grade'])
        ->name('guru.laporan.grade');

    // Rekap Nilai
    Route::get('/aplikasi/{serial}/rekap-nilai', [RekapNilaiController::class, 'index'])
        ->name('guru.rekapnilai');
    Route::get('/aplikasi/{serial}/rekap-nilai/kelas/{classroom}', [RekapNilaiController::class, 'showClass'])
        ->name('guru.rekapnilai.kelas');
    Route::get('/aplikasi/{serial}/rekap-nilai/kelas/{classroom}/download-pdf', [RekapNilaiController::class, 'downloadClassPdf'])
        ->name('guru.rekapnilai.kelas.pdf');
    Route::get('/aplikasi/{serial}/rekap-nilai/kelas/{classroom}/siswa/{student}', [RekapNilaiController::class, 'showStudent'])
        ->name('guru.rekapnilai.siswa');
    Route::get('/aplikasi/{serial}/rekap-nilai/kelas/{classroom}/siswa/{student}/download-pdf', [RekapNilaiController::class, 'downloadStudentPdf'])
        ->name('guru.rekapnilai.siswa.pdf');

    // Pengaturan - Profile and account settings
    Route::get('/aplikasi/{serial}/pengaturan', [\App\Http\Controllers\Guru\PengaturanController::class, 'index'])
        ->name('guru.pengaturan');
    Route::put('/aplikasi/{serial}/pengaturan/profile', [\App\Http\Controllers\Guru\PengaturanController::class, 'updateProfile'])
        ->name('guru.pengaturan.profile');
    Route::put('/aplikasi/{serial}/pengaturan/password', [\App\Http\Controllers\Guru\PengaturanController::class, 'updatePassword'])
        ->name('guru.pengaturan.password');
    Route::put('/aplikasi/{serial}/pengaturan/field', [\App\Http\Controllers\Guru\PengaturanController::class, 'updateField'])
        ->name('guru.pengaturan.updateField');


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
        
        // CSV Import
        Route::post('/{classroom}/siswa/import', [KelasController::class, 'importStudents'])
            ->name('guru.kelas.siswa.import');
        Route::get('/siswa/template-csv', [KelasController::class, 'downloadTemplate'])
            ->name('guru.kelas.siswa.template');
    });
    
    // =========================
//  MATERI (POST)
// =========================
// MATERI
Route::prefix('aplikasi/{serial}/materi')->group(function() {

    Route::get('/', [MateriController::class, 'index'])->name('guru.materi');
    
    // Admin Materials
    Route::get('/admin', [MateriController::class, 'admin'])->name('guru.materi.admin');
    Route::get('/admin/mapel/{mapel}', [MateriController::class, 'adminLessons'])->name('guru.materi.admin.lessons');
    Route::post('/admin/share/{lessonItem}', [MateriController::class, 'shareAdminLesson'])->name('guru.materi.admin.share');
    
    // Custom Materials (Guru's own) - pilih mapel langsung
    Route::get('/custom', [MateriController::class, 'custom'])->name('guru.materi.custom');
    Route::post('/share/{post}', [MateriController::class, 'shareCustomMateri'])->name('guru.materi.share');
    
    // List materi by mapel
    Route::get('/mapel/{mapel}', [MateriController::class, 'listByMapel'])->name('guru.materi.mapel');
    
    // CRUD Materi
    Route::get('/mapel/{mapel}/create', [MateriController::class, 'createMateri'])->name('guru.materi.create');
    Route::post('/mapel/{mapel}', [MateriController::class, 'storeMateri'])->name('guru.materi.store');
    Route::get('/mapel/{mapel}/{id}', [MateriController::class, 'showDetail'])->name('guru.materi.detail');
    Route::get('/mapel/{mapel}/{id}/edit', [MateriController::class, 'editMateri'])->name('guru.materi.edit');
    Route::put('/mapel/{mapel}/{id}', [MateriController::class, 'updateMateri'])->name('guru.materi.update');
    Route::delete('/mapel/{mapel}/{id}', [MateriController::class, 'destroyMateri'])->name('guru.materi.destroy');
    
    // Comments & Discussion
    Route::post('/mapel/{mapel}/{id}/comment', [MateriController::class, 'storeComment'])->name('guru.materi.comment.store');
    Route::post('/mapel/{mapel}/{id}/comment/{comment}/reply', [MateriController::class, 'storeReply'])->name('guru.materi.comment.reply');
    Route::delete('/mapel/{mapel}/{id}/comment/{comment}', [MateriController::class, 'deleteComment'])->name('guru.materi.comment.delete');
    Route::delete('/mapel/{mapel}/{id}/reply/{reply}', [MateriController::class, 'deleteReply'])->name('guru.materi.reply.delete');

});

// =========================
//  TUGAS (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/tugas')->group(function() {

    Route::get('/', [TugasController::class, 'index'])->name('guru.tugas'); 
    Route::get('/mapel/{mapel}', [TugasController::class, 'listByMapel'])->name('guru.tugas.mapel');
    
    // CRUD Operations
    Route::get('/mapel/{mapel}/create', [TugasController::class, 'create'])->name('guru.tugas.create');
    Route::post('/mapel/{mapel}', [TugasController::class, 'store'])->name('guru.tugas.store');
    Route::get('/mapel/{mapel}/{id}', [TugasController::class, 'show'])->name('guru.tugas.show');
    Route::get('/mapel/{mapel}/{id}/edit', [TugasController::class, 'edit'])->name('guru.tugas.edit');
    Route::put('/mapel/{mapel}/{id}', [TugasController::class, 'update'])->name('guru.tugas.update');
    Route::delete('/mapel/{mapel}/{id}', [TugasController::class, 'destroy'])->name('guru.tugas.destroy');

    // Comments & Discussion
    Route::post('/mapel/{mapel}/{id}/comment', [TugasController::class, 'storeComment'])->name('guru.tugas.comment.store');
    Route::post('/mapel/{mapel}/{id}/comment/{comment}/reply', [TugasController::class, 'storeReply'])->name('guru.tugas.comment.reply');
    Route::delete('/mapel/{mapel}/{id}/comment/{comment}', [TugasController::class, 'deleteComment'])->name('guru.tugas.comment.delete');
    Route::delete('/mapel/{mapel}/{id}/reply/{reply}', [TugasController::class, 'deleteReply'])->name('guru.tugas.reply.delete');

});

// =========================
//  SOAL (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/soal')->group(function() {

    Route::get('/', [SoalController::class, 'index'])->name('guru.soal'); 
    
    // Type-based routes (most specific first)
    Route::get('/type/{type}', [SoalController::class, 'category'])->name('guru.soal.category');
    Route::get('/type/{type}/tipe/{exerciseTypeId}', [SoalController::class, 'listByType'])->name('guru.soal.list-by-type');
    Route::post('/type/{type}/tipe/{exerciseTypeId}/{id}/share-single', [SoalController::class, 'shareSingle'])->name('guru.soal.share-single');
    Route::post('/type/{type}/tipe/{exerciseTypeId}/bulk-share', [SoalController::class, 'bulkShare'])->name('guru.soal.bulk-share');
    
    // Grading route (specific)
    Route::post('/grade/{taskId}', [SoalController::class, 'grade'])->name('guru.soal.grade');
    
    // Custom Soal Tambahan CRUD (specific routes first)
    Route::get('/tambahan/create', [SoalController::class, 'createCustom'])->name('guru.soal.create-custom');
    Route::post('/tambahan', [SoalController::class, 'storeCustom'])->name('guru.soal.store-custom');
    Route::get('/tambahan/{id}/edit', [SoalController::class, 'editCustom'])->name('guru.soal.edit-custom');
    Route::put('/tambahan/{id}', [SoalController::class, 'updateCustom'])->name('guru.soal.update-custom');
    Route::delete('/tambahan/{id}', [SoalController::class, 'destroyCustom'])->name('guru.soal.destroy-custom');
    
    // CRUD Operations (more specific routes first)
    Route::get('/{category}/{tema}/create', [SoalController::class, 'create'])->name('guru.soal.create');
    Route::get('/{category}/{tema}/{id}/edit', [SoalController::class, 'edit'])->name('guru.soal.edit');
    Route::get('/{category}/{tema}/{id}', [SoalController::class, 'show'])->name('guru.soal.show');
    Route::post('/{category}/{tema}', [SoalController::class, 'store'])->name('guru.soal.store');
    Route::put('/{category}/{tema}/{id}', [SoalController::class, 'update'])->name('guru.soal.update');
    Route::delete('/{category}/{tema}/{id}', [SoalController::class, 'destroy'])->name('guru.soal.destroy');
    
    // OLD Routes (backward compatibility)
    Route::get('/{category}/pilih', [SoalController::class, 'categorySelect'])->name('guru.soal.category-select');
    Route::get('/{category}/old', [SoalController::class, 'subtema'])->name('guru.soal.tema');
    Route::get('/{category}/{tema}', [SoalController::class, 'list'])->name('guru.soal.list');
    
    // Share routes (specific before general category route)
    Route::post('/{category}/{id}/share', [SoalController::class, 'shareSingleCategory'])->name('guru.soal.share-direct');
    Route::post('/{category}/bulk-share', [SoalController::class, 'bulkShareCategory'])->name('guru.soal.bulk-share-direct');
    
    // Direct access to exercises (admin only) - LAST because it's most general
    Route::get('/{category}', [SoalController::class, 'listByCategory'])->name('guru.soal.list-direct');

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
//  KELAS ONLINE (JITSI MEET)
// =========================
Route::prefix('aplikasi/{serial}/meeting')->group(function() {
    Route::get('/', [OnlineMeetingController::class, 'index'])->name('guru.meeting');
    Route::post('/quick-start', [OnlineMeetingController::class, 'quickStart'])->name('guru.meeting.quick-start');
    Route::get('/create', [OnlineMeetingController::class, 'create'])->name('guru.meeting.create');
    Route::post('/', [OnlineMeetingController::class, 'store'])->name('guru.meeting.store');
    Route::get('/{id}', [OnlineMeetingController::class, 'show'])->name('guru.meeting.show');
    Route::get('/{id}/join', [OnlineMeetingController::class, 'join'])->name('guru.meeting.join');
    Route::get('/{id}/edit', [OnlineMeetingController::class, 'edit'])->name('guru.meeting.edit');
    Route::put('/{id}', [OnlineMeetingController::class, 'update'])->name('guru.meeting.update');
    Route::delete('/{id}', [OnlineMeetingController::class, 'destroy'])->name('guru.meeting.destroy');
    Route::post('/{id}/end', [OnlineMeetingController::class, 'end'])->name('guru.meeting.end');
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
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
| Upload Image Global (Soal, Materi, Tugas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->post('/upload/image', [\App\Http\Controllers\ImageUploadController::class, 'upload'])->name('upload.image');

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
    Route::post('/aplikasi/activate-serial', [AplikasiController::class, 'activateSerial'])
        ->name('guru.aplikasi.activate');

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
    Route::get('/aplikasi/{serial}/laporan-harian/review/{task}', [LaporanHarianController::class, 'review'])
        ->name('guru.laporanharian.review');
    Route::post('/aplikasi/{serial}/laporan-harian/grade/{taskId}', [LaporanHarianController::class, 'grade'])
        ->name('guru.laporan.grade');

    // Monitoring Quiz
    // Monitoring Quiz Baru
    Route::get('/monitoring-quiz', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'indexClasses'])->name('guru.monitoring-quiz');
    Route::get('/monitoring-quiz/{kelas_name}', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'indexProducts'])->name('guru.monitoring-quiz.products');
    Route::get('/monitoring-quiz/{kelas_name}/{serial_id}', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'monitoringStudent'])->name('guru.monitoring-quiz.students');
    Route::get('/monitoring-quiz/{kelas_name}/{serial_id}/data', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'dataTable'])->name('guru.monitoring-quiz.data');
    Route::get('/monitoring-quiz/{kelas_name}/{serial_id}/detail/{student_id}/{exercise_id}', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'detail'])->name('guru.monitoring-quiz.detail');
    Route::post('/monitoring-quiz/{kelas_name}/{serial_id}/reminder', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'sendReminder'])->name('guru.monitoring-quiz.reminder');
    Route::get('/monitoring-quiz/{kelas_name}/{serial_id}/export/csv', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'exportCsv'])->name('guru.monitoring-quiz.export-csv');
    Route::get('/monitoring-quiz/{kelas_name}/{serial_id}/export/pdf', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'exportPdf'])->name('guru.monitoring-quiz.export-pdf');

    // Rekap Nilai
    Route::get('/aplikasi/{serial}/rekap-nilai', [RekapNilaiController::class, 'index'])
        ->name('guru.rekapnilai');
    Route::get('/aplikasi/{serial}/rekap-nilai/kelas/{classroom}', [RekapNilaiController::class, 'showClass'])
        ->name('guru.rekapnilai.kelas');
    Route::get('/aplikasi/{serial}/rekap-nilai/kelas/{classroom}/lesson/{lesson_id}', [RekapNilaiController::class, 'showLesson'])
        ->name('guru.rekapnilai.lesson');
    Route::get('/aplikasi/{serial}/rekap-nilai/kelas/{classroom}/lesson/{lesson_id}/download-pdf', [RekapNilaiController::class, 'downloadClassPdf'])
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
        Route::put('/{classroom}/siswa/{student}', [KelasController::class, 'updateStudent'])
            ->name('guru.kelas.siswa.update');
        Route::put('/{classroom}/siswa/{student}/password', [KelasController::class, 'updateStudentPassword'])
            ->name('guru.kelas.siswa.update-password');
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
    Route::get('/admin/mapel/{mapel_id}', [MateriController::class, 'adminMapel'])->name('guru.materi.admin.mapel');
    Route::get('/admin/lesson/{lesson}', [MateriController::class, 'adminLessons'])->name('guru.materi.admin.lessons');
    Route::post('/admin/share/{lessonItem}', [MateriController::class, 'shareAdminLesson'])->name('guru.materi.admin.share');
    
    // Custom Materials (Guru's own) - pilih mapel langsung
    Route::get('/custom', [MateriController::class, 'custom'])->name('guru.materi.custom');
    Route::get('/custom/mapel/{mapel_id}', [MateriController::class, 'customMapel'])->name('guru.materi.custom.mapel');
    Route::post('/share/{post}', [MateriController::class, 'shareCustomMateri'])->name('guru.materi.share');
    
    // List materi by lesson
    Route::get('/lesson/{lesson}', [MateriController::class, 'listByLesson'])->name('guru.materi.lesson');
    
    // CRUD Materi
    Route::get('/lesson/{lesson}/create', [MateriController::class, 'createMateri'])->name('guru.materi.create');
    Route::post('/lesson/{lesson}', [MateriController::class, 'storeMateri'])->name('guru.materi.store');
    Route::get('/lesson/{lesson}/{id}', [MateriController::class, 'showDetail'])->name('guru.materi.detail');
    Route::get('/lesson/{lesson}/{id}/edit', [MateriController::class, 'editMateri'])->name('guru.materi.edit');
    Route::put('/lesson/{lesson}/{id}', [MateriController::class, 'updateMateri'])->name('guru.materi.update');
    Route::delete('/lesson/{lesson}/{id}', [MateriController::class, 'destroyMateri'])->name('guru.materi.destroy');
    
    // Comments & Discussion
    Route::post('/lesson/{lesson}/{id}/comment', [MateriController::class, 'storeComment'])->name('guru.materi.comment.store');
    Route::post('/lesson/{lesson}/{id}/comment/{comment}/reply', [MateriController::class, 'storeReply'])->name('guru.materi.comment.reply');
    Route::delete('/lesson/{lesson}/{id}/comment/{comment}', [MateriController::class, 'deleteComment'])->name('guru.materi.comment.delete');
    Route::delete('/lesson/{lesson}/{id}/reply/{reply}', [MateriController::class, 'deleteReply'])->name('guru.materi.reply.delete');

});

// =========================
//  TUGAS (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/tugas')->group(function() {

    Route::get('/', [TugasController::class, 'index'])->name('guru.tugas');
    Route::get('/mapel/{mapel_id}', [TugasController::class, 'mapel'])->name('guru.tugas.mapel');
    Route::get('/lesson/{lesson}', [TugasController::class, 'listByLesson'])->name('guru.tugas.lesson');
    
    // CRUD Tugas
    Route::get('/lesson/{lesson}/create', [TugasController::class, 'create'])->name('guru.tugas.create');
    Route::post('/lesson/{lesson}', [TugasController::class, 'store'])->name('guru.tugas.store');
    Route::get('/lesson/{lesson}/{id}', [TugasController::class, 'show'])->name('guru.tugas.show');
    Route::get('/lesson/{lesson}/{id}/edit', [TugasController::class, 'edit'])->name('guru.tugas.edit');
    Route::put('/lesson/{lesson}/{id}', [TugasController::class, 'update'])->name('guru.tugas.update');
    Route::put('/lesson/{lesson}/{id}/classroom', [TugasController::class, 'updateClassroom'])->name('guru.tugas.update-classroom');
    Route::delete('/lesson/{lesson}/{id}', [TugasController::class, 'destroy'])->name('guru.tugas.destroy');
    
    // Comments & Discussion
    Route::post('/lesson/{lesson}/{id}/comment', [TugasController::class, 'storeComment'])->name('guru.tugas.comment.store');
    Route::post('/lesson/{lesson}/{id}/comment/{comment}/reply', [TugasController::class, 'storeReply'])->name('guru.tugas.comment.reply');
    Route::delete('/lesson/{lesson}/{id}/comment/{comment}', [TugasController::class, 'deleteComment'])->name('guru.tugas.comment.delete');
    Route::delete('/lesson/{lesson}/{id}/reply/{reply}', [TugasController::class, 'deleteReply'])->name('guru.tugas.reply.delete');

});

// =========================
//  SOAL (LESSONS)
// =========================
Route::prefix('aplikasi/{serial}/soal')->group(function() {

    // 1. First entry: List Mapel
    Route::get('/', [SoalController::class, 'index'])->name('guru.soal'); 
    
    // 2. Second entry: List Lessons for Mapel
    Route::get('/mapel/{mapel_id}', [SoalController::class, 'mapel'])->name('guru.soal.mapel');
    
    // 2. Second entry: List Categories for a Lesson
    Route::get('/lesson/{lesson}', [SoalController::class, 'categories'])->name('guru.soal.lesson');

    // 3. Category exercises list for a lesson
    Route::get('/lesson/{lesson}/kategori/{category}', [SoalController::class, 'listByCategory'])->name('guru.soal.list-direct');

    // Custom Soal Tambahan CRUD for a lesson
    Route::get('/lesson/{lesson}/tambahan/create', [SoalController::class, 'createCustom'])->name('guru.soal.create-custom');
    Route::post('/lesson/{lesson}/tambahan', [SoalController::class, 'storeCustom'])->name('guru.soal.store-custom');
    Route::get('/lesson/{lesson}/tambahan/{id}/edit', [SoalController::class, 'editCustom'])->name('guru.soal.edit-custom');
    Route::put('/lesson/{lesson}/tambahan/{id}', [SoalController::class, 'updateCustom'])->name('guru.soal.update-custom');
    Route::post('/lesson/{lesson}/tambahan/{id}/add-item', [SoalController::class, 'storeCustomItem'])->name('guru.soal.store-custom-item');
    Route::delete('/lesson/{lesson}/tambahan/{id}', [SoalController::class, 'destroyCustom'])->name('guru.soal.destroy-custom');
    
    // AI Question Generator Routes
    Route::get('/lesson/{lesson}/ai-generator', [SoalController::class, 'aiGenerator'])->name('guru.soal.ai-generator');
    Route::get('/lesson/{lesson}/ai-material/{materialId}/read', [SoalController::class, 'readUploadedMaterial'])->name('guru.soal.ai-material.read');
    Route::post('/lesson/{lesson}/ai-generate', [SoalController::class, 'generateWithAI'])->name('guru.soal.ai-generate');
    Route::get('/lesson/{lesson}/ai-preview', [SoalController::class, 'aiPreview'])->name('guru.soal.ai-preview');
    Route::post('/lesson/{lesson}/ai-save', [SoalController::class, 'saveAIQuestions'])->name('guru.soal.ai-save');
    
    // View exercise (read-only)
    Route::get('/lesson/{lesson}/view/{exerciseId}', [SoalController::class, 'viewExercise'])->name('guru.soal.view-exercise');
    
    // Hasil Pengerjaan Siswa
    Route::get('/lesson/{lesson}/view/{exerciseId}/results', [SoalController::class, 'studentResults'])->name('guru.soal.student-results');
    Route::get('/lesson/{lesson}/view/{exerciseId}/results/{studentId}', [SoalController::class, 'studentAnswerDetail'])->name('guru.soal.student-answer-detail');
    Route::post('/lesson/{lesson}/view/{exerciseId}/results/{studentId}', [SoalController::class, 'saveManualGrade'])->name('guru.soal.save-manual-grade');
    
    // Grading route
    Route::post('/lesson/{lesson}/grade/{taskId}', [SoalController::class, 'grade'])->name('guru.soal.grade');

    // Share routes
    Route::post('/lesson/{lesson}/kategori/{category}/{id}/share', [SoalController::class, 'shareSingleCategory'])->name('guru.soal.share-direct');
    Route::post('/lesson/{lesson}/kategori/{category}/bulk-share', [SoalController::class, 'bulkShareCategory'])->name('guru.soal.bulk-share-direct');

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

// Quiz Activity Client Tracking API
Route::post('/api/quiz-activity/log', [\App\Http\Controllers\Guru\QuizMonitoringController::class, 'track'])->name('quiz-activity.track');
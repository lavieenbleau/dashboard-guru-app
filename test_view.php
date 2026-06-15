<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$serial = App\Models\Serial::find(1);
$classroom = App\Models\Classroom::find(1);
$selectedLesson = App\Models\Lesson::find(5);

$rekapData = [];
$detailColumns = [
    'tasks' => collect(),
    'akm' => collect(),
    'uh' => collect(),
    'pts' => collect(),
    'pas' => collect()
];
$stats = [
    'total_siswa' => 45,
    'sudah_dinilai' => 0,
    'belum_dinilai' => 45,
    'rata_kelas' => 0,
    'tertinggi' => 0,
    'terendah' => 0
];
$detailAverages = [];
$students = collect();

$html = view('guru.rekap-nilai.show-class', compact('serial', 'classroom', 'selectedLesson', 'rekapData', 'detailColumns', 'stats', 'detailAverages', 'students'))->render();
file_put_contents('test_view.html', $html);

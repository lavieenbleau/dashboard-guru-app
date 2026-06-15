<?php
$content = file_get_contents('app/Http/Controllers/Guru/RekapNilaiController.php');

$search = <<<PHP
            \$rekapData[] = [
                'student' => \$student,
                'tugas' => ['avg' => \$rataTugas, 'count' => \$tugas['count']],
                'akm' => ['avg' => \$rataAKM, 'count' => \$akm['count']],
                'uh' => ['avg' => \$rataUH, 'count' => \$uh['count']],
                'pts' => ['avg' => \$rataPTS, 'count' => \$pts['count']],
                'pas' => ['avg' => \$rataPAS, 'count' => \$pas['count']],
                'nilai_akhir' => \$nilaiAkhir,
                'detail' => \$studentDetails
            ];
PHP;

$replace = <<<PHP
            \$rekapData[] = [
                'student' => \$student,
                'tugas' => ['avg' => \$rataTugas, 'count' => \$tugas['count']],
                'akm' => ['avg' => \$rataAKM, 'count' => \$akm['count']],
                'uh' => ['avg' => \$rataUH, 'count' => \$uh['count']],
                'pts' => ['avg' => \$rataPTS, 'count' => \$pts['count']],
                'pas' => ['avg' => \$rataPAS, 'count' => \$pas['count']],
                'nilai_akhir' => \$nilaiAkhir,
                'detail' => \$studentDetails
            ];

            // TAHAP 3: Build clean array
            \$cleanStudentDetails[] = [
                'id' => \$student->id,
                'name' => \$student->name,
                'nis' => \$student->nis,
                'nilai_akhir' => \$nilaiAkhir,
                'tugas' => \$studentDetails['tasks'] ?? [],
                'akm' => \$studentDetails['akm'] ?? [],
                'uh' => \$studentDetails['uh'] ?? [],
                'pts' => \$studentDetails['pts'] ?? [],
                'pas' => \$studentDetails['pas'] ?? []
            ];
PHP;

$content = str_replace($search, $replace, $content);

// Add initialization
$content = str_replace("\$rekapData = [];", "\$rekapData = [];\n        \$cleanStudentDetails = [];", $content);

// Return variable
$content = str_replace("return view('guru.rekap-nilai.show-class', compact('serial', 'classroom', 'students', 'selectedLesson', 'rekapData', 'stats', 'detailColumns', 'detailAverages'));", "return view('guru.rekap-nilai.show-class', compact('serial', 'classroom', 'students', 'selectedLesson', 'rekapData', 'stats', 'detailColumns', 'detailAverages', 'cleanStudentDetails'));", $content);

file_put_contents('app/Http/Controllers/Guru/RekapNilaiController.php', $content);
echo "Controller patched for Tahap 3 & 4.\n";

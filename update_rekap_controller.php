<?php

$controllerPath = __DIR__ . '/app/Http/Controllers/Guru/RekapNilaiController.php';
$content = file_get_contents($controllerPath);

// Define the new showLesson method
$newShowLesson = <<<'EOD'
    public function showLesson($serial, $classroomId, $lessonId)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $selectedLesson = Lesson::findOrFail($lessonId);
        
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();

        $validPostIds = Post::where('serial_id', $serial->id)
                ->where('category', 'like', '%"lesson_id":' . $selectedLesson->id . '%')
                ->where('is_task', 1)
                ->where(function($q) use ($classroom) {
                    $q->whereNull('classroom_id')
                      ->orWhere('classroom_id', $classroom->id);
                })->pluck('id');

        $guruExerciseIds = Exercise::where('lesson_id', $selectedLesson->id)
                ->where('is_admin', 0)
                ->pluck('id');

        $adminExerciseIds = Exercise::whereHas('lesson', function($q) use ($selectedLesson) {
                    $q->where('mapel_id', $selectedLesson->mapel_id)
                      ->where('category', Lesson::CATEGORY_SOAL);
                })
                ->where('is_admin', 1)
                ->pluck('id');

        $validExerciseIds = $guruExerciseIds->concat($adminExerciseIds)->unique();

        $allTasks = Task::whereIn('student_id', $students->pluck('id'))
            ->whereIn('post_id', $validPostIds)
            ->get()
            ->groupBy('student_id');

        $allExPoints = ExercisePoint::whereIn('student_id', $students->pluck('id'))
            ->whereIn('exercise_id', $validExerciseIds)
            ->with('exercise.exerciseType')
            ->get()
            ->groupBy('student_id');

        $rekapData = [];
        foreach ($students as $student) {
            $sTasks = $allTasks->get($student->id, collect());
            $sExPoints = $allExPoints->get($student->id, collect());

            $tugas = ['sum' => 0, 'count' => 0];
            $akm = ['sum' => 0, 'count' => 0];
            $uh = ['sum' => 0, 'count' => 0];
            $pts = ['sum' => 0, 'count' => 0];
            $pas = ['sum' => 0, 'count' => 0];

            foreach ($sTasks as $task) {
                if (!is_null($task->point)) {
                    $tugas['sum'] += $task->point;
                    $tugas['count']++;
                }
            }

            foreach ($sExPoints as $ex) {
                if (!is_null($ex->exercise_point)) {
                    $typeName = $ex->exercise && $ex->exercise->exerciseType ? strtolower($ex->exercise->exerciseType->name) : '';
                    if (str_contains($typeName, 'akm')) {
                        $akm['sum'] += $ex->exercise_point;
                        $akm['count']++;
                    } elseif (str_contains($typeName, 'ulangan harian')) {
                        $uh['sum'] += $ex->exercise_point;
                        $uh['count']++;
                    } elseif (str_contains($typeName, 'pts')) {
                        $pts['sum'] += $ex->exercise_point;
                        $pts['count']++;
                    } elseif (str_contains($typeName, 'pas')) {
                        $pas['sum'] += $ex->exercise_point;
                        $pas['count']++;
                    }
                }
            }

            $rataTugas = $tugas['count'] > 0 ? round($tugas['sum'] / $tugas['count'], 1) : null;
            $rataAKM = $akm['count'] > 0 ? round($akm['sum'] / $akm['count'], 1) : null;
            $rataUH = $uh['count'] > 0 ? round($uh['sum'] / $uh['count'], 1) : null;
            $rataPTS = $pts['count'] > 0 ? round($pts['sum'] / $pts['count'], 1) : null;
            $rataPAS = $pas['count'] > 0 ? round($pas['sum'] / $pas['count'], 1) : null;

            $categories = [$rataTugas, $rataAKM, $rataUH, $rataPTS, $rataPAS];
            $filled = collect($categories)->filter(fn($v) => !is_null($v));
            $nilaiAkhir = $filled->count() ? round($filled->avg(), 1) : null;

            $rekapData[] = [
                'student' => $student,
                'tugas' => ['avg' => $rataTugas, 'count' => $tugas['count']],
                'akm' => ['avg' => $rataAKM, 'count' => $akm['count']],
                'uh' => ['avg' => $rataUH, 'count' => $uh['count']],
                'pts' => ['avg' => $rataPTS, 'count' => $pts['count']],
                'pas' => ['avg' => $rataPAS, 'count' => $pas['count']],
                'nilai_akhir' => $nilaiAkhir
            ];
        }

        $stats = [
            'total_siswa' => $students->count(),
            'sudah_dinilai' => collect($rekapData)->filter(fn($s) => !is_null($s['nilai_akhir']))->count(),
            'belum_dinilai' => collect($rekapData)->filter(fn($s) => is_null($s['nilai_akhir']))->count(),
            'rata_kelas' => 0,
            'tertinggi' => null,
            'terendah' => null,
        ];
        
        $validAkhir = collect($rekapData)->pluck('nilai_akhir')->filter(fn($v) => !is_null($v));
        if ($validAkhir->count() > 0) {
            $stats['rata_kelas'] = round($validAkhir->avg(), 1);
            $stats['tertinggi'] = $validAkhir->max();
            $stats['terendah'] = $validAkhir->min();
        }

        return view('guru.rekap-nilai.show-class', compact('serial', 'classroom', 'students', 'selectedLesson', 'rekapData', 'stats'));
    }
EOD;

// Replace showLesson method (from line 51 to 220 in the original file)
$startShowLesson = strpos($content, 'public function showLesson');
$endShowLesson = strpos($content, 'public function downloadClassPdf');
if ($startShowLesson !== false && $endShowLesson !== false) {
    $content = substr_replace($content, $newShowLesson . "\n\n    ", $startShowLesson, $endShowLesson - $startShowLesson);
}

// Define the new downloadClassPdf method
$newDownloadClassPdf = <<<'EOD'
    public function downloadClassPdf($serial, $classroomId, $lessonId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $selectedLesson = Lesson::findOrFail($lessonId);
        
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();
            
        $validPostIds = Post::where('serial_id', $serial->id)
                ->where('category', 'like', '%"lesson_id":' . $selectedLesson->id . '%')
                ->where('is_task', 1)
                ->where(function($q) use ($classroom) {
                    $q->whereNull('classroom_id')
                      ->orWhere('classroom_id', $classroom->id);
                })->pluck('id');

        $guruExerciseIds = Exercise::where('lesson_id', $selectedLesson->id)
                ->where('is_admin', 0)
                ->pluck('id');

        $adminExerciseIds = Exercise::whereHas('lesson', function($q) use ($selectedLesson) {
                    $q->where('mapel_id', $selectedLesson->mapel_id)
                      ->where('category', Lesson::CATEGORY_SOAL);
                })
                ->where('is_admin', 1)
                ->pluck('id');

        $validExerciseIds = $guruExerciseIds->concat($adminExerciseIds)->unique();

        $allTasks = Task::whereIn('student_id', $students->pluck('id'))
            ->whereIn('post_id', $validPostIds)
            ->get()
            ->groupBy('student_id');

        $allExPoints = ExercisePoint::whereIn('student_id', $students->pluck('id'))
            ->whereIn('exercise_id', $validExerciseIds)
            ->with('exercise.exerciseType')
            ->get()
            ->groupBy('student_id');

        $rekapData = [];
        foreach ($students as $student) {
            $sTasks = $allTasks->get($student->id, collect());
            $sExPoints = $allExPoints->get($student->id, collect());

            $tugas = ['sum' => 0, 'count' => 0];
            $akm = ['sum' => 0, 'count' => 0];
            $uh = ['sum' => 0, 'count' => 0];
            $pts = ['sum' => 0, 'count' => 0];
            $pas = ['sum' => 0, 'count' => 0];

            foreach ($sTasks as $task) {
                if (!is_null($task->point)) {
                    $tugas['sum'] += $task->point;
                    $tugas['count']++;
                }
            }

            foreach ($sExPoints as $ex) {
                if (!is_null($ex->exercise_point)) {
                    $typeName = $ex->exercise && $ex->exercise->exerciseType ? strtolower($ex->exercise->exerciseType->name) : '';
                    if (str_contains($typeName, 'akm')) {
                        $akm['sum'] += $ex->exercise_point;
                        $akm['count']++;
                    } elseif (str_contains($typeName, 'ulangan harian')) {
                        $uh['sum'] += $ex->exercise_point;
                        $uh['count']++;
                    } elseif (str_contains($typeName, 'pts')) {
                        $pts['sum'] += $ex->exercise_point;
                        $pts['count']++;
                    } elseif (str_contains($typeName, 'pas')) {
                        $pas['sum'] += $ex->exercise_point;
                        $pas['count']++;
                    }
                }
            }

            $rataTugas = $tugas['count'] > 0 ? round($tugas['sum'] / $tugas['count'], 1) : null;
            $rataAKM = $akm['count'] > 0 ? round($akm['sum'] / $akm['count'], 1) : null;
            $rataUH = $uh['count'] > 0 ? round($uh['sum'] / $uh['count'], 1) : null;
            $rataPTS = $pts['count'] > 0 ? round($pts['sum'] / $pts['count'], 1) : null;
            $rataPAS = $pas['count'] > 0 ? round($pas['sum'] / $pas['count'], 1) : null;

            $categories = [$rataTugas, $rataAKM, $rataUH, $rataPTS, $rataPAS];
            $filled = collect($categories)->filter(fn($v) => !is_null($v));
            $nilaiAkhir = $filled->count() ? round($filled->avg(), 1) : null;

            $rekapData[] = [
                'student' => $student,
                'tugas' => ['avg' => $rataTugas, 'count' => $tugas['count']],
                'akm' => ['avg' => $rataAKM, 'count' => $akm['count']],
                'uh' => ['avg' => $rataUH, 'count' => $uh['count']],
                'pts' => ['avg' => $rataPTS, 'count' => $pts['count']],
                'pas' => ['avg' => $rataPAS, 'count' => $pas['count']],
                'nilai_akhir' => $nilaiAkhir
            ];
        }

        $pdf = Pdf::loadView('guru.rekap-nilai.pdf.class', compact('serial', 'classroom', 'students', 'selectedLesson', 'rekapData'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('rekap_nilai_'.$classroom->name.'_'.\Str::slug($selectedLesson->name).'.pdf');
    }
EOD;

$startDownload = strpos($content, 'public function downloadClassPdf');
$endDownload = strpos($content, 'public function showStudent');
if ($startDownload !== false && $endDownload !== false) {
    $content = substr_replace($content, $newDownloadClassPdf . "\n\n    ", $startDownload, $endDownload - $startDownload);
}

file_put_contents($controllerPath, $content);

echo "Controller updated successfully.\n";

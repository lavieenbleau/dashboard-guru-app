<?php

$controllerPath = __DIR__ . '/app/Http/Controllers/Guru/RekapNilaiController.php';
$content = file_get_contents($controllerPath);

$newShowStudent = <<<'EOD'
    public function showStudent($serial, $classroomId, $studentId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $student = Student::findOrFail($studentId);

        $tasks = Task::where('student_id', $student->id)
            ->with(['post.mapel'])
            ->orderBy('created_at', 'desc')
            ->get();

        $exercisePoints = ExercisePoint::where('student_id', $student->id)
            ->with(['exercise.lesson.mapel', 'exercise.exerciseType'])
            ->orderBy('created_at', 'desc')
            ->get();

        $lessonIds = $tasks->map(function($task) {
            $cat = is_string($task->post->category) ? json_decode($task->post->category, true) : $task->post->category;
            return $cat['lesson_id'] ?? null;
        })->filter()->unique()->toArray();
        $lessonsForTasks = \App\Models\Lesson::whereIn('id', $lessonIds)->pluck('name', 'id');

        $tugasList = []; $tugasSum = 0; $tugasCount = 0;
        foreach ($tasks as $task) {
            $cat = is_string($task->post->category) ? json_decode($task->post->category, true) : $task->post->category;
            $lessonId = $cat['lesson_id'] ?? null;
            $lessonName = $lessonId && isset($lessonsForTasks[$lessonId]) ? $lessonsForTasks[$lessonId] : '';
            $tugasList[] = [
                'title' => $task->post->title ?? 'Tugas',
                'lesson' => $lessonName,
                'point' => $task->point,
                'date' => $task->created_at,
            ];
            if (!is_null($task->point)) {
                $tugasSum += $task->point;
                $tugasCount++;
            }
        }
        $rataTugas = $tugasCount > 0 ? round($tugasSum / $tugasCount, 1) : null;

        $akmList = []; $akmSum = 0; $akmCount = 0;
        $uhList = []; $uhSum = 0; $uhCount = 0;
        $ptsList = []; $ptsSum = 0; $ptsCount = 0;
        $pasList = []; $pasSum = 0; $pasCount = 0;

        foreach ($exercisePoints as $ex) {
            $typeName = $ex->exercise && $ex->exercise->exerciseType ? strtolower($ex->exercise->exerciseType->name) : '';
            $item = [
                'title' => $ex->exercise->title ?? 'Soal',
                'lesson' => $ex->exercise->lesson->name ?? '',
                'point' => $ex->exercise_point,
                'date' => $ex->created_at,
            ];
            
            if (str_contains($typeName, 'akm')) {
                $akmList[] = $item;
                if (!is_null($ex->exercise_point)) { $akmSum += $ex->exercise_point; $akmCount++; }
            } elseif (str_contains($typeName, 'ulangan harian')) {
                $uhList[] = $item;
                if (!is_null($ex->exercise_point)) { $uhSum += $ex->exercise_point; $uhCount++; }
            } elseif (str_contains($typeName, 'pts')) {
                $ptsList[] = $item;
                if (!is_null($ex->exercise_point)) { $ptsSum += $ex->exercise_point; $ptsCount++; }
            } elseif (str_contains($typeName, 'pas')) {
                $pasList[] = $item;
                if (!is_null($ex->exercise_point)) { $pasSum += $ex->exercise_point; $pasCount++; }
            }
        }

        $rataAKM = $akmCount > 0 ? round($akmSum / $akmCount, 1) : null;
        $rataUH = $uhCount > 0 ? round($uhSum / $uhCount, 1) : null;
        $rataPTS = $ptsCount > 0 ? round($ptsSum / $ptsCount, 1) : null;
        $rataPAS = $pasCount > 0 ? round($pasSum / $pasCount, 1) : null;

        $categories = [$rataTugas, $rataAKM, $rataUH, $rataPTS, $rataPAS];
        $filled = collect($categories)->filter(fn($v) => !is_null($v));
        $nilaiAkhir = $filled->count() ? round($filled->avg(), 1) : null;

        $rekapDetail = [
            'tugas' => ['list' => $tugasList, 'avg' => $rataTugas],
            'akm' => ['list' => $akmList, 'avg' => $rataAKM],
            'uh' => ['list' => $uhList, 'avg' => $rataUH],
            'pts' => ['list' => $ptsList, 'avg' => $rataPTS],
            'pas' => ['list' => $pasList, 'avg' => $rataPAS],
            'nilai_akhir' => $nilaiAkhir
        ];

        return view('guru.rekap-nilai.show-student', compact('serial', 'classroom', 'student', 'rekapDetail'));
    }
EOD;

$startShowStudent = strpos($content, 'public function showStudent');
$endShowStudent = strpos($content, 'public function downloadStudentPdf');
if ($startShowStudent !== false && $endShowStudent !== false) {
    $content = substr_replace($content, $newShowStudent . "\n\n    ", $startShowStudent, $endShowStudent - $startShowStudent);
}

$newDownloadStudentPdf = <<<'EOD'
    public function downloadStudentPdf($serial, $classroomId, $studentId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $student = Student::findOrFail($studentId);

        $tasks = Task::where('student_id', $student->id)
            ->with(['post.mapel'])
            ->orderBy('created_at', 'desc')
            ->get();

        $exercisePoints = ExercisePoint::where('student_id', $student->id)
            ->with(['exercise.lesson.mapel', 'exercise.exerciseType'])
            ->orderBy('created_at', 'desc')
            ->get();

        $lessonIds = $tasks->map(function($task) {
            $cat = is_string($task->post->category) ? json_decode($task->post->category, true) : $task->post->category;
            return $cat['lesson_id'] ?? null;
        })->filter()->unique()->toArray();
        $lessonsForTasks = \App\Models\Lesson::whereIn('id', $lessonIds)->pluck('name', 'id');

        $tugasList = []; $tugasSum = 0; $tugasCount = 0;
        foreach ($tasks as $task) {
            $cat = is_string($task->post->category) ? json_decode($task->post->category, true) : $task->post->category;
            $lessonId = $cat['lesson_id'] ?? null;
            $lessonName = $lessonId && isset($lessonsForTasks[$lessonId]) ? $lessonsForTasks[$lessonId] : '';
            $tugasList[] = [
                'title' => $task->post->title ?? 'Tugas',
                'lesson' => $lessonName,
                'point' => $task->point,
                'date' => $task->created_at,
            ];
            if (!is_null($task->point)) {
                $tugasSum += $task->point;
                $tugasCount++;
            }
        }
        $rataTugas = $tugasCount > 0 ? round($tugasSum / $tugasCount, 1) : null;

        $akmList = []; $akmSum = 0; $akmCount = 0;
        $uhList = []; $uhSum = 0; $uhCount = 0;
        $ptsList = []; $ptsSum = 0; $ptsCount = 0;
        $pasList = []; $pasSum = 0; $pasCount = 0;

        foreach ($exercisePoints as $ex) {
            $typeName = $ex->exercise && $ex->exercise->exerciseType ? strtolower($ex->exercise->exerciseType->name) : '';
            $item = [
                'title' => $ex->exercise->title ?? 'Soal',
                'lesson' => $ex->exercise->lesson->name ?? '',
                'point' => $ex->exercise_point,
                'date' => $ex->created_at,
            ];
            
            if (str_contains($typeName, 'akm')) {
                $akmList[] = $item;
                if (!is_null($ex->exercise_point)) { $akmSum += $ex->exercise_point; $akmCount++; }
            } elseif (str_contains($typeName, 'ulangan harian')) {
                $uhList[] = $item;
                if (!is_null($ex->exercise_point)) { $uhSum += $ex->exercise_point; $uhCount++; }
            } elseif (str_contains($typeName, 'pts')) {
                $ptsList[] = $item;
                if (!is_null($ex->exercise_point)) { $ptsSum += $ex->exercise_point; $ptsCount++; }
            } elseif (str_contains($typeName, 'pas')) {
                $pasList[] = $item;
                if (!is_null($ex->exercise_point)) { $pasSum += $ex->exercise_point; $pasCount++; }
            }
        }

        $rataAKM = $akmCount > 0 ? round($akmSum / $akmCount, 1) : null;
        $rataUH = $uhCount > 0 ? round($uhSum / $uhCount, 1) : null;
        $rataPTS = $ptsCount > 0 ? round($ptsSum / $ptsCount, 1) : null;
        $rataPAS = $pasCount > 0 ? round($pasSum / $pasCount, 1) : null;

        $categories = [$rataTugas, $rataAKM, $rataUH, $rataPTS, $rataPAS];
        $filled = collect($categories)->filter(fn($v) => !is_null($v));
        $nilaiAkhir = $filled->count() ? round($filled->avg(), 1) : null;

        $rekapDetail = [
            'tugas' => ['list' => $tugasList, 'avg' => $rataTugas],
            'akm' => ['list' => $akmList, 'avg' => $rataAKM],
            'uh' => ['list' => $uhList, 'avg' => $rataUH],
            'pts' => ['list' => $ptsList, 'avg' => $rataPTS],
            'pas' => ['list' => $pasList, 'avg' => $rataPAS],
            'nilai_akhir' => $nilaiAkhir
        ];

        $pdf = Pdf::loadView('guru.rekap-nilai.pdf.student', compact('serial', 'classroom', 'student', 'rekapDetail'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Rekap-Nilai-' . str_replace(' ', '-', $student->name) . '.pdf');
    }
EOD;

$startDownloadStudent = strpos($content, 'public function downloadStudentPdf');
$endClass = strrpos($content, '}');
if ($startDownloadStudent !== false && $endClass !== false) {
    // End is before the final }
    $content = substr_replace($content, $newDownloadStudentPdf . "\n", $startDownloadStudent, $endClass - $startDownloadStudent);
}

file_put_contents($controllerPath, $content);

echo "Controller updated successfully.\n";

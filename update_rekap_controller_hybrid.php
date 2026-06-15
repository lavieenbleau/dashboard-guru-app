<?php

$controllerPath = __DIR__ . '/app/Http/Controllers/Guru/RekapNilaiController.php';
$content = file_get_contents($controllerPath);

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

        // Build unique columns for Detail Penilaian Tab
        $uniquePosts = Post::whereIn('id', $validPostIds)->orderBy('created_at')->get();
        $uniqueExercises = Exercise::whereIn('id', $validExerciseIds)->with('exerciseType')->orderBy('created_at')->get();

        $detailColumns = [
            'tasks' => collect(),
            'akm' => collect(),
            'uh' => collect(),
            'pts' => collect(),
            'pas' => collect()
        ];

        foreach ($uniquePosts as $p) {
            $detailColumns['tasks']->push(['id' => $p->id, 'title' => $p->title]);
        }
        foreach ($uniqueExercises as $ex) {
            $typeName = strtolower($ex->exerciseType->name ?? '');
            $item = ['id' => $ex->id, 'title' => $ex->title];
            if (str_contains($typeName, 'akm')) {
                $detailColumns['akm']->push($item);
            } elseif (str_contains($typeName, 'ulangan harian')) {
                $detailColumns['uh']->push($item);
            } elseif (str_contains($typeName, 'pts')) {
                $detailColumns['pts']->push($item);
            } elseif (str_contains($typeName, 'pas')) {
                $detailColumns['pas']->push($item);
            }
        }

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

            $studentDetails = [
                'tasks' => [],
                'akm' => [],
                'uh' => [],
                'pts' => [],
                'pas' => []
            ];

            foreach ($sTasks as $task) {
                if (!is_null($task->point)) {
                    $tugas['sum'] += $task->point;
                    $tugas['count']++;
                    $studentDetails['tasks'][$task->post_id] = $task->point;
                }
            }

            foreach ($sExPoints as $ex) {
                if (!is_null($ex->exercise_point)) {
                    $typeName = $ex->exercise && $ex->exercise->exerciseType ? strtolower($ex->exercise->exerciseType->name) : '';
                    if (str_contains($typeName, 'akm')) {
                        $akm['sum'] += $ex->exercise_point;
                        $akm['count']++;
                        $studentDetails['akm'][$ex->exercise_id] = $ex->exercise_point;
                    } elseif (str_contains($typeName, 'ulangan harian')) {
                        $uh['sum'] += $ex->exercise_point;
                        $uh['count']++;
                        $studentDetails['uh'][$ex->exercise_id] = $ex->exercise_point;
                    } elseif (str_contains($typeName, 'pts')) {
                        $pts['sum'] += $ex->exercise_point;
                        $pts['count']++;
                        $studentDetails['pts'][$ex->exercise_id] = $ex->exercise_point;
                    } elseif (str_contains($typeName, 'pas')) {
                        $pas['sum'] += $ex->exercise_point;
                        $pas['count']++;
                        $studentDetails['pas'][$ex->exercise_id] = $ex->exercise_point;
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
                'nilai_akhir' => $nilaiAkhir,
                'detail' => $studentDetails
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

        $detailAverages = [
            'tasks' => [], 'akm' => [], 'uh' => [], 'pts' => [], 'pas' => []
        ];
        foreach (['tasks', 'akm', 'uh', 'pts', 'pas'] as $cat) {
            foreach ($detailColumns[$cat] as $col) {
                $sum = 0; $count = 0;
                foreach ($rekapData as $s) {
                    if (isset($s['detail'][$cat][$col['id']])) {
                        $sum += $s['detail'][$cat][$col['id']];
                        $count++;
                    }
                }
                $detailAverages[$cat][$col['id']] = $count > 0 ? round($sum / $count, 1) : null;
            }
        }

        return view('guru.rekap-nilai.show-class', compact('serial', 'classroom', 'students', 'selectedLesson', 'rekapData', 'stats', 'detailColumns', 'detailAverages'));
    }
EOD;

$startShowLesson = strpos($content, 'public function showLesson');
$endShowLesson = strpos($content, 'public function downloadClassPdf');
if ($startShowLesson !== false && $endShowLesson !== false) {
    $content = substr_replace($content, $newShowLesson . "\n\n    ", $startShowLesson, $endShowLesson - $startShowLesson);
}

file_put_contents($controllerPath, $content);
echo "Controller updated successfully.\n";

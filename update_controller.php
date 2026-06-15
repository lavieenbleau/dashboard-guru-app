<?php

$file = __DIR__ . '/app/Http/Controllers/Guru/QuizMonitoringController.php';
$content = file_get_contents($file);

// 1. Refactor indexClasses (Level 1)
$indexClassesStart = strpos($content, 'public function indexClasses()');
$indexProductsStart = strpos($content, 'public function indexProducts($kelasName)');

$newIndexClasses = <<<'EOD'
public function indexClasses()
    {
        $userId = auth()->id();
        
        $serials = \App\Models\Serial::where('user_id', $userId)->pluck('id');
        $classrooms = \App\Models\Classroom::whereIn('serial_id', $serials)
            ->get()
            ->groupBy('name');
            
        $classStats = [];
        
        foreach ($classrooms as $className => $classes) {
            $classIds = $classes->pluck('id');
            // Distinct students by username across these classes to avoid double counting if they are the same
            $students = \App\Models\Student::whereIn('classroom_id', $classIds)->get();
            $totalStudents = $students->unique('username')->count();
            
            $classStats[] = [
                'name' => $className,
                'total_students' => $totalStudents
            ];
        }
        
        return view('guru.monitoring-quiz.classes', compact('classStats'));
    }

    /**
     * LEVEL 2: Daftar Produk (Serials) untuk kelas tertentu
     */
EOD;

$content = substr_replace($content, $newIndexClasses, $indexClassesStart, $indexProductsStart - $indexClassesStart);


// 2. Refactor indexProducts (Level 2)
// Since indexProducts is after indexClasses, we need to find it again.
$indexProductsStart = strpos($content, 'public function indexProducts($kelasName)');
$monitoringStudentStart = strpos($content, 'public function monitoringStudent($kelasName, $serialId)');

$newIndexProducts = <<<'EOD'
public function indexProducts($kelasName)
    {
        $userId = auth()->id();
        $serials = \App\Models\Serial::where('user_id', $userId)->pluck('id');
        
        $classes = \App\Models\Classroom::whereIn('serial_id', $serials)
            ->where('name', $kelasName)
            ->with(['serial.product'])
            ->get();
            
        if ($classes->isEmpty()) {
            abort(404);
        }
            
        $productStats = [];
        
        foreach ($classes as $classroom) {
            $serial = $classroom->serial;
            if (!$serial) continue;
            
            $lessonIds = [];
            if ($serial->product) {
                $lessonIds = json_decode($serial->product->lesson_id, true) ?? [];
            }
            
            if (empty($lessonIds)) {
                $studentIds = \App\Models\Student::where('classroom_id', $classroom->id)->pluck('id');
                $exercises = \App\Models\Exercise::where('serial_id', $serial->id)->get();
                $totalStudents = $studentIds->count();
                $productName = $serial->product ? $serial->product->name : $serial->paket;
                
                $lastUpdate = $exercises->max('created_at');
                
                $productStats[] = [
                    'serial_id' => $serial->id,
                    'lesson_id' => null,
                    'name' => $productName,
                    'total_students' => $totalStudents,
                    'total_quiz' => $exercises->count(),
                    'last_update' => $lastUpdate
                ];
            } else {
                $lessons = \App\Models\Lesson::whereIn('id', $lessonIds)->get();
                foreach ($lessons as $lesson) {
                    $studentIds = \App\Models\Student::where('classroom_id', $classroom->id)->pluck('id');
                    $exercises = \App\Models\Exercise::where('serial_id', $serial->id)->where('lesson_id', $lesson->id)->get();
                    $totalStudents = $studentIds->count();
                    
                    $lastUpdate = $exercises->max('created_at');
                    
                    $productStats[] = [
                        'serial_id' => $serial->id,
                        'lesson_id' => $lesson->id,
                        'name' => $lesson->name,
                        'total_students' => $totalStudents,
                        'total_quiz' => $exercises->count(),
                        'last_update' => $lastUpdate
                    ];
                }
            }
        }
        
        return view('guru.monitoring-quiz.products', compact('productStats', 'kelasName'));
    }

    /**
     * LEVEL 3: Monitoring Siswa
     */
EOD;

$content = substr_replace($content, $newIndexProducts, $indexProductsStart, $monitoringStudentStart - $indexProductsStart);


// 3. Refactor monitoringStudent & monitoringStudentDetail
$monitoringStudentStart = strpos($content, 'public function monitoringStudent($kelasName, $serialId)');
$dataTableStart = strpos($content, 'public function dataTable(Request $request, $kelasName, $serialId)');

$newMonitoringMethods = <<<'EOD'
public function monitoringStudent($kelasName, $serialId)
    {
        $serialModel = \App\Models\Serial::findOrFail($serialId);
        
        // Pastikan kelas ini milik user
        $classroom = \App\Models\Classroom::where('serial_id', $serialId)
            ->where('name', $kelasName)
            ->firstOrFail();
            
        $lessonId = request()->query('lesson_id');
        
        $exercisesQuery = \App\Models\Exercise::with(['exerciseType', 'exerciseItems.competence', 'serial.classrooms', 'sharedSerials.classrooms'])
            ->where('serial_id', $serialId);
            
        if ($lessonId) {
            $exercisesQuery->where('lesson_id', $lessonId);
        }
        
        // Urutan: Terbaru -> Terlama
        $exercises = $exercisesQuery->orderBy('created_at', 'desc')->get();
        
        // Populate info dasar kuis
        foreach ($exercises as $ex) {
            $kds = collect();
            if ($ex->exerciseItems) {
                foreach ($ex->exerciseItems as $item) {
                    if ($item->competence && $item->competence->point) {
                        $kds->push($item->competence->point);
                    }
                }
            }
            $ex->kd_list = $kds->unique()->values();
            $ex->total_soal = $ex->exerciseItems ? $ex->exerciseItems->count() : 0;
            
            // Collect shared classes count
            $sharedClassesCount = 0;
            if ($ex->serial && $ex->serial->classrooms) {
                $sharedClassesCount += $ex->serial->classrooms->count();
            }
            if ($ex->sharedSerials) {
                foreach ($ex->sharedSerials as $ss) {
                    if ($ss->classrooms) {
                        $sharedClassesCount += $ss->classrooms->count();
                    }
                }
            }
            $ex->shared_classes_count = $sharedClassesCount;
            
            $catName = $ex->exerciseType ? $ex->exerciseType->name : 'Lainnya';
            $ex->category_name = $catName;
            
            if (stripos($catName, 'akm') !== false) {
                $ex->badge_color = 'success';
            } elseif (stripos($catName, 'ulangan harian') !== false || stripos($catName, 'uh') !== false) {
                $ex->badge_color = 'primary';
            } elseif (stripos($catName, 'quiz') !== false || stripos($catName, 'kuis') !== false) {
                $ex->badge_color = 'info';
            } elseif (stripos($catName, 'pts') !== false) {
                $ex->badge_color = 'warning';
            } elseif (stripos($catName, 'pas') !== false || stripos($catName, 'pat') !== false) {
                $ex->badge_color = 'danger';
            } elseif (stripos($catName, 'asesmen') !== false) {
                $ex->badge_color = 'dark';
            } else {
                $ex->badge_color = 'secondary';
            }
        }
        
        $productName = $serialModel->paket;
        if ($lessonId) {
            $lesson = \App\Models\Lesson::find($lessonId);
            if ($lesson) {
                $productName = $lesson->name;
            }
        } else if ($serialModel->product) {
            $productName = $serialModel->product->name;
        }
        
        $lessonIdParam = $lessonId ? '?lesson_id=' . $lessonId : '';

        // Grouping
        $groupedExercises = collect($exercises)->groupBy('category_name');

        return view('guru.soal.monitoring_kuis_list', compact(
            'kelasName',
            'serialModel',
            'productName',
            'groupedExercises',
            'exercises',
            'classroom',
            'lessonId',
            'lessonIdParam'
        ));
    }

    /**
     * LEVEL 4: Detail Monitoring Siswa per Kuis
     */
    public function monitoringStudentDetail($kelasName, $serialId, $exerciseId)
    {
        $serialModel = \App\Models\Serial::findOrFail($serialId);
        
        $classroom = \App\Models\Classroom::where('serial_id', $serialId)
            ->where('name', $kelasName)
            ->firstOrFail();
            
        $lessonId = request()->query('lesson_id');
        
        $exercise = \App\Models\Exercise::with(['exerciseType', 'exerciseItems.competence'])->findOrFail($exerciseId);
        $exercises = collect([$exercise]); // for the view compatibility
        
        $studentIds = \App\Models\Student::where('classroom_id', $classroom->id)->pluck('id');
        $totalStudents = $studentIds->count();
        
        try {
            $latestEvents = collect();
            if (!$studentIds->isEmpty()) {
                $latestEvents = \Illuminate\Support\Facades\DB::connection('log_db')->table('quiz_activity_logs as q1')
                    ->select('q1.student_id', 'q1.exercise_id', 'q1.event_type', 'q1.suspicious_flag')
                    ->join(\Illuminate\Support\Facades\DB::raw('(SELECT student_id, exercise_id, MAX(created_at) as max_time FROM quiz_activity_logs WHERE student_id IN ('.implode(',', $studentIds->toArray()).') AND exercise_id = '.$exerciseId.' GROUP BY student_id, exercise_id) as q2'), function($join) {
                        $join->on('q1.student_id', '=', 'q2.student_id')
                             ->on('q1.exercise_id', '=', 'q2.exercise_id')
                             ->on('q1.created_at', '=', 'q2.max_time');
                    })
                    ->get();
            }
            
            $finishedCount = $latestEvents->whereIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
            $activeCount = $latestEvents->whereNotIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
            $notStartedCount = max(0, $totalStudents - $finishedCount - $activeCount);
            
            // Perhitungan Rata-rata Nilai
            $exercisePointsQuery = \App\Models\ExercisePoint::where('exercise_id', $exerciseId)
                                ->whereIn('student_id', $studentIds);
                                
            $averageScore = $exercisePointsQuery->avg('exercise_point') ?? 0;
            $highestScore = $exercisePointsQuery->max('exercise_point') ?? 0;
            $lowestScore = $exercisePointsQuery->count() > 0 ? $exercisePointsQuery->min('exercise_point') : 0;
            
            $dbError = null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (Monitoring Detail): ' . $e->getMessage());
            $activeCount = $finishedCount = $notStartedCount = $averageScore = $highestScore = $lowestScore = 0;
            $dbError = "Gagal terhubung ke Database Log. Pemantauan sedang tidak aktif.";
        }
        
        $productName = $serialModel->paket;
        if ($lessonId) {
            $lesson = \App\Models\Lesson::find($lessonId);
            if ($lesson) {
                $productName = $lesson->name;
            }
        } else if ($serialModel->product) {
            $productName = $serialModel->product->name;
        }
        
        $lessonIdParam = $lessonId ? '?lesson_id=' . $lessonId : '';
        
        $catName = $exercise->exerciseType ? $exercise->exerciseType->name : 'Lainnya';
        if (stripos($catName, 'akm') !== false) {
            $exercise->badge_color = 'success';
        } elseif (stripos($catName, 'ulangan harian') !== false || stripos($catName, 'uh') !== false) {
            $exercise->badge_color = 'primary';
        } elseif (stripos($catName, 'quiz') !== false || stripos($catName, 'kuis') !== false) {
            $exercise->badge_color = 'info';
        } elseif (stripos($catName, 'pts') !== false) {
            $exercise->badge_color = 'warning';
        } elseif (stripos($catName, 'pas') !== false || stripos($catName, 'pat') !== false) {
            $exercise->badge_color = 'danger';
        } elseif (stripos($catName, 'asesmen') !== false) {
            $exercise->badge_color = 'dark';
        } else {
            $exercise->badge_color = 'secondary';
        }
        
        $exercise->category_name = $catName;
        
        $kds = collect();
        if ($exercise->exerciseItems) {
            foreach ($exercise->exerciseItems as $item) {
                if ($item->competence && $item->competence->point) {
                    $kds->push($item->competence->point);
                }
            }
        }
        $exercise->kd_list = $kds->unique()->values();

        return view('guru.soal.monitoring', compact(
            'kelasName',
            'serialModel',
            'productName',
            'exercises',
            'exercise',
            'activeCount',
            'finishedCount',
            'notStartedCount',
            'totalStudents',
            'averageScore',
            'highestScore',
            'lowestScore',
            'dbError',
            'classroom',
            'lessonId',
            'lessonIdParam'
        ));
    }

    /**
     * Datatables JSON response untuk Level 3
     */
EOD;

$content = substr_replace($content, $newMonitoringMethods, $monitoringStudentStart, $dataTableStart - $monitoringStudentStart);

file_put_contents($file, $content);
echo "QuizMonitoringController updated.";


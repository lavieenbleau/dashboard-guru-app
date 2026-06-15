<?php
$content = file_get_contents("app/Http/Controllers/Guru/RekapNilaiController.php");
$method = "
    public function getStudentDetailAjax(\$serial, \$classroomId, \$lessonId, \$studentId)
    {
        \$serial = Serial::findOrFail(\$serial);
        \$classroom = Classroom::findOrFail(\$classroomId);
        \$selectedLesson = Lesson::findOrFail(\$lessonId);
        \$student = Student::with(['tasks.post', 'exercisePoints.exercise.exerciseType'])->findOrFail(\$studentId);

        \$validPostIds = Post::where('serial_id', \$serial->id)
                ->where('is_task', 1)
                ->whereRaw('IF(JSON_VALID(category) = 1, JSON_UNQUOTE(JSON_EXTRACT(category, \"$.lesson_id\")), NULL) = ?', [\$selectedLesson->id])
                ->where(function(\$q) use (\$classroom) {
                    \$q->whereNull('classroom_id')
                      ->orWhere('classroom_id', \$classroom->id);
                })->pluck('id');

        \$guruExerciseIds = Exercise::where('lesson_id', \$selectedLesson->id)
                ->where('is_admin', 0)
                ->pluck('id');

        \$adminExerciseIds = Exercise::whereHas('lesson', function(\$q) use (\$selectedLesson) {
                    \$q->where('mapel_id', \$selectedLesson->mapel_id)
                      ->where('category', Lesson::CATEGORY_SOAL);
                })
                ->where('is_admin', 1)
                ->pluck('id');

        \$validExerciseIds = \$guruExerciseIds->concat(\$adminExerciseIds)->unique();

        \$uniquePosts = Post::whereIn('id', \$validPostIds)->orderBy('created_at')->get();
        \$uniqueExercises = Exercise::whereIn('id', \$validExerciseIds)->with('exerciseType')->orderBy('created_at')->get();

        \$sTasks = \$student->tasks->whereIn('post_id', \$validPostIds);
        \$sExPoints = \$student->exercisePoints->whereIn('exercise_id', \$validExerciseIds);

        \$rekapDetail = [
            'tugas' => ['sum' => 0, 'count' => 0, 'avg' => null, 'list' => []],
            'akm' => ['sum' => 0, 'count' => 0, 'avg' => null, 'list' => []],
            'uh' => ['sum' => 0, 'count' => 0, 'avg' => null, 'list' => []],
            'pts' => ['sum' => 0, 'count' => 0, 'avg' => null, 'list' => []],
            'pas' => ['sum' => 0, 'count' => 0, 'avg' => null, 'list' => []],
        ];

        \$studentDetails = ['tugas' => [], 'akm' => [], 'uh' => [], 'pts' => [], 'pas' => []];

        foreach (\$sTasks as \$t) {
            \$rekapDetail['tugas']['sum'] += \$t->point;
            \$rekapDetail['tugas']['count']++;
            \$studentDetails['tugas'][\$t->post_id] = \$t->point;
        }

        foreach (\$sExPoints as \$ex) {
            if (!is_null(\$ex->exercise_point)) {
                \$typeName = \$ex->exercise && \$ex->exercise->exerciseType ? strtolower(\$ex->exercise->exerciseType->name) : '';
                if (str_contains(\$typeName, 'akm')) {
                    \$rekapDetail['akm']['sum'] += \$ex->exercise_point;
                    \$rekapDetail['akm']['count']++;
                    \$studentDetails['akm'][\$ex->exercise_id] = \$ex->exercise_point;
                } elseif (str_contains(\$typeName, 'ulangan harian')) {
                    \$rekapDetail['uh']['sum'] += \$ex->exercise_point;
                    \$rekapDetail['uh']['count']++;
                    \$studentDetails['uh'][\$ex->exercise_id] = \$ex->exercise_point;
                } elseif (str_contains(\$typeName, 'pts')) {
                    \$rekapDetail['pts']['sum'] += \$ex->exercise_point;
                    \$rekapDetail['pts']['count']++;
                    \$studentDetails['pts'][\$ex->exercise_id] = \$ex->exercise_point;
                } elseif (str_contains(\$typeName, 'pas')) {
                    \$rekapDetail['pas']['sum'] += \$ex->exercise_point;
                    \$rekapDetail['pas']['count']++;
                    \$studentDetails['pas'][\$ex->exercise_id] = \$ex->exercise_point;
                }
            }
        }

        foreach (\$uniquePosts as \$p) {
            \$rekapDetail['tugas']['list'][] = [
                'title' => \$p->title,
                'lesson' => \$selectedLesson->name,
                'point' => \$studentDetails['tugas'][\$p->id] ?? null
            ];
        }

        foreach (\$uniqueExercises as \$ex) {
            \$typeName = strtolower(\$ex->exerciseType->name ?? '');
            \$item = [
                'title' => \$ex->title,
                'point' => null
            ];
            if (str_contains(\$typeName, 'akm')) {
                \$item['point'] = \$studentDetails['akm'][\$ex->id] ?? null;
                \$rekapDetail['akm']['list'][] = \$item;
            } elseif (str_contains(\$typeName, 'ulangan harian')) {
                \$item['point'] = \$studentDetails['uh'][\$ex->id] ?? null;
                \$rekapDetail['uh']['list'][] = \$item;
            } elseif (str_contains(\$typeName, 'pts')) {
                \$item['point'] = \$studentDetails['pts'][\$ex->id] ?? null;
                \$rekapDetail['pts']['list'][] = \$item;
            } elseif (str_contains(\$typeName, 'pas')) {
                \$item['point'] = \$studentDetails['pas'][\$ex->id] ?? null;
                \$rekapDetail['pas']['list'][] = \$item;
            }
        }

        \$validCategories = 0;
        \$totalAvg = 0;

        foreach (['tugas', 'akm', 'uh', 'pts', 'pas'] as \$cat) {
            if (\$rekapDetail[\$cat]['count'] > 0) {
                \$rekapDetail[\$cat]['avg'] = \$rekapDetail[\$cat]['sum'] / \$rekapDetail[\$cat]['count'];
                \$totalAvg += \$rekapDetail[\$cat]['avg'];
                \$validCategories++;
            }
        }

        \$overallScore = \$validCategories > 0 ? round(\$totalAvg / \$validCategories, 1) : null;

        return view('guru.rekap-nilai.partials.student-modal-body', compact('serial', 'classroom', 'student', 'rekapDetail', 'overallScore'))->render();
    }

    /**
     * @deprecated Digantikan oleh AJAX Modal pada v2.1
     */
    public function showStudent";

$content = str_replace("    public function showStudent", \$method, \$content);
file_put_contents("app/Http/Controllers/Guru/RekapNilaiController.php", \$content);

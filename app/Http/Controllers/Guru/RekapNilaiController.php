<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\Post;
use App\Models\Task;
use App\Models\Exercise;
use App\Models\ExercisePoint;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapNilaiController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        $classrooms = Classroom::where('serial_id', $serial->id)->get();

        return view('guru.rekap-nilai.index', compact('serial', 'classrooms'));
    }

    public function showClass($serial, $classroomId)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        
        // Get all students in this classroom
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();

        // Get all lessons (Paket Pembelajaran) for this serial product
        $lessonIds = json_decode($serial->product->lesson_id ?? '[]', true) ?? [];
        $lessonIds = json_decode($serial->product->lesson_id ?? '[]', true) ?? [];
        $lessons = Lesson::whereIn('id', $lessonIds)
            ->where('category', Lesson::CATEGORY_MATERI)
            ->with('mapel')
            ->orderBy('name')
            ->get();

        return view('guru.rekap-nilai.lessons', compact('serial', 'classroom', 'lessons'));
    }

            public function showLesson($serial, $classroomId, $lessonId)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $selectedLesson = Lesson::findOrFail($lessonId);
        
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();

        $validPostIds = Post::where('serial_id', $serial->id)
                ->where('is_task', 1)
                ->whereRaw('IF(JSON_VALID(category) = 1, JSON_UNQUOTE(JSON_EXTRACT(category, "$.lesson_id")), NULL) = ?', [$selectedLesson->id])
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

        public function getStudentDetailAjax($serial, $classroomId, $studentId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $student = Student::with(["tasks.post", "exercisePoints.exercise.exerciseType"])->findOrFail($studentId);

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

        return view('guru.rekap-nilai.show-student', compact('serial', 'classroom', 'student', 'rekapDetail', 'lessonsForTasks'));
    }

    /**
     * @deprecated Digantikan oleh AJAX Modal pada v2.1
     */
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

        return view('guru.rekap-nilai.show-student', compact('serial', 'classroom', 'student', 'rekapDetail', 'lessonsForTasks'));
    }

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

        $pdf = Pdf::loadView('guru.rekap-nilai.pdf.student', compact('serial', 'classroom', 'student', 'rekapDetail', 'lessonsForTasks'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Rekap-Nilai-' . str_replace(' ', '-', $student->name) . '.pdf');
    }
}

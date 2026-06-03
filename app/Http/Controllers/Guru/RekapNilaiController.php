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
        
        // Get all students in this classroom
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();

        $lessons = collect([$selectedLesson]);

        // Group posts (tugas) by lesson
        $allTasks = [];
        foreach ($lessons as $lesson) {
            $posts = Post::where('serial_id', $serial->id)
                ->where('category', 'like', '%"lesson_id":' . $lesson->id . '%')
                ->where('is_task', 1)
                ->where(function($q) use ($classroom) {
                    $q->doesntHave('classrooms')
                      ->orWhereHas('classrooms', function($query) use ($classroom) {
                          $query->where('classrooms.id', $classroom->id);
                      });
                })
                ->orderBy('created_at')
                ->get();
            
            foreach ($posts as $index => $post) {
                $allTasks[$lesson->id][] = [
                    'post' => $post,
                    'title' => $post->title,
                    'number' => $index + 1,
                ];
            }
        }

        // Group exercises by lesson (Tambahan) and mapel (Admin)
        $allExercises = [];
        foreach ($lessons as $lesson) {
            // Guru exercises (Tambahan)
            $guruExercises = Exercise::where('lesson_id', $lesson->id)
                ->where('is_admin', 0)
                ->orderBy('created_at')
                ->get();
                
            foreach ($guruExercises as $index => $exercise) {
                $allExercises[$lesson->id]['Tambahan'][] = [
                    'exercise' => $exercise,
                    'title' => $exercise->title,
                    'number' => $index + 1,
                ];
            }

            // Admin exercises for the same mapel
            $adminExercises = Exercise::whereHas('lesson', function($q) use ($lesson) {
                    $q->where('mapel_id', $lesson->mapel_id)
                      ->where('category', Lesson::CATEGORY_SOAL);
                })
                ->where('is_admin', 1)
                ->with(['lesson'])
                ->orderBy('created_at')
                ->get()
                ->groupBy(function($item) {
                    return $item->lesson->semester ?? 0;
                });
                
            foreach ($adminExercises as $semester => $exList) {
                $type = match($semester) {
                    1 => 'UH',
                    2 => 'PTS',
                    3 => 'PAS',
                    default => 'Soal'
                };
                
                foreach ($exList as $index => $exercise) {
                    $allExercises[$lesson->id][$type][] = [
                        'exercise' => $exercise,
                        'title' => $exercise->title,
                        'number' => $index + 1,
                    ];
                }
            }
        }

        // Prepare rekap data per student
        $rekapData = [];
        foreach ($students as $student) {
            $studentData = [
                'student' => $student,
                'lessons' => []
            ];

            foreach ($lessons as $lesson) {
                $lessonTasks = [];
                $lessonExercises = [];

                // Get individual task scores
                if (isset($allTasks[$lesson->id])) {
                    foreach ($allTasks[$lesson->id] as $taskInfo) {
                        $task = Task::where('student_id', $student->id)
                            ->where('post_id', $taskInfo['post']->id)
                            ->first();
                        
                        $lessonTasks[] = [
                            'number' => $taskInfo['number'],
                            'title' => $taskInfo['title'],
                            'point' => $task ? $task->point : null,
                        ];
                    }
                }

                // Get individual exercise scores
                if (isset($allExercises[$lesson->id])) {
                    foreach ($allExercises[$lesson->id] as $type => $exList) {
                        foreach ($exList as $exInfo) {
                            $exPoint = ExercisePoint::where('student_id', $student->id)
                                ->where('exercise_id', $exInfo['exercise']->id)
                                ->first();
                            
                            $lessonExercises[$type][] = [
                                'number' => $exInfo['number'],
                                'title' => $exInfo['title'],
                                'point' => $exPoint ? $exPoint->point : null,
                            ];
                        }
                    }
                }

                // Calculate averages
                $taskSum = 0; $taskCount = 0;
                foreach ($lessonTasks as $t) {
                    if ($t['point'] !== null) { $taskSum += $t['point']; $taskCount++; }
                }
                $taskAvg = $taskCount > 0 ? round($taskSum / $taskCount, 1) : 0;

                $exSum = 0; $exCount = 0;
                foreach ($lessonExercises as $type => $list) {
                    foreach ($list as $e) {
                        if ($e['point'] !== null) { $exSum += $e['point']; $exCount++; }
                    }
                }
                $exAvg = $exCount > 0 ? round($exSum / $exCount, 1) : 0;

                $totalAvg = ($taskAvg + $exAvg) / 2;

                $studentData['lessons'][$lesson->id] = [
                    'tasks' => $lessonTasks,
                    'exercises' => $lessonExercises,
                    'task_avg' => $taskAvg,
                    'ex_avg' => $exAvg,
                    'total_avg' => $totalAvg
                ];
            }
            $rekapData[] = $studentData;
        }

        // Calculate class averages per lesson
        $averages = [];
        foreach ($lessons as $lesson) {
            $sum = 0; $count = 0;
            foreach ($rekapData as $data) {
                if (isset($data['lessons'][$lesson->id]['total_avg']) && $data['lessons'][$lesson->id]['total_avg'] > 0) {
                    $sum += $data['lessons'][$lesson->id]['total_avg'];
                    $count++;
                }
            }
            $averages[$lesson->id] = $count > 0 ? round($sum / $count, 1) : 0;
        }

        return view('guru.rekap-nilai.show-class', compact('serial', 'classroom', 'students', 'lessons', 'allTasks', 'allExercises', 'rekapData', 'averages'));
    }

    public function downloadClassPdf($serial, $classroomId, $lessonId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $selectedLesson = Lesson::findOrFail($lessonId);
        
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();
            
        $lessons = collect([$selectedLesson]);

        // Group posts (tugas) by lesson
        $allTasks = [];
        foreach ($lessons as $lesson) {
            $posts = Post::where('serial_id', $serial->id)
                ->where('category', 'like', '%"lesson_id":' . $lesson->id . '%')
                ->where('is_task', 1)
                ->where(function($q) use ($classroom) {
                    $q->doesntHave('classrooms')
                      ->orWhereHas('classrooms', function($query) use ($classroom) {
                          $query->where('classrooms.id', $classroom->id);
                      });
                })
                ->orderBy('created_at')
                ->get();
            
            foreach ($posts as $index => $post) {
                $allTasks[$lesson->id][] = [
                    'post' => $post,
                    'title' => $post->title,
                    'number' => $index + 1,
                ];
            }
        }

        // Group exercises by lesson
        $allExercises = [];
        foreach ($lessons as $lesson) {
            // Guru exercises
            $guruExercises = Exercise::where('lesson_id', $lesson->id)
                ->where('is_admin', 0)
                ->orderBy('created_at')
                ->get();
                
            foreach ($guruExercises as $index => $exercise) {
                $allExercises[$lesson->id]['Tambahan'][] = [
                    'exercise' => $exercise,
                    'title' => $exercise->title,
                    'number' => $index + 1,
                ];
            }

            // Admin exercises
            $adminExercises = Exercise::whereHas('lesson', function($q) use ($lesson) {
                    $q->where('mapel_id', $lesson->mapel_id)
                      ->where('category', Lesson::CATEGORY_SOAL);
                })
                ->where('is_admin', 1)
                ->with(['lesson'])
                ->orderBy('created_at')
                ->get()
                ->groupBy(function($item) {
                    return $item->lesson->semester ?? 0;
                });
                
            foreach ($adminExercises as $semester => $exList) {
                $type = match($semester) {
                    1 => 'UH',
                    2 => 'PTS',
                    3 => 'PAS',
                    default => 'Soal'
                };
                
                foreach ($exList as $index => $exercise) {
                    $allExercises[$lesson->id][$type][] = [
                        'exercise' => $exercise,
                        'title' => $exercise->title,
                        'number' => $index + 1,
                    ];
                }
            }
        }

        // Prepare rekap data per student
        $rekapData = [];
        foreach ($students as $student) {
            $studentData = [
                'student' => $student,
                'lessons' => []
            ];

            foreach ($lessons as $lesson) {
                $lessonTasks = [];
                $lessonExercises = [];

                // Get individual task scores
                if (isset($allTasks[$lesson->id])) {
                    foreach ($allTasks[$lesson->id] as $taskInfo) {
                        $task = Task::where('student_id', $student->id)
                            ->where('post_id', $taskInfo['post']->id)
                            ->first();
                        
                        $lessonTasks[] = [
                            'number' => $taskInfo['number'],
                            'title' => $taskInfo['title'],
                            'point' => $task ? $task->point : null,
                        ];
                    }
                }

                // Get individual exercise scores by type
                if (isset($allExercises[$lesson->id])) {
                    foreach ($allExercises[$lesson->id] as $type => $exList) {
                        foreach ($exList as $exInfo) {
                            $exPoint = ExercisePoint::where('student_id', $student->id)
                                ->where('exercise_id', $exInfo['exercise']->id)
                                ->first();
                            
                            $lessonExercises[$type][] = [
                                'number' => $exInfo['number'],
                                'title' => $exInfo['title'],
                                'point' => $exPoint ? $exPoint->exercise_point : null,
                            ];
                        }
                    }
                }

                $studentData['lessons'][$lesson->id] = [
                    'tasks' => $lessonTasks,
                    'exercises' => $lessonExercises,
                ];
            }

            $rekapData[] = $studentData;
        }

        $pdf = Pdf::loadView('guru.rekap-nilai.pdf.class', compact('serial', 'classroom', 'students', 'lessons', 'allTasks', 'allExercises', 'rekapData', 'averages'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('rekap_nilai_'.$classroom->name.'_'.\Str::slug($selectedLesson->name).'.pdf');
    }

    public function showStudent($serial, $classroomId, $studentId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $student = Student::findOrFail($studentId);

        // Get all tasks with points
        $tasks = Task::where('student_id', $student->id)
            ->with(['post.mapel'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all exercise points
        $exercisePoints = ExercisePoint::where('student_id', $student->id)
            ->with(['exercise.lesson.mapel', 'exercise.exerciseType'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get lessons for tasks to map lesson_id to lesson name
        $lessonIds = $tasks->map(function($task) {
            $cat = is_string($task->post->category) ? json_decode($task->post->category, true) : $task->post->category;
            return $cat['lesson_id'] ?? null;
        })->filter()->unique()->toArray();
        $lessonsForTasks = \App\Models\Lesson::whereIn('id', $lessonIds)->pluck('name', 'id');

        return view('guru.rekap-nilai.show-student', compact('serial', 'classroom', 'student', 'tasks', 'exercisePoints', 'lessonsForTasks'));
    }

    public function downloadStudentPdf($serial, $classroomId, $studentId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        $student = Student::findOrFail($studentId);

        // Get all tasks with points
        $tasks = Task::where('student_id', $student->id)
            ->with(['post.mapel'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all exercise points
        $exercisePoints = ExercisePoint::where('student_id', $student->id)
            ->with(['exercise.lesson.mapel', 'exercise.exerciseType'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get lessons for tasks to map lesson_id to lesson name
        $lessonIds = $tasks->map(function($task) {
            $cat = is_string($task->post->category) ? json_decode($task->post->category, true) : $task->post->category;
            return $cat['lesson_id'] ?? null;
        })->filter()->unique()->toArray();
        $lessonsForTasks = \App\Models\Lesson::whereIn('id', $lessonIds)->pluck('name', 'id');

        $pdf = Pdf::loadView('guru.rekap-nilai.pdf.student', compact('serial', 'classroom', 'student', 'tasks', 'exercisePoints', 'lessonsForTasks'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Rekap-Nilai-' . str_replace(' ', '-', $student->name) . '.pdf');
    }
}

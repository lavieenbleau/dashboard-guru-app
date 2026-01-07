<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Mapel;
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
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        
        // Get all students in this classroom
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();

        // Get all mapels
        $mapels = Mapel::all();

        // Get all distinct posts (tugas) grouped by mapel with lesson info
        $allTasks = [];
        foreach ($mapels as $mapel) {
            $posts = Post::where('mapel_id', $mapel->id)
                ->where('is_task', 1)
                ->where('serial_id', $serial->id)
                ->orderBy('created_at')
                ->get();
            
            foreach ($posts as $index => $post) {
                $allTasks[$mapel->id][] = [
                    'post' => $post,
                    'title' => $post->title,
                    'number' => $index + 1,
                ];
            }
        }

        // Get all distinct exercises grouped by mapel and type (UH, PTS, PAS)
        $allExercises = [];
        foreach ($mapels as $mapel) {
            // Group by lesson semester (1=UH, 2=PTS, 3=PAS, 4=Soal Tambahan)
            $exercises = Exercise::whereHas('lesson', function($q) use ($mapel) {
                    $q->where('mapel_id', $mapel->id)
                      ->where('category', 3); // category 3 = soal
                })
                ->with(['lesson'])
                ->orderBy('created_at')
                ->get()
                ->groupBy(function($item) {
                    return $item->lesson->semester ?? 0;
                });

            foreach ($exercises as $semester => $exList) {
                $type = match($semester) {
                    1 => 'UH',
                    2 => 'PTS',
                    3 => 'PAS',
                    4 => 'Tambahan',
                    default => 'Soal'
                };
                
                foreach ($exList as $index => $exercise) {
                    $allExercises[$mapel->id][$type][] = [
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
                'mapels' => []
            ];

            foreach ($mapels as $mapel) {
                $mapelTasks = [];
                $mapelExercises = [];

                // Get individual task scores
                if (isset($allTasks[$mapel->id])) {
                    foreach ($allTasks[$mapel->id] as $taskInfo) {
                        $task = Task::where('student_id', $student->id)
                            ->where('post_id', $taskInfo['post']->id)
                            ->first();
                        
                        $mapelTasks[] = [
                            'number' => $taskInfo['number'],
                            'title' => $taskInfo['title'],
                            'point' => $task ? $task->point : null,
                        ];
                    }
                }

                // Get individual exercise scores by type
                if (isset($allExercises[$mapel->id])) {
                    foreach ($allExercises[$mapel->id] as $type => $exList) {
                        foreach ($exList as $exInfo) {
                            $exPoint = ExercisePoint::where('student_id', $student->id)
                                ->where('exercise_id', $exInfo['exercise']->id)
                                ->first();
                            
                            $mapelExercises[$type][] = [
                                'number' => $exInfo['number'],
                                'title' => $exInfo['title'],
                                'point' => $exPoint ? $exPoint->exercise_point : null,
                            ];
                        }
                    }
                }

                $studentData['mapels'][$mapel->id] = [
                    'tasks' => $mapelTasks,
                    'exercises' => $mapelExercises,
                ];
            }

            $rekapData[] = $studentData;
        }

        return view('guru.rekap-nilai.show-class', compact('serial', 'classroom', 'students', 'mapels', 'rekapData', 'allTasks', 'allExercises'));
    }

    public function downloadClassPdf($serial, $classroomId)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroomId);
        
        // Get all students in this classroom
        $students = Student::where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();

        // Get all mapels
        $mapels = Mapel::all();

        // Get all distinct posts (tugas) grouped by mapel
        $allTasks = [];
        foreach ($mapels as $mapel) {
            $posts = Post::where('mapel_id', $mapel->id)
                ->where('is_task', 1)
                ->where('serial_id', $serial->id)
                ->orderBy('created_at')
                ->get();
            
            foreach ($posts as $index => $post) {
                $allTasks[$mapel->id][] = [
                    'post' => $post,
                    'title' => $post->title,
                    'number' => $index + 1,
                ];
            }
        }

        // Get all distinct exercises grouped by mapel and type
        $allExercises = [];
        foreach ($mapels as $mapel) {
            $exercises = Exercise::whereHas('lesson', function($q) use ($mapel) {
                    $q->where('mapel_id', $mapel->id)
                      ->where('category', 3);
                })
                ->with(['lesson'])
                ->orderBy('created_at')
                ->get()
                ->groupBy(function($item) {
                    return $item->lesson->semester ?? 0;
                });

            foreach ($exercises as $semester => $exList) {
                $type = match($semester) {
                    1 => 'UH',
                    2 => 'PTS',
                    3 => 'PAS',
                    4 => 'Tambahan',
                    default => 'Soal'
                };
                
                foreach ($exList as $index => $exercise) {
                    $allExercises[$mapel->id][$type][] = [
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
                'mapels' => []
            ];

            foreach ($mapels as $mapel) {
                $mapelTasks = [];
                $mapelExercises = [];

                // Get individual task scores
                if (isset($allTasks[$mapel->id])) {
                    foreach ($allTasks[$mapel->id] as $taskInfo) {
                        $task = Task::where('student_id', $student->id)
                            ->where('post_id', $taskInfo['post']->id)
                            ->first();
                        
                        $mapelTasks[] = [
                            'number' => $taskInfo['number'],
                            'title' => $taskInfo['title'],
                            'point' => $task ? $task->point : null,
                        ];
                    }
                }

                // Get individual exercise scores by type
                if (isset($allExercises[$mapel->id])) {
                    foreach ($allExercises[$mapel->id] as $type => $exList) {
                        foreach ($exList as $exInfo) {
                            $exPoint = ExercisePoint::where('student_id', $student->id)
                                ->where('exercise_id', $exInfo['exercise']->id)
                                ->first();
                            
                            $mapelExercises[$type][] = [
                                'number' => $exInfo['number'],
                                'title' => $exInfo['title'],
                                'point' => $exPoint ? $exPoint->exercise_point : null,
                            ];
                        }
                    }
                }

                $studentData['mapels'][$mapel->id] = [
                    'tasks' => $mapelTasks,
                    'exercises' => $mapelExercises,
                ];
            }

            $rekapData[] = $studentData;
        }

        $pdf = Pdf::loadView('guru.rekap-nilai.pdf.class', compact('serial', 'classroom', 'students', 'mapels', 'rekapData', 'allTasks', 'allExercises'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('Rekap-Nilai-' . str_replace(' ', '-', $classroom->name) . '.pdf');
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

        return view('guru.rekap-nilai.show-student', compact('serial', 'classroom', 'student', 'tasks', 'exercisePoints'));
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

        $pdf = Pdf::loadView('guru.rekap-nilai.pdf.student', compact('serial', 'classroom', 'student', 'tasks', 'exercisePoints'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Rekap-Nilai-' . str_replace(' ', '-', $student->name) . '.pdf');
    }
}

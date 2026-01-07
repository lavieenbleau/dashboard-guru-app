<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Support\Facades\DB;

class LaporanHarianController extends Controller
{
    public function index($serial, Request $request)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get selected date or use today
        $selectedDate = $request->get('date', date('Y-m-d'));
        
        // Get task submissions for selected date with lesson category
        $taskActivities = DB::table('tasks')
            ->join('students', 'tasks.student_id', '=', 'students.id')
            ->join('posts', 'tasks.post_id', '=', 'posts.id')
            ->leftJoin('lessons', function($join) {
                $join->whereRaw('JSON_EXTRACT(posts.category, "$.lesson_id") = lessons.id');
            })
            ->leftJoin('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->select(
                'tasks.id',
                'tasks.student_id',
                'tasks.created_at',
                'tasks.description as submission_description',
                'tasks.attachment',
                'tasks.point',
                'students.name as student_name',
                'students.nis as student_nis',
                'classrooms.name as classroom_name',
                'posts.title as task_title',
                'lessons.category as lesson_category',
                'lessons.semester as lesson_semester',
                'lessons.name as lesson_name',
                DB::raw("'task' as source_type")
            )
            ->where('tasks.serial_id', $serial->id)
            ->whereDate('tasks.created_at', $selectedDate)
            ->get();

        // Get exercise point submissions for selected date
        $exerciseActivities = DB::table('exercise_points')
            ->join('students', 'exercise_points.student_id', '=', 'students.id')
            ->join('exercises', 'exercise_points.exercise_id', '=', 'exercises.id')
            ->join('lessons', 'exercises.lesson_id', '=', 'lessons.id')
            ->leftJoin('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->select(
                'exercise_points.id',
                'exercise_points.student_id',
                'exercise_points.updated_at as created_at',
                DB::raw("'Jawaban soal' as submission_description"),
                DB::raw("NULL as attachment"),
                'exercise_points.exercise_point as point',
                'students.name as student_name',
                'students.nis as student_nis',
                'classrooms.name as classroom_name',
                'exercises.title as task_title',
                'lessons.category as lesson_category',
                'lessons.semester as lesson_semester',
                'lessons.name as lesson_name',
                DB::raw("'exercise' as source_type")
            )
            ->where('exercise_points.serial_id', $serial->id)
            ->whereDate('exercise_points.updated_at', $selectedDate)
            ->whereNotNull('exercise_points.answer')
            ->get();

        // Merge both collections and map activity types
        $activities = $taskActivities->merge($exerciseActivities)
            ->sortByDesc('created_at')
            ->map(function($item) {
                // Determine activity type based on lesson category
                if ($item->lesson_category == 2) {
                    $item->activity_type = 'Tugas';
                    $item->badge_color = 'success';
                } elseif ($item->lesson_category == 3) {
                    // Check semester for soal sub-category
                    if ($item->lesson_semester == 1) {
                        $item->activity_type = 'Ulangan Harian';
                        $item->badge_color = 'primary';
                    } elseif ($item->lesson_semester == 2) {
                        $item->activity_type = 'PTS';
                        $item->badge_color = 'warning';
                    } elseif ($item->lesson_semester == 3) {
                        $item->activity_type = 'PAS';
                        $item->badge_color = 'danger';
                    } elseif ($item->lesson_semester == 4) {
                        $item->activity_type = 'Soal Tambahan';
                        $item->badge_color = 'info';
                    } else {
                        $item->activity_type = 'Soal';
                        $item->badge_color = 'secondary';
                    }
                } elseif ($item->lesson_category == 1) {
                    $item->activity_type = 'Materi';
                    $item->badge_color = 'dark';
                } else {
                    $item->activity_type = 'Lainnya';
                    $item->badge_color = 'secondary';
                }
                return $item;
            })
            ->values();
        
        // Get dates with activities for current month (both tasks and exercises)
        $year = date('Y', strtotime($selectedDate));
        $month = date('m', strtotime($selectedDate));
        
        $taskDates = DB::table('tasks')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('serial_id', $serial->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();
        
        $exerciseDates = DB::table('exercise_points')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('serial_id', $serial->id)
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->whereNotNull('answer')
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->pluck('count', 'date')
            ->toArray();
        
        // Merge both dates arrays
        $datesWithActivities = [];
        foreach($taskDates as $date => $count) {
            $datesWithActivities[$date] = ($datesWithActivities[$date] ?? 0) + $count;
        }
        foreach($exerciseDates as $date => $count) {
            $datesWithActivities[$date] = ($datesWithActivities[$date] ?? 0) + $count;
        }

        return view('guru.laporan-harian.index', compact('serial', 'selectedDate', 'activities', 'datesWithActivities'));
    }

    public function show($serial, $date)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get task submissions for specific date
        $taskActivities = DB::table('tasks')
            ->join('students', 'tasks.student_id', '=', 'students.id')
            ->join('posts', 'tasks.post_id', '=', 'posts.id')
            ->leftJoin('lessons', function($join) {
                $join->whereRaw('JSON_EXTRACT(posts.category, "$.lesson_id") = lessons.id');
            })
            ->leftJoin('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->select(
                'tasks.id',
                'tasks.student_id',
                'tasks.created_at',
                'tasks.description as submission_description',
                'tasks.attachment',
                'tasks.point',
                'students.name as student_name',
                'students.nis as student_nis',
                'classrooms.name as classroom_name',
                'posts.title as task_title',
                'lessons.category as lesson_category',
                'lessons.semester as lesson_semester',
                'lessons.name as lesson_name',
                DB::raw("'task' as source_type")
            )
            ->where('tasks.serial_id', $serial->id)
            ->whereDate('tasks.created_at', $date)
            ->get();

        // Get exercise point submissions for specific date
        $exerciseActivities = DB::table('exercise_points')
            ->join('students', 'exercise_points.student_id', '=', 'students.id')
            ->join('exercises', 'exercise_points.exercise_id', '=', 'exercises.id')
            ->join('lessons', 'exercises.lesson_id', '=', 'lessons.id')
            ->leftJoin('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->select(
                'exercise_points.id',
                'exercise_points.student_id',
                'exercise_points.updated_at as created_at',
                DB::raw("'Jawaban soal' as submission_description"),
                DB::raw("NULL as attachment"),
                'exercise_points.exercise_point as point',
                'students.name as student_name',
                'students.nis as student_nis',
                'classrooms.name as classroom_name',
                'exercises.title as task_title',
                'lessons.category as lesson_category',
                'lessons.semester as lesson_semester',
                'lessons.name as lesson_name',
                DB::raw("'exercise' as source_type")
            )
            ->where('exercise_points.serial_id', $serial->id)
            ->whereDate('exercise_points.updated_at', $date)
            ->whereNotNull('exercise_points.answer')
            ->get();

        // Merge both collections and map activity types
        $activities = $taskActivities->merge($exerciseActivities)
            ->sortByDesc('created_at')
            ->map(function($item) {
                // Determine activity type based on lesson category
                if ($item->lesson_category == 2) {
                    $item->activity_type = 'Tugas';
                    $item->badge_color = 'success';
                } elseif ($item->lesson_category == 3) {
                    // Check semester for soal sub-category
                    if ($item->lesson_semester == 1) {
                        $item->activity_type = 'Ulangan Harian';
                        $item->badge_color = 'primary';
                    } elseif ($item->lesson_semester == 2) {
                        $item->activity_type = 'PTS';
                        $item->badge_color = 'warning';
                    } elseif ($item->lesson_semester == 3) {
                        $item->activity_type = 'PAS';
                        $item->badge_color = 'danger';
                    } elseif ($item->lesson_semester == 4) {
                        $item->activity_type = 'Soal Tambahan';
                        $item->badge_color = 'info';
                    } else {
                        $item->activity_type = 'Soal';
                        $item->badge_color = 'secondary';
                    }
                } elseif ($item->lesson_category == 1) {
                    $item->activity_type = 'Materi';
                    $item->badge_color = 'dark';
                } else {
                    $item->activity_type = 'Lainnya';
                    $item->badge_color = 'secondary';
                }
                return $item;
            })
            ->values();

        return view('guru.laporan-harian.show', compact('serial', 'date', 'activities'));
    }

    public function grade(Request $request, $serial, $id)
    {
        $request->validate([
            'point' => 'required|numeric|min:0|max:100',
            'source_type' => 'required|in:task,exercise',
        ]);

        if ($request->source_type == 'task') {
            $task = \App\Models\Task::findOrFail($id);
            $task->update([
                'point' => $request->point,
            ]);
        } else {
            $exercisePoint = \App\Models\ExercisePoint::findOrFail($id);
            $exercisePoint->update([
                'exercise_point' => $request->point,
            ]);
        }

        return back()->with('success', 'Nilai berhasil disimpan!');
    }

}
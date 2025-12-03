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
        $activities = DB::table('tasks')
            ->join('students', 'tasks.student_id', '=', 'students.id')
            ->join('posts', 'tasks.post_id', '=', 'posts.id')
            ->leftJoin('lessons', function($join) {
                $join->whereRaw('JSON_EXTRACT(posts.category, "$.lesson_id") = lessons.id');
            })
            ->select(
                'tasks.id',
                'tasks.student_id',
                'tasks.created_at',
                'tasks.description as submission_description',
                'tasks.attachment',
                'tasks.point',
                'students.name as student_name',
                'posts.title as task_title',
                'lessons.category as lesson_category',
                'lessons.semester as lesson_semester',
                'lessons.name as lesson_name'
            )
            ->where('tasks.serial_id', $serial->id)
            ->whereDate('tasks.created_at', $selectedDate)
            ->orderBy('tasks.created_at', 'desc')
            ->get()
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
            });
        
        // Get dates with activities for current month
        $year = date('Y', strtotime($selectedDate));
        $month = date('m', strtotime($selectedDate));
        
        $datesWithActivities = DB::table('tasks')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('serial_id', $serial->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        return view('guru.laporan-harian.index', compact('serial', 'selectedDate', 'activities', 'datesWithActivities'));
    }

    public function show($serial, $date)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get task submissions for specific date with student and post info
        $activities = DB::table('tasks')
            ->join('students', 'tasks.student_id', '=', 'students.id')
            ->join('posts', 'tasks.post_id', '=', 'posts.id')
            ->select(
                'tasks.id',
                'tasks.student_id',
                'tasks.created_at',
                'tasks.description as submission_description',
                'tasks.attachment',
                'students.name as student_name',
                'posts.title as task_title'
            )
            ->where('tasks.serial_id', $serial->id)
            ->whereDate('tasks.created_at', $date)
            ->orderBy('tasks.created_at', 'desc')
            ->get();

        return view('guru.laporan-harian.show', compact('serial', 'date', 'activities'));
    }

    public function grade(Request $request, $serial, $taskId)
    {
        $request->validate([
            'point' => 'required|numeric|min:0|max:100',
        ]);

        $task = \App\Models\Task::findOrFail($taskId);
        $task->update([
            'point' => $request->point,
        ]);

        return back()->with('success', 'Nilai berhasil disimpan!');
    }

}
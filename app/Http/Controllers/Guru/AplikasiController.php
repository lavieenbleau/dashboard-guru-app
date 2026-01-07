<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Serial;
use Illuminate\Support\Facades\Auth;

class AplikasiController extends Controller
{
    public function index()
    {
        // daftar aplikasi (serials)
        $serials = Serial::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return view('guru.aplikasi.index', compact('serials'));
    }

    public function dashboard($serial)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        
        // Get statistics - using Posts for Materi & Tugas, Exercises for Soal
        $stats = [
            // Materi: posts with is_task = 0
            'materi' => \App\Models\Post::where('serial_id', $serial->id)
                ->where('is_task', 0)
                ->count(),
            
            // Tugas: posts with is_task = 1
            'tugas' => \App\Models\Post::where('serial_id', $serial->id)
                ->where('is_task', 1)
                ->count(),
            
            // Soal: exercises (custom soal from teacher + admin soal shared to classrooms)
            'soal' => \App\Models\Exercise::where(function($q) use ($serial) {
                // Custom soal created by teacher for this serial
                $q->where('serial_id', $serial->id)
                  ->where('is_admin', 0);
                
                // OR admin soal that have been shared to this serial's classrooms
                $classroomIds = \App\Models\Classroom::where('serial_id', $serial->id)->pluck('id');
                if ($classroomIds->isNotEmpty()) {
                    $q->orWhere(function($subQ) use ($classroomIds) {
                        $subQ->where('is_admin', 1)
                             ->whereHas('classrooms', function($query) use ($classroomIds) {
                                 $query->whereIn('classrooms.id', $classroomIds);
                             });
                    });
                }
            })
            ->count(),
            
            'classrooms' => \App\Models\Classroom::where('serial_id', $serial->id)->count(),
            'students' => \App\Models\Student::whereHas('classroom', function($q) use ($serial) {
                $q->where('serial_id', $serial->id);
            })->count(),
            'online_meetings' => \App\Models\OnlineMeeting::where('serial_id', $serial->id)
                ->where('start_time', '>=', now())
                ->count(),
            'tasks_pending' => \App\Models\Task::where('serial_id', $serial->id)
                ->whereNull('point')
                ->count(),
        ];
        
        // Upcoming meetings
        $upcomingMeetings = \App\Models\OnlineMeeting::where('serial_id', $serial->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->with('classroom')
            ->limit(3)
            ->get();
        
        // Recent activities (from tasks) - only for this serial
        $recentActivities = \App\Models\Task::with(['student', 'post'])
            ->where('serial_id', $serial->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('guru.aplikasi.dashboard', compact('serial', 'stats', 'upcomingMeetings', 'recentActivities'));
    }
}

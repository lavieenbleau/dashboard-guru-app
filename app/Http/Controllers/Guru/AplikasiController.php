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
        
        // Get statistics
        $stats = [
            'materi' => \App\Models\Lesson::where('category', 1)->count(),
            'tugas' => \App\Models\Lesson::where('category', 2)->count(),
            'soal' => \App\Models\Lesson::where('category', 3)->count(),
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
        
        // Recent activities (from tasks)
        $recentActivities = \App\Models\Task::with(['student', 'post'])
            ->where('serial_id', $serial->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('guru.aplikasi.dashboard', compact('serial', 'stats', 'upcomingMeetings', 'recentActivities'));
    }
}

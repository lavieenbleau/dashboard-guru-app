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
            // Materi Admin: lesson items from product's assigned lessons
            'materi_admin' => \App\Models\LessonItem::whereIn('lesson_id', json_decode($serial->product->lesson_id ?? '[]', true) ?? [])->count(),
            
            // Materi Guru: custom posts from teacher
            'materi_guru' => \App\Models\Post::where('serial_id', $serial->id)
                ->where('is_task', 0)
                ->count(),
            
            // Total Materi
            'materi' => (\App\Models\Post::where('serial_id', $serial->id)
                ->where('is_task', 0)
                ->count()) + (\App\Models\LessonItem::whereHas('lesson', function($q) {
                    $q->where('category', \App\Models\Lesson::CATEGORY_MATERI);
                })->count()),
            
            // Tugas: posts with is_task = 1
            'tugas' => \App\Models\Post::where('serial_id', $serial->id)
                ->where('is_task', 1)
                ->count(),
            
            // Soal: exercises for this serial
            'soal' => \App\Models\Exercise::where('serial_id', $serial->id)->count(),
            
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

        // Check for over-capacity classrooms
        $maxStudentsPerClass = $serial->getMaxStudentsPerClass();
        $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)
            ->withCount('students')
            ->get();
        
        $overCapacityClasses = $classrooms->filter(function ($c) use ($maxStudentsPerClass) {
            return $c->students_count > $maxStudentsPerClass;
        });
        
        // Check class limit vs paket
        $maxClasses = (int) $serial->paket;
        $currentClassCount = $classrooms->count();
        $classLimitExceeded = $currentClassCount > $maxClasses;

        // Define stat cards for dashboard view
        $statCards = [
            [
                'key' => 'materi',
                'label' => 'Total Materi',
                'icon' => 'library',
                'iconClass' => 'indigo',
            ],
            [
                'key' => 'materi_admin',
                'label' => 'Materi Pusat',
                'icon' => 'book-open',
                'iconClass' => 'indigo',
                'iconStyle' => 'background:#F4F4F5; color:#374151;',
            ],
            [
                'key' => 'soal',
                'label' => 'Bank Soal',
                'icon' => 'file-text',
                'iconClass' => 'cyan',
            ],
            [
                'key' => 'students',
                'label' => 'Total Siswa',
                'icon' => 'users',
                'iconClass' => 'emerald',
            ],
            [
                'key' => 'tasks_pending',
                'label' => 'Perlu Dinilai',
                'icon' => 'clock',
                'iconClass' => 'amber',
            ],
            [
                'key' => 'online_meetings',
                'label' => 'Meeting',
                'icon' => 'video',
                'iconClass' => 'rose',
            ],
        ];

        return view('guru.aplikasi.dashboard', compact(
            'serial', 'stats', 'statCards', 'upcomingMeetings', 'recentActivities',
            'overCapacityClasses', 'maxStudentsPerClass', 'classLimitExceeded', 'maxClasses', 'currentClassCount'
        ));
    }
}

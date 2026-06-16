<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Serial;
use Illuminate\Support\Facades\Auth;

class AplikasiController extends Controller
{
    public function index()
    {
        // daftar aplikasi (serials) dikelompokkan per produk dan ambil expired_at terjauh
        $serials = Serial::with('product')
            ->where('user_id', auth()->id())
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                return $group->sortByDesc('expired_at')->first();
            })
            ->values();

        if ($serials->isEmpty()) {
            return view('guru.aplikasi.empty-state');
        }

        return view('guru.aplikasi.index', compact('serials'));
    }

    public function activateSerial(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'serial_code' => 'required|string|max:255',
        ], [
            'serial_code.required' => 'Kode serial wajib diisi.',
        ]);

        $serialCode = trim($request->serial_code);
        
        $serial = Serial::where('serial', $serialCode)->first();

        // 1. Validasi Serial Tidak Ditemukan
        if (!$serial) {
            return back()->with('error', 'Kode serial tidak ditemukan.');
        }

        // 2. Validasi Duplikasi (Sudah dipakai oleh user ini)
        if ($serial->user_id == auth()->id()) {
            return back()->with('error', 'Serial sudah terhubung ke akun Anda.');
        }

        // 3. Validasi Kuota (Sudah dipakai oleh user lain)
        if (!is_null($serial->user_id)) {
            return back()->with('error', 'Kode serial sudah digunakan.');
        }

        // 4. Validasi Kedaluwarsa
        if ($serial->expired_at && \Carbon\Carbon::parse($serial->expired_at)->isPast()) {
            return back()->with('error', 'Kode serial telah kedaluwarsa.');
        }

        // Cek apakah guru sudah punya serial untuk product_id ini
        $existingSerial = Serial::where('user_id', auth()->id())
                                ->where('product_id', $serial->product_id)
                                ->orderByDesc('expired_at')
                                ->first();

        if ($existingSerial) {
            // KASUS 2: PERPANJANGAN
            // Tambahkan masa aktif ke serial lama
            $baseDate = $existingSerial->expired_at && \Carbon\Carbon::parse($existingSerial->expired_at)->isFuture() 
                        ? \Carbon\Carbon::parse($existingSerial->expired_at) 
                        : now();
            
            $existingSerial->expired_at = $baseDate->addMonths((int)$serial->active);
            $existingSerial->save();

            // Catat log perpanjangan untuk serial lama
            \App\Models\SerialLog::create([
                'serial_id' => $existingSerial->id,
                'active' => $serial->active,
                'status' => 'Perpanjang',
            ]);

            // Konsumsi serial baru sebagai voucher
            $serial->user_id = auth()->id();
            $serial->expired_at = now();
            $serial->save();

            return back()->with('success', 'Masa aktif produk berhasil diperpanjang.');
        } else {
            // KASUS 1: PRODUK BARU
            $serial->user_id = auth()->id();
            $serial->expired_at = now()->addMonths((int)$serial->active);
            $serial->save();

            \App\Models\SerialLog::create([
                'serial_id' => $serial->id,
                'active' => $serial->active,
                'status' => 'Baru',
            ]);

            return back()->with('success', 'Produk berhasil ditambahkan ke akun Anda.');
        }
    }

    public function dashboard($serial)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        
        $materiAdminCount = \App\Models\LessonItem::whereIn('lesson_id', json_decode($serial->product->lesson_id ?? '[]', true) ?? [])->count();
        $materiGuruCount = \App\Models\Post::where('serial_id', $serial->id)
            ->where('is_task', 0)
            ->count();

        // Get statistics - using Posts for Materi & Tugas, Exercises for Soal
        $stats = [
            // Materi Admin: lesson items from product's assigned lessons
            'materi_admin' => $materiAdminCount,
            
            // Materi Guru: custom posts from teacher
            'materi_guru' => $materiGuruCount,
            
            // Total Materi
            'materi' => $materiAdminCount + $materiGuruCount,
            
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

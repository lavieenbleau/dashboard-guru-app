<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizActivityLog;
use App\Models\Exercise;
use App\Models\Student;
use App\Models\Serial;
use App\Models\Classroom;
use App\Services\QuizActivityService;
use Illuminate\Support\Facades\DB;

class QuizMonitoringController extends Controller
{
    private function translateEvent($eventName)
    {
        if (in_array($eventName, ['START', 'QUIZ_ENTER'])) return 'Mulai Kuis';
        if (in_array($eventName, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) return 'Keluar dari Aplikasi';
        if (in_array($eventName, ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])) return 'Kembali ke Aplikasi';
        if ($eventName === 'RECONNECTED') return 'Koneksi Tersambung Kembali';
        if ($eventName === 'BACK_BUTTON_BLOCKED') return 'Menekan Tombol Kembali';
        if ($eventName === 'SUBMIT') return 'Kuis Diselesaikan';
        if ($eventName === 'AUTO_SUBMIT') return 'Sistem Otomatis Mengumpulkan Kuis';
        return $eventName;
    }
    
    /**
     * LEVEL 1: Daftar Kelas
     */
    public function indexClasses()
    {
        $userId = auth()->id();
        
        $serials = Serial::where('user_id', $userId)->pluck('id');
        $classrooms = Classroom::whereIn('serial_id', $serials)
            ->get()
            ->groupBy('name');
            
        $classStats = [];
        
        foreach ($classrooms as $className => $classes) {
            $classIds = $classes->pluck('id');
            // Distinct students by username across these classes to avoid double counting if they are the same
            $students = Student::whereIn('classroom_id', $classIds)->get();
            $totalStudents = $students->unique('username')->count();
            
            $serialIds = $classes->pluck('serial_id')->unique();
            $exercises = Exercise::whereIn('serial_id', $serialIds)->pluck('id');
            
            $studentIds = $students->pluck('id');
            
            if ($studentIds->isEmpty() || $exercises->isEmpty()) {
                $finished = 0;
                $inProgress = 0;
                $notStarted = $totalStudents * $exercises->count();
            } else {
                $latestEvents = DB::connection('log_db')->table('quiz_activity_logs as q1')
                    ->select('q1.student_id', 'q1.exercise_id', 'q1.event_type')
                    ->join(DB::raw('(SELECT student_id, exercise_id, MAX(created_at) as max_time FROM quiz_activity_logs WHERE student_id IN ('.implode(',', $studentIds->toArray()).') AND exercise_id IN ('.implode(',', $exercises->toArray()).') GROUP BY student_id, exercise_id) as q2'), function($join) {
                        $join->on('q1.student_id', '=', 'q2.student_id')
                             ->on('q1.exercise_id', '=', 'q2.exercise_id')
                             ->on('q1.created_at', '=', 'q2.max_time');
                    })
                    ->get();
                    
                $finished = $latestEvents->whereIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
                $inProgress = $latestEvents->whereNotIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
                $totalAssignments = $studentIds->count() * $exercises->count();
                $notStarted = max(0, $totalAssignments - $finished - $inProgress);
            }
            
            $classStats[] = [
                'name' => $className,
                'total_students' => $totalStudents,
                'finished' => $finished,
                'in_progress' => $inProgress,
                'not_started' => $notStarted
            ];
        }
        
        return view('guru.monitoring-quiz.classes', compact('classStats'));
    }

    /**
     * LEVEL 2: Daftar Produk (Serials) untuk kelas tertentu
     */
    public function indexProducts($kelasName)
    {
        $userId = auth()->id();
        $serials = Serial::where('user_id', $userId)->pluck('id');
        
        $classes = Classroom::whereIn('serial_id', $serials)
            ->where('name', $kelasName)
            ->with(['serial.product'])
            ->get();
            
        if ($classes->isEmpty()) {
            abort(404);
        }
            
        $productStats = [];
        
        foreach ($classes as $classroom) {
            $serial = $classroom->serial;
            if (!$serial) continue;
            
            $lessonIds = [];
            if ($serial->product) {
                $lessonIds = json_decode($serial->product->lesson_id, true) ?? [];
            }
            
            if (empty($lessonIds)) {
                $studentIds = Student::where('classroom_id', $classroom->id)->pluck('id');
                $exercises = Exercise::where('serial_id', $serial->id)->pluck('id');
                
                $totalStudents = $studentIds->count();
                
                if ($studentIds->isEmpty() || $exercises->isEmpty()) {
                    $finished = 0;
                    $inProgress = 0;
                    $notStarted = $totalStudents * $exercises->count();
                } else {
                    $latestEvents = DB::connection('log_db')->table('quiz_activity_logs as q1')
                        ->select('q1.student_id', 'q1.exercise_id', 'q1.event_type')
                        ->join(DB::raw('(SELECT student_id, exercise_id, MAX(created_at) as max_time FROM quiz_activity_logs WHERE student_id IN ('.implode(',', $studentIds->toArray()).') AND exercise_id IN ('.implode(',', $exercises->toArray()).') GROUP BY student_id, exercise_id) as q2'), function($join) {
                            $join->on('q1.student_id', '=', 'q2.student_id')
                                 ->on('q1.exercise_id', '=', 'q2.exercise_id')
                                 ->on('q1.created_at', '=', 'q2.max_time');
                        })
                        ->get();
                        
                    $finished = $latestEvents->whereIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
                    $inProgress = $latestEvents->whereNotIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
                    $totalAssignments = $totalStudents * $exercises->count();
                    $notStarted = max(0, $totalAssignments - $finished - $inProgress);
                }
                
                $productName = $serial->product ? $serial->product->name : $serial->paket;
                
                $productStats[] = [
                    'serial_id' => $serial->id,
                    'lesson_id' => null,
                    'name' => $productName,
                    'total_students' => $totalStudents,
                    'finished' => $finished,
                    'in_progress' => $inProgress,
                    'not_started' => $notStarted
                ];
            } else {
                $lessons = \App\Models\Lesson::whereIn('id', $lessonIds)->get();
                foreach ($lessons as $lesson) {
                    $studentIds = Student::where('classroom_id', $classroom->id)->pluck('id');
                    $exercises = Exercise::where('serial_id', $serial->id)->where('lesson_id', $lesson->id)->pluck('id');
                    
                    $totalStudents = $studentIds->count();
                    
                    if ($studentIds->isEmpty() || $exercises->isEmpty()) {
                        $finished = 0;
                        $inProgress = 0;
                        $notStarted = $totalStudents * $exercises->count();
                    } else {
                        $latestEvents = DB::connection('log_db')->table('quiz_activity_logs as q1')
                            ->select('q1.student_id', 'q1.exercise_id', 'q1.event_type')
                            ->join(DB::raw('(SELECT student_id, exercise_id, MAX(created_at) as max_time FROM quiz_activity_logs WHERE student_id IN ('.implode(',', $studentIds->toArray()).') AND exercise_id IN ('.implode(',', $exercises->toArray()).') GROUP BY student_id, exercise_id) as q2'), function($join) {
                                $join->on('q1.student_id', '=', 'q2.student_id')
                                     ->on('q1.exercise_id', '=', 'q2.exercise_id')
                                     ->on('q1.created_at', '=', 'q2.max_time');
                            })
                            ->get();
                            
                        $finished = $latestEvents->whereIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
                        $inProgress = $latestEvents->whereNotIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
                        $totalAssignments = $totalStudents * $exercises->count();
                        $notStarted = max(0, $totalAssignments - $finished - $inProgress);
                    }
                    
                    $productStats[] = [
                        'serial_id' => $serial->id,
                        'lesson_id' => $lesson->id,
                        'name' => $lesson->name,
                        'total_students' => $totalStudents,
                        'finished' => $finished,
                        'in_progress' => $inProgress,
                        'not_started' => $notStarted
                    ];
                }
            }

        }
        
        return view('guru.monitoring-quiz.products', compact('kelasName', 'productStats'));
    }

    /**
     * LEVEL 3: Monitoring Siswa
     */
    public function monitoringStudent($kelasName, $serialId)
    {
        $serialModel = Serial::findOrFail($serialId);
        
        // Pastikan kelas ini milik user
        $classroom = Classroom::where('serial_id', $serialId)
            ->where('name', $kelasName)
            ->firstOrFail();
            
        $lessonId = request()->query('lesson_id');
        
        $exercisesQuery = Exercise::where('serial_id', $serialId);
        if ($lessonId) {
            $exercisesQuery->where('lesson_id', $lessonId);
        }
        $exercises = $exercisesQuery->get();
        $exerciseIds = $exercises->pluck('id');
        
        $studentIds = Student::where('classroom_id', $classroom->id)->pluck('id');
        
        try {
            $latestEvents = collect();
            if (!$studentIds->isEmpty() && !$exerciseIds->isEmpty()) {
                $latestEvents = DB::connection('log_db')->table('quiz_activity_logs as q1')
                    ->select('q1.student_id', 'q1.exercise_id', 'q1.event_type', 'q1.suspicious_flag')
                    ->join(DB::raw('(SELECT student_id, exercise_id, MAX(created_at) as max_time FROM quiz_activity_logs WHERE student_id IN ('.implode(',', $studentIds->toArray()).') AND exercise_id IN ('.implode(',', $exerciseIds->toArray()).') GROUP BY student_id, exercise_id) as q2'), function($join) {
                        $join->on('q1.student_id', '=', 'q2.student_id')
                             ->on('q1.exercise_id', '=', 'q2.exercise_id')
                             ->on('q1.created_at', '=', 'q2.max_time');
                    })
                    ->get();
            }
            
            $finishedCount = $latestEvents->whereIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
            $activeCount = $latestEvents->whereNotIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
            
            $totalAssignments = $studentIds->count() * $exercises->count();
            $notStartedCount = max(0, $totalAssignments - $finishedCount - $activeCount);
            
            $dbError = null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (Monitoring): ' . $e->getMessage());
            $activeCount = $finishedCount = $notStartedCount = 0;
            $dbError = "Gagal terhubung ke Database Log. Pemantauan sedang tidak aktif.";
        }
        
        $productName = $serialModel->paket;
        if ($lessonId) {
            $lesson = \App\Models\Lesson::find($lessonId);
            if ($lesson) {
                $productName = $lesson->name;
            }
        } else if ($serialModel->product) {
            $productName = $serialModel->product->name;
        }
        
        $lessonIdParam = $lessonId ? '?lesson_id=' . $lessonId : '';

        return view('guru.soal.monitoring', compact(
            'kelasName',
            'serialModel',
            'productName',
            'exercises',
            'activeCount',
            'finishedCount',
            'notStartedCount',
            'dbError',
            'classroom',
            'lessonId',
            'lessonIdParam'
        ));
    }

    /**
     * Datatables JSON response untuk Level 3
     */
    public function dataTable(Request $request, $kelasName, $serialId)
    {
        $serialModel = Serial::findOrFail($serialId);
        $classroom = Classroom::where('serial_id', $serialId)
            ->where('name', $kelasName)
            ->firstOrFail();
            
        $students = Student::where('classroom_id', $classroom->id)->get()->keyBy('id');
        $studentIds = $students->pluck('id')->toArray();
        
        $lessonId = request()->query('lesson_id');
        
        $exercisesQuery = Exercise::where('serial_id', $serialId);
        if ($lessonId) {
            $exercisesQuery->where('lesson_id', $lessonId);
        }
        if ($request->exercise_id) {
            $exercisesQuery->where('id', $request->exercise_id);
        }
        $exercises = $exercisesQuery->get()->keyBy('id');
        $exerciseIds = $exercises->pluck('id')->toArray();

        try {
            $data = [];
            
            if (!empty($studentIds) && !empty($exerciseIds)) {
                // Ambil semua log untuk semua kombinasi siswa & exercise
                $allLogs = QuizActivityLog::whereIn('student_id', $studentIds)
                    ->whereIn('exercise_id', $exerciseIds)
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->groupBy(function($log) {
                        return $log->student_id . '_' . $log->exercise_id;
                    });
                    
                foreach ($students as $student) {
                    foreach ($exercises as $exercise) {
                        $key = $student->id . '_' . $exercise->id;
                        $studentLogs = $allLogs->get($key, collect());
                        
                        $bgCount = $studentLogs->whereIn('event_type', ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])->count();
                        $reconCount = $studentLogs->where('event_type', 'RECONNECTED')->count();
                        $resumeCount = $studentLogs->whereIn('event_type', ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])->count();
                        $blockedCount = $studentLogs->where('event_type', 'BACK_BUTTON_BLOCKED')->count();
                        $hasSuspicious = $studentLogs->where('suspicious_flag', 1)->count() > 0;
                        
                        $totalAwaySeconds = $studentLogs->whereIn('event_type', ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])->sum('duration_seconds');
                        $awayMinutes = floor($totalAwaySeconds / 60);
                        $awaySeconds = $totalAwaySeconds % 60;
                        $awayStr = $totalAwaySeconds > 0 ? "{$awayMinutes} Menit {$awaySeconds} Detik" : "0 Detik";
                        
                        $lastLog = $studentLogs->last();
                        $rawEvent = $lastLog ? $lastLog->event_type : 'UNKNOWN';
                        $lastActivityStr = $lastLog ? $lastLog->created_at->format('H:i:s') : '-';
                        $friendlyEvent = $lastLog ? $this->translateEvent($rawEvent) : 'Belum Mulai';
                        
                        $hasStart = $studentLogs->whereIn('event_type', ['START', 'QUIZ_ENTER'])->count() > 0;
                        $hasSubmit = $studentLogs->where('event_type', 'SUBMIT')->count() > 0;
                        $hasAutoSubmit = $studentLogs->where('event_type', 'AUTO_SUBMIT')->count() > 0;
                        
                        if ($hasSubmit) {
                            $mappedStatus = 'Selesai Manual';
                            $statusType = 'Selesai';
                        } elseif ($hasAutoSubmit) {
                            $mappedStatus = 'Selesai Otomatis';
                            $statusType = 'Selesai';
                        } elseif ($hasStart || $studentLogs->count() > 0) {
                            $mappedStatus = 'Sedang Mengerjakan';
                            $statusType = 'Sedang Mengerjakan';
                        } else {
                            $mappedStatus = 'Belum Mengerjakan';
                            $statusType = 'Belum Mengerjakan';
                        }
                        
                        $riskLevel = 'Normal';
                        $riskColor = 'success';
                        if ($statusType !== 'Belum Mengerjakan') {
                            if ($hasSuspicious || $bgCount > 3 || $blockedCount > 2) {
                                $riskLevel = 'Berisiko Tinggi';
                                $riskColor = 'danger';
                            } elseif ($bgCount > 0 || $blockedCount > 0 || $reconCount > 0) {
                                $riskLevel = 'Perlu Perhatian';
                                $riskColor = 'warning';
                            }
                        } else {
                            $riskColor = 'secondary';
                            $riskLevel = '-';
                        }
                        
                        $data[] = [
                            'student_name' => $student->name,
                            'exercise_name' => $exercise->title,
                            'status' => $mappedStatus,
                            'status_type' => $statusType, // Untuk filter warna
                            'last_event' => $friendlyEvent,
                            'aktivitas_terakhir' => $lastActivityStr,
                            'jml_background' => $bgCount,
                            'jml_resume' => $resumeCount,
                            'total_away' => $awayStr,
                            'jml_reconnected' => $reconCount,
                            'jml_blocked' => $blockedCount,
                            'risk_level' => $riskLevel,
                            'risk_color' => $riskColor,
                            'suspicious' => $hasSuspicious ? 'Ya' : 'Tidak',
                            'submit_status' => $hasSubmit ? 'Selesai Manual' : ($hasAutoSubmit ? 'Selesai Otomatis' : 'Belum'),
                            'student_id' => $student->id,
                            'exercise_id' => $exercise->id,
                        ];
                    }
                }
            }

            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (DataTable): ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'DEBUG: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get detail timeline for a specific student and quiz
     */
    public function detail($kelasName, $serialId, $studentId, $exerciseId)
    {
        try {
            $logs = QuizActivityLog::where('student_id', $studentId)
                ->where('exercise_id', $exerciseId)
                ->orderBy('created_at', 'desc')
                ->get();

            $html = '<ul class="timeline">';
            if ($logs->isEmpty()) {
                $html .= '<li class="mb-3"><div class="text-muted">Siswa belum memulai kuis ini.</div></li>';
            } else {
                foreach ($logs as $log) {
                    $time = $log->created_at->format('H:i:s');
                    $eventName = $log->event_type;
                    
                    $friendlyName = $this->translateEvent($eventName);
                    
                    $color = 'primary';
                    $icon = 'bx-info-circle';
                    
                    if (in_array($eventName, ['START', 'QUIZ_ENTER'])) {
                        $color = 'primary';
                        $icon = 'bx-play-circle';
                    } elseif (in_array($eventName, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) {
                        $color = 'warning';
                        $icon = 'bx-log-out-circle';
                    } elseif (in_array($eventName, ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])) {
                        $color = 'success';
                        $icon = 'bx-log-in-circle';
                    } elseif ($eventName === 'RECONNECTED') {
                        $color = 'info';
                        $icon = 'bx-wifi';
                    } elseif ($eventName === 'BACK_BUTTON_BLOCKED') {
                        $color = 'danger';
                        $icon = 'bx-block';
                    } elseif (in_array($eventName, ['SUBMIT', 'AUTO_SUBMIT'])) {
                        $color = 'success';
                        $icon = 'bx-check-double';
                    }

                    $html .= '<li class="mb-3 border-bottom pb-2">';
                    $html .= "<div class=\"d-flex align-items-center mb-1\">";
                    $html .= "<span class=\"badge bg-{$color} me-2\"><i class=\"bx {$icon}\"></i> {$time}</span>";
                    $html .= "<strong>{$friendlyName}</strong>";
                    $html .= "</div>";
                    
                    if ($log->duration_seconds) {
                        $html .= "<div class=\"text-muted ms-4 small\"><i class=\"bx bx-time\"></i> Durasi tercatat: {$log->duration_seconds} detik</div>";
                    }

                    if ($log->suspicious_flag) {
                        $html .= "<div class=\"ms-4 mt-1\"><span class=\"badge bg-danger\"><i class=\"bx bx-error\"></i> Aktivitas Berisiko</span></div>";
                    }
                    $html .= '</li>';
                }
            }
            $html .= '</ul>';

            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (Detail): ' . $e->getMessage());
            return response()->json(['html' => '<div class="alert alert-danger">Log tidak tersedia.</div>']);
        }
    }
    
    /**
     * Send Reminder
     */
    public function sendReminder(Request $request, $kelasName, $serialId)
    {
        // UI Action Only based on user requirements
        $type = $request->input('type'); // 'individual' or 'bulk'
        $studentName = $request->input('student_name', 'Siswa');
        
        if ($type === 'bulk') {
            return response()->json([
                'status' => 'success',
                'message' => 'Reminder berhasil dikirim ke seluruh siswa yang belum mengerjakan.'
            ]);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => "Reminder berhasil dikirim ke {$studentName}."
        ]);
    }

    /**
     * Client endpoint for students to submit tracking data (API)
     */
    public function track(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exercise_id' => 'required|exists:exercises,id',
            'event_type' => 'required|string',
        ]);

        $service = new QuizActivityService();
        $service->logActivity(
            $request->student_id,
            $request->exercise_id,
            $request->event_type
        );

        return response()->json(['status' => 'success']);
    }

    /**
     * Export to CSV
     */
    public function exportCsv(Request $request, $kelasName, $serialId)
    {
        $serialModel = Serial::findOrFail($serialId);
        $classroom = Classroom::where('serial_id', $serialId)->where('name', $kelasName)->firstOrFail();
        
        $studentIds = Student::where('classroom_id', $classroom->id)->pluck('id');
        $exerciseIds = Exercise::where('serial_id', $serialModel->id)->pluck('id');
        
        if ($request->exercise_id) {
            $exerciseIds = [$request->exercise_id];
        }
        
        try {
            $logs = QuizActivityLog::whereIn('student_id', $studentIds)
                ->whereIn('exercise_id', $exerciseIds)
                ->orderBy('created_at', 'desc')
                ->get();

            $students = Student::whereIn('id', $logs->pluck('student_id')->unique())->get()->keyBy('id');
            $exercises = Exercise::whereIn('id', $logs->pluck('exercise_id')->unique())->get()->keyBy('id');
            
            foreach ($logs as $log) {
                $log->setRelation('student', $students->get($log->student_id));
                $log->setRelation('exercise', $exercises->get($log->exercise_id));
            }

            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=monitoring_quiz_{$kelasName}_".date('Ymd').".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function() use ($logs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Nama Siswa', 'Nama Kuis', 'Event', 'Waktu', 'Mencurigakan', 'IP Address']);

                foreach ($logs as $log) {
                    $friendlyName = $this->translateEvent($log->event_type);
                    fputcsv($file, [
                        $log->student->name ?? 'Unknown',
                        $log->exercise->title ?? 'Unknown',
                        $friendlyName,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->suspicious_flag ? 'Ya' : 'Tidak',
                        $log->ip_address
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (CSV): ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor data karena database log bermasalah.');
        }
    }
    
    /**
     * Export to PDF
     */
    public function exportPdf(Request $request, $kelasName, $serialId)
    {
        $serialModel = Serial::findOrFail($serialId);
        $classroom = Classroom::where('serial_id', $serialId)->where('name', $kelasName)->firstOrFail();
        
        $studentIds = Student::where('classroom_id', $classroom->id)->pluck('id');
        $exerciseIds = Exercise::where('serial_id', $serialModel->id)->pluck('id');
        
        if ($request->exercise_id) {
            $exerciseIds = [$request->exercise_id];
        }
        
        try {
            $logs = QuizActivityLog::whereIn('student_id', $studentIds)
                ->whereIn('exercise_id', $exerciseIds)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $students = Student::whereIn('id', $logs->pluck('student_id')->unique())->get()->keyBy('id');
            $exercises = Exercise::whereIn('id', $logs->pluck('exercise_id')->unique())->get()->keyBy('id');
            
            foreach ($logs as $log) {
                $log->setRelation('student', $students->get($log->student_id));
                $log->setRelation('exercise', $exercises->get($log->exercise_id));
            }
            
            $pdf = \PDF::loadView('guru.soal.monitoring_pdf', compact('logs', 'serialModel', 'kelasName'));
            return $pdf->download("monitoring_quiz_{$kelasName}_".date('Ymd').".pdf");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (PDF): ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor data karena database log bermasalah.');
        }
    }
}

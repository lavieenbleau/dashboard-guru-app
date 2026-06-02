<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizActivityLog;
use App\Models\Exercise;
use App\Models\Student;
use App\Models\Serial;
use App\Services\QuizActivityService;
use Illuminate\Support\Facades\DB;

class QuizMonitoringController extends Controller
{
    /**
     * Display the monitoring dashboard
     */
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
    
    public function index($serial)
    {
        $serialModel = Serial::findOrFail($serial);
        
        // Retrieve filters
        $exercises = Exercise::where('serial_id', $serialModel->id)->get();
        $exerciseIds = $exercises->pluck('id');
        
        try {
            // Summary calculations
            $query = QuizActivityLog::whereIn('exercise_id', $exerciseIds);

            // Current status for each student-exercise combo
            $latestEvents = DB::connection('log_db')->table('quiz_activity_logs as q1')
                ->select('q1.student_id', 'q1.exercise_id', 'q1.event_type', 'q1.suspicious_flag')
                ->join(DB::raw('(SELECT student_id, exercise_id, MAX(created_at) as max_time FROM quiz_activity_logs GROUP BY student_id, exercise_id) as q2'), function($join) {
                    $join->on('q1.student_id', '=', 'q2.student_id')
                         ->on('q1.exercise_id', '=', 'q2.exercise_id')
                         ->on('q1.created_at', '=', 'q2.max_time');
                })
                ->whereIn('q1.exercise_id', $exerciseIds)
                ->get();
            
            $activeCount = $latestEvents->whereNotIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
            $finishedCount = $latestEvents->whereIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])->count();
            
            $totalAppBackground = $query->clone()->whereIn('event_type', ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])->count();
            $totalReconnected = $query->clone()->where('event_type', 'RECONNECTED')->count();
            $totalBlocked = $query->clone()->where('event_type', 'BACK_BUTTON_BLOCKED')->count();
            $totalSuspicious = $query->clone()->where('suspicious_flag', 1)->count();
            
            $dbError = null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (Index): ' . $e->getMessage());
            $activeCount = $finishedCount = $totalAppBackground = $totalReconnected = $totalBlocked = $totalSuspicious = 0;
            $dbError = "Gagal terhubung ke Database Log. Pemantauan sedang tidak aktif.";
        }

        return view('guru.soal.monitoring', compact(
            'serialModel',
            'exercises',
            'activeCount',
            'finishedCount',
            'totalAppBackground',
            'totalReconnected',
            'totalBlocked',
            'totalSuspicious',
            'dbError'
        ));
    }

    /**
     * Datatables JSON response
     */
    public function dataTable(Request $request, $serial)
    {
        $serialModel = Serial::findOrFail($serial);
        $exerciseIds = Exercise::where('serial_id', $serialModel->id)->pluck('id');

        try {
            // Build base query to get latest status per student and exercise
            $subQuery = DB::connection('log_db')->table('quiz_activity_logs')
                ->select('student_id', 'exercise_id', DB::raw('MAX(created_at) as max_time'))
                ->groupBy('student_id', 'exercise_id');

            $query = QuizActivityLog::joinSub($subQuery, 'latest_logs', function ($join) {
                    $join->on('quiz_activity_logs.student_id', '=', 'latest_logs.student_id')
                         ->on('quiz_activity_logs.exercise_id', '=', 'latest_logs.exercise_id')
                         ->on('quiz_activity_logs.created_at', '=', 'latest_logs.max_time');
                })
                ->whereIn('quiz_activity_logs.exercise_id', $exerciseIds);

            // Apply filters
            if ($request->exercise_id) {
                $query->where('quiz_activity_logs.exercise_id', $request->exercise_id);
            }
            
            if ($request->date) {
                $query->whereDate('quiz_activity_logs.created_at', $request->date);
            }

            $logs = $query->get();

            // Manual Eager Loading to bypass Eloquent connection inheritance bug
            $students = Student::whereIn('id', $logs->pluck('student_id')->unique())->get()->keyBy('id');
            $exercises = Exercise::whereIn('id', $logs->pluck('exercise_id')->unique())->get()->keyBy('id');

            $data = [];
            foreach ($logs as $log) {
                $student = $students->get($log->student_id);
                $exercise = $exercises->get($log->exercise_id);
                
                $log->setRelation('student', $student);
                $log->setRelation('exercise', $exercise);

                // Fetch all logs for this student and exercise
                $allLogs = QuizActivityLog::where('student_id', $log->student_id)
                    ->where('exercise_id', $log->exercise_id)
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $bgCount = $allLogs->whereIn('event_type', ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])->count();
                $reconCount = $allLogs->where('event_type', 'RECONNECTED')->count();
                $resumeCount = $allLogs->whereIn('event_type', ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])->count();
                $blockedCount = $allLogs->where('event_type', 'BACK_BUTTON_BLOCKED')->count();
                $hasSuspicious = $allLogs->where('suspicious_flag', 1)->count() > 0;

                // Calculate total away time
                $totalAwaySeconds = $allLogs->whereIn('event_type', ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])->sum('duration_seconds');
                $awayMinutes = floor($totalAwaySeconds / 60);
                $awaySeconds = $totalAwaySeconds % 60;
                $awayStr = $totalAwaySeconds > 0 ? "{$awayMinutes} Menit {$awaySeconds} Detik" : "0 Detik";

                // Mapping last event
                $lastLog = $allLogs->last();
                $rawEvent = $lastLog ? $lastLog->event_type : 'UNKNOWN';
                $lastActivityStr = $lastLog ? $lastLog->created_at->format('H:i:s') : '-';
                
                $friendlyEvent = $this->translateEvent($rawEvent);

                // Cek Submit dan Auto Submit
                $hasSubmit = $allLogs->where('event_type', 'SUBMIT')->count() > 0;
                $hasAutoSubmit = $allLogs->where('event_type', 'AUTO_SUBMIT')->count() > 0;

                // Real status mapping with priority
                if ($hasSubmit) {
                    $mappedStatus = 'Selesai Manual';
                } elseif ($hasAutoSubmit) {
                    $mappedStatus = 'Selesai Otomatis';
                } elseif (in_array($rawEvent, ['START', 'QUIZ_ENTER', 'APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS', 'RECONNECTED'])) {
                    $mappedStatus = 'Sedang Mengerjakan';
                } elseif (in_array($rawEvent, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) {
                    $mappedStatus = 'Di Luar Aplikasi';
                } else {
                    $mappedStatus = 'Belum Mengerjakan';
                }
                
                // Risk classification
                $riskLevel = 'Normal';
                $riskColor = 'success';
                if ($hasSuspicious || $bgCount > 3 || $blockedCount > 2) {
                    $riskLevel = 'Berisiko Tinggi';
                    $riskColor = 'danger';
                } elseif ($bgCount > 0 || $blockedCount > 0 || $reconCount > 0) {
                    $riskLevel = 'Perlu Perhatian';
                    $riskColor = 'warning';
                }

                $data[] = [
                    'student_name' => $student->name ?? 'Unknown',
                    'exercise_name' => $exercise->title ?? 'Unknown',
                    'status' => $mappedStatus,
                    'last_event' => $friendlyEvent,
                    'aktivitas_terakhir' => $lastActivityStr,
                    'jml_background' => $bgCount,
                    'jml_resume' => $resumeCount ?? 0,
                    'total_away' => $awayStr,
                    'jml_reconnected' => $reconCount,
                    'jml_blocked' => $blockedCount ?? 0,
                    'risk_level' => $riskLevel,
                    'risk_color' => $riskColor,
                    'suspicious' => $hasSuspicious ? 'Ya' : 'Tidak',
                    'submit_status' => $hasSubmit ? 'Selesai Manual' : ($hasAutoSubmit ? 'Selesai Otomatis' : 'Belum'),
                    'student_id' => $log->student_id,
                    'exercise_id' => $log->exercise_id,
                ];
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
    public function detail($serial, $studentId, $exerciseId)
    {
        try {
            $logs = QuizActivityLog::where('student_id', $studentId)
                ->where('exercise_id', $exerciseId)
                ->orderBy('created_at', 'desc')
                ->get();

            $html = '<ul class="timeline">';
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
                    $html .= '<div class=\"ms-4 mt-1\"><span class="badge bg-danger"><i class="bx bx-error"></i> Aktivitas Berisiko</span></div>';
                }
                $html .= '</li>';
            }
            $html .= '</ul>';

            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (Detail): ' . $e->getMessage());
            return response()->json(['html' => '<div class="alert alert-danger">Log tidak tersedia. Kuis berjalan tanpa pemantauan aktif.</div>']);
        }
    }

    /**
     * Client endpoint for students to submit tracking data
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
    public function exportCsv(Request $request, $serial)
    {
        $serialModel = Serial::findOrFail($serial);
        $exerciseIds = Exercise::where('serial_id', $serialModel->id)->pluck('id');
        
        try {
            $query = QuizActivityLog::whereIn('exercise_id', $exerciseIds);

            if ($request->exercise_id) {
                $query->where('exercise_id', $request->exercise_id);
            }

            $logs = $query->orderBy('created_at', 'desc')->get();

            // Manual Eager Loading
            $students = Student::whereIn('id', $logs->pluck('student_id')->unique())->get()->keyBy('id');
            $exercises = Exercise::whereIn('id', $logs->pluck('exercise_id')->unique())->get()->keyBy('id');
            foreach ($logs as $log) {
                $log->setRelation('student', $students->get($log->student_id));
                $log->setRelation('exercise', $exercises->get($log->exercise_id));
            }

            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=monitoring_quiz_".date('Ymd').".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function() use ($logs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Nama Siswa', 'Nama Kuis', 'Event', 'Waktu', 'Mencurigakan', 'IP Address']);

                foreach ($logs as $log) {
                    $eventName = $log->event_type;
                    $friendlyName = $this->translateEvent($eventName);

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
    public function exportPdf(Request $request, $serial)
    {
        $serialModel = Serial::findOrFail($serial);
        $exerciseIds = Exercise::where('serial_id', $serialModel->id)->pluck('id');
        
        try {
            $query = QuizActivityLog::whereIn('exercise_id', $exerciseIds);

            if ($request->exercise_id) {
                $query->where('exercise_id', $request->exercise_id);
            }

            $logs = $query->orderBy('created_at', 'desc')->get();
            
            // Manual Eager Loading
            $students = Student::whereIn('id', $logs->pluck('student_id')->unique())->get()->keyBy('id');
            $exercises = Exercise::whereIn('id', $logs->pluck('exercise_id')->unique())->get()->keyBy('id');
            foreach ($logs as $log) {
                $log->setRelation('student', $students->get($log->student_id));
                $log->setRelation('exercise', $exercises->get($log->exercise_id));
            }
            
            $pdf = \PDF::loadView('guru.soal.monitoring_pdf', compact('logs', 'serialModel'));
            return $pdf->download('monitoring_quiz_'.date('Ymd').'.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (PDF): ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor data karena database log bermasalah.');
        }
    }
}

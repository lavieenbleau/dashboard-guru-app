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
    public function index($serial)
    {
        $serialModel = Serial::findOrFail($serial);
        
        // Retrieve filters
        $exercises = Exercise::where('serial_id', $serialModel->id)->get();
        
        try {
            // Summary calculations
            $query = QuizActivityLog::whereHas('exercise', function($q) use ($serialModel) {
                $q->where('serial_id', $serialModel->id);
            });

            // Current status for each student-exercise combo
            $latestEvents = DB::connection('log_db')->table('quiz_activity_logs as q1')
                ->select('q1.student_id', 'q1.exercise_id', 'q1.event_type', 'q1.suspicious_flag')
                ->join(DB::raw('(SELECT student_id, exercise_id, MAX(created_at) as max_time FROM quiz_activity_logs GROUP BY student_id, exercise_id) as q2'), function($join) {
                    $join->on('q1.student_id', '=', 'q2.student_id')
                         ->on('q1.exercise_id', '=', 'q2.exercise_id')
                         ->on('q1.created_at', '=', 'q2.max_time');
                })
                ->get();
            
            $activeCount = $latestEvents->where('event_type', '!=', 'SUBMIT')->count();
            $finishedCount = $latestEvents->where('event_type', 'SUBMIT')->count();
            
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

        try {
            // Build base query to get latest status per student and exercise
            $subQuery = DB::connection('log_db')->table('quiz_activity_logs')
                ->select('student_id', 'exercise_id', DB::raw('MAX(created_at) as max_time'))
                ->groupBy('student_id', 'exercise_id');

            $query = QuizActivityLog::with(['student', 'exercise'])
                ->joinSub($subQuery, 'latest_logs', function ($join) {
                    $join->on('quiz_activity_logs.student_id', '=', 'latest_logs.student_id')
                         ->on('quiz_activity_logs.exercise_id', '=', 'latest_logs.exercise_id')
                         ->on('quiz_activity_logs.created_at', '=', 'latest_logs.max_time');
                })
                ->whereHas('exercise', function($q) use ($serialModel) {
                    $q->where('serial_id', $serialModel->id);
                });

            // Apply filters
            if ($request->exercise_id) {
                $query->where('quiz_activity_logs.exercise_id', $request->exercise_id);
            }
            
            if ($request->date) {
                $query->whereDate('quiz_activity_logs.created_at', $request->date);
            }

            $logs = $query->get();

            $data = [];
            foreach ($logs as $log) {
                $student = $log->student;
                $exercise = $log->exercise;

                // Fetch all logs for this student and exercise
                $allLogs = QuizActivityLog::where('student_id', $student->id)
                    ->where('exercise_id', $exercise->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $bgCount = $allLogs->whereIn('event_type', ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])->count();
                $reconCount = $allLogs->where('event_type', 'RECONNECTED')->count();
                $hasSuspicious = $allLogs->where('suspicious_flag', 1)->count() > 0;
                $hasSubmit = $allLogs->where('event_type', 'SUBMIT')->count() > 0;

                // Calculate total away time
                $totalAwaySeconds = $allLogs->whereIn('event_type', ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])->sum('duration_seconds');
                $awayMinutes = floor($totalAwaySeconds / 60);
                $awaySeconds = $totalAwaySeconds % 60;
                $awayStr = $totalAwaySeconds > 0 ? "{$awayMinutes} Menit {$awaySeconds} Detik" : "0 Detik";

                // Mapping last event
                $lastLog = $allLogs->last();
                $rawEvent = $lastLog ? $lastLog->event_type : 'UNKNOWN';
                $lastActivityStr = $lastLog ? $lastLog->created_at->format('H:i:s') : '-';
                
                $friendlyEvent = $rawEvent;
                if (in_array($rawEvent, ['START', 'QUIZ_ENTER'])) $friendlyEvent = 'Mulai Kuis';
                if (in_array($rawEvent, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) $friendlyEvent = 'Keluar Aplikasi';
                if (in_array($rawEvent, ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])) $friendlyEvent = 'Kembali ke Aplikasi';
                if ($rawEvent === 'RECONNECTED') $friendlyEvent = 'Koneksi Tersambung Kembali';
                if ($rawEvent === 'BACK_BUTTON_BLOCKED') $friendlyEvent = 'Tombol Kembali Diblokir';
                if ($rawEvent === 'SUBMIT') $friendlyEvent = 'Kuis Diselesaikan';

                // Real status mapping
                $mappedStatus = 'Belum Mengerjakan';
                if (in_array($rawEvent, ['START', 'QUIZ_ENTER', 'APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS', 'RECONNECTED'])) {
                    $mappedStatus = 'Sedang Mengerjakan';
                } elseif (in_array($rawEvent, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) {
                    $mappedStatus = 'Di Luar Aplikasi';
                } elseif ($rawEvent === 'SUBMIT') {
                    $mappedStatus = 'Selesai';
                }

                $data[] = [
                    'student_name' => $student->name ?? 'Unknown',
                    'exercise_name' => $exercise->title ?? 'Unknown',
                    'status' => $mappedStatus,
                    'last_event' => $friendlyEvent,
                    'aktivitas_terakhir' => $lastActivityStr,
                    'jml_background' => $bgCount,
                    'total_away' => $awayStr,
                    'jml_reconnected' => $reconCount,
                    'suspicious' => $hasSuspicious ? 'Ya' : 'Tidak',
                    'submit_status' => $hasSubmit ? 'Selesai' : 'Belum',
                    'student_id' => $student->id,
                    'exercise_id' => $exercise->id,
                ];
            }

            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (DataTable): ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'Gagal mengambil data dari Database Log.'
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
                ->orderBy('created_at', 'asc')
                ->get();

            $html = '<ul class="timeline">';
            foreach ($logs as $log) {
                $time = $log->created_at->format('H:i:s');
                $eventName = $log->event_type;
                
                $friendlyName = $eventName;
                if (in_array($eventName, ['START', 'QUIZ_ENTER'])) $friendlyName = 'Mulai Kuis';
                if (in_array($eventName, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) $friendlyName = 'Keluar Aplikasi';
                if (in_array($eventName, ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])) $friendlyName = 'Kembali ke Aplikasi';
                if ($eventName === 'RECONNECTED') $friendlyName = 'Koneksi Tersambung Kembali';
                if ($eventName === 'BACK_BUTTON_BLOCKED') $friendlyName = 'Tombol Kembali Diblokir';
                if ($eventName === 'SUBMIT') $friendlyName = 'Kuis Diselesaikan';
                
                $color = 'primary';
                if (in_array($eventName, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR', 'BACK_BUTTON_BLOCKED'])) $color = 'warning';
                if ($eventName == 'SUBMIT') $color = 'success';
                if ($eventName == 'RECONNECTED') $color = 'info';

                $html .= '<li class="mb-2">';
                $html .= "<span class=\"badge bg-{$color} me-2\">{$time}</span> <strong>{$friendlyName}</strong>";
                
                if ($log->duration_seconds) {
                    $html .= " <small class=\"text-muted ms-1\">(Durasi: {$log->duration_seconds} dtk)</small>";
                }

                if ($log->suspicious_flag) {
                    $html .= ' <span class="badge bg-danger ms-2"><i class="bx bx-error"></i> Mencurigakan</span>';
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
        
        try {
            $query = QuizActivityLog::with(['student', 'exercise'])
                ->whereHas('exercise', function($q) use ($serialModel) {
                    $q->where('serial_id', $serialModel->id);
                });

            if ($request->exercise_id) {
                $query->where('exercise_id', $request->exercise_id);
            }

            $logs = $query->orderBy('created_at', 'desc')->get();

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
                    $friendlyName = $eventName;
                    if (in_array($eventName, ['START', 'QUIZ_ENTER'])) $friendlyName = 'Mulai Kuis';
                    if (in_array($eventName, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) $friendlyName = 'Keluar Aplikasi';
                    if (in_array($eventName, ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])) $friendlyName = 'Kembali ke Aplikasi';
                    if ($eventName === 'RECONNECTED') $friendlyName = 'Koneksi Tersambung Kembali';
                    if ($eventName === 'BACK_BUTTON_BLOCKED') $friendlyName = 'Tombol Kembali Diblokir';
                    if ($eventName === 'SUBMIT') $friendlyName = 'Kuis Diselesaikan';

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
        
        try {
            $query = QuizActivityLog::with(['student', 'exercise'])
                ->whereHas('exercise', function($q) use ($serialModel) {
                    $q->where('serial_id', $serialModel->id);
                });

            if ($request->exercise_id) {
                $query->where('exercise_id', $request->exercise_id);
            }

            $logs = $query->orderBy('created_at', 'desc')->get();
            
            $pdf = \PDF::loadView('guru.soal.monitoring_pdf', compact('logs', 'serialModel'));
            return $pdf->download('monitoring_quiz_'.date('Ymd').'.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Database Log Error (PDF): ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor data karena database log bermasalah.');
        }
    }
}

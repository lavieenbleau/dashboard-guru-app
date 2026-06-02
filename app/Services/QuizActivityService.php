<?php

namespace App\Services;

use App\Models\QuizActivityLog;
use Illuminate\Support\Facades\DB;

class QuizActivityService
{
    /**
     * Log a student's quiz activity event.
     * 
     * @param int $studentId
     * @param int $exerciseId
     * @param string $eventType (QUIZ_ENTER, QUIZ_EXIT, QUIZ_REJOIN, QUIZ_SUBMIT, TAB_SWITCH, WINDOW_BLUR, WINDOW_FOCUS)
     * @param string|null $deviceInfo
     * @param string|null $ipAddress
     * @return QuizActivityLog
     */
    public function logActivity($studentId, $exerciseId, $eventType, $deviceInfo = null, $ipAddress = null)
    {
        // 1. Get the most recent log to calculate duration if needed
        $lastLog = QuizActivityLog::where('student_id', $studentId)
                                  ->where('exercise_id', $exerciseId)
                                  ->orderBy('created_at', 'desc')
                                  ->first();

        $durationSeconds = 0;
        if ($lastLog) {
            $durationSeconds = now()->diffInSeconds($lastLog->created_at);
        }

        // 2. Determine if suspicious flag should be set
        $suspiciousFlag = false;

        // Rule A: Leaving more than 3 times
        if (in_array($eventType, ['QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) {
            $totalExits = QuizActivityLog::where('student_id', $studentId)
                ->where('exercise_id', $exerciseId)
                ->whereIn('event_type', ['QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])
                ->count();
                
            // If they are leaving for the 4th time
            if ($totalExits >= 3) {
                $suspiciousFlag = true;
            }
        }
        
        // Rule B: Total time away > 5 minutes (300 seconds)
        if (in_array($eventType, ['QUIZ_REJOIN', 'WINDOW_FOCUS'])) {
            if ($lastLog && in_array($lastLog->event_type, ['QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) {
                // Calculate total duration from previous exit events
                $previousAwayTime = QuizActivityLog::where('student_id', $studentId)
                    ->where('exercise_id', $exerciseId)
                    ->whereIn('event_type', ['QUIZ_REJOIN', 'WINDOW_FOCUS'])
                    ->sum('duration_seconds');
                    
                // Add current away duration
                $totalAwayTime = $previousAwayTime + $durationSeconds;

                if ($totalAwayTime > 300) { 
                    $suspiciousFlag = true;
                }
            }
        }

        // Rule C: Entering quiz from multiple sessions (already logged in but entering again without exiting)
        if ($eventType === 'QUIZ_ENTER') {
            if ($lastLog && !in_array($lastLog->event_type, ['QUIZ_EXIT', 'QUIZ_SUBMIT'])) {
                $suspiciousFlag = true;
            }
        }

        // 3. Create the log
        return QuizActivityLog::create([
            'student_id' => $studentId,
            'exercise_id' => $exerciseId,
            'event_type' => $eventType,
            'duration_seconds' => $durationSeconds,
            'suspicious_flag' => $suspiciousFlag,
            'device_info' => $deviceInfo ?? request()->header('User-Agent'),
            'ip_address' => $ipAddress ?? request()->ip(),
            'created_at' => now(),
        ]);
    }
}

import re

with open('app/Http/Controllers/Guru/QuizMonitoringController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add translateEvent method inside the class, right before index method
translate_method = """
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
"""
content = re.sub(r'public function index\(\$serial\)', translate_method.strip(), content, count=1)

# In dataTable, replace the mapping block
old_mapping = """                $friendlyEvent = $rawEvent;
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
                }"""

new_mapping = """                $friendlyEvent = $this->translateEvent($rawEvent);

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
                }"""
content = content.replace(old_mapping, new_mapping)

# In dataTable, replace the array items
old_data = """                    'jml_background' => $bgCount,
                    'total_away' => $awayStr,
                    'jml_reconnected' => $reconCount,
                    'suspicious' => $hasSuspicious ? 'Ya' : 'Tidak',
                    'submit_status' => $hasSubmit ? 'Selesai' : 'Belum',"""

new_data = """                    'jml_background' => $bgCount,
                    'jml_resume' => $resumeCount ?? 0,
                    'total_away' => $awayStr,
                    'jml_reconnected' => $reconCount,
                    'jml_blocked' => $blockedCount ?? 0,
                    'risk_level' => $riskLevel,
                    'risk_color' => $riskColor,
                    'suspicious' => $hasSuspicious ? 'Ya' : 'Tidak',
                    'submit_status' => $hasSubmit ? 'Selesai Manual' : ($hasAutoSubmit ? 'Selesai Otomatis' : 'Belum'),"""
content = content.replace(old_data, new_data)

# In dataTable, add the $blockedCount and $resumeCount calculation
old_count = """                $bgCount = $allLogs->whereIn('event_type', ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])->count();
                $reconCount = $allLogs->where('event_type', 'RECONNECTED')->count();
                $hasSuspicious = $allLogs->where('suspicious_flag', 1)->count() > 0;
                $hasSubmit = $allLogs->where('event_type', 'SUBMIT')->count() > 0;"""

new_count = """                $bgCount = $allLogs->whereIn('event_type', ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])->count();
                $reconCount = $allLogs->where('event_type', 'RECONNECTED')->count();
                $resumeCount = $allLogs->whereIn('event_type', ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])->count();
                $blockedCount = $allLogs->where('event_type', 'BACK_BUTTON_BLOCKED')->count();
                $hasSuspicious = $allLogs->where('suspicious_flag', 1)->count() > 0;"""
content = content.replace(old_count, new_count)

# Detail timeline HTML logic
old_detail = """                $friendlyName = $eventName;
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
                $html .= "<span class=\\"badge bg-{$color} me-2\\">{$time}</span> <strong>{$friendlyName}</strong>";"""

new_detail = """                $friendlyName = $this->translateEvent($eventName);
                
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
                $html .= "<div class=\\"d-flex align-items-center mb-1\\">";
                $html .= "<span class=\\"badge bg-{$color} me-2\\"><i class=\\"bx {$icon}\\"></i> {$time}</span>";
                $html .= "<strong>{$friendlyName}</strong>";
                $html .= "</div>";"""
content = content.replace(old_detail, new_detail)

old_detail_end = """                if ($log->duration_seconds) {
                    $html .= " <small class=\\"text-muted ms-1\\">(Durasi: {$log->duration_seconds} dtk)</small>";
                }

                if ($log->suspicious_flag) {
                    $html .= ' <span class="badge bg-danger ms-2"><i class="bx bx-error"></i> Mencurigakan</span>';
                }
                $html .= '</li>';"""
new_detail_end = """                if ($log->duration_seconds) {
                    $html .= "<div class=\\"text-muted ms-4 small\\"><i class=\\"bx bx-time\\"></i> Durasi tercatat: {$log->duration_seconds} detik</div>";
                }

                if ($log->suspicious_flag) {
                    $html .= '<div class=\\"ms-4 mt-1\\"><span class="badge bg-danger"><i class="bx bx-error"></i> Aktivitas Berisiko</span></div>';
                }
                $html .= '</li>';"""
content = content.replace(old_detail_end, new_detail_end)

# Also fix orderBy to desc in detail timeline
old_order = """            $logs = QuizActivityLog::where('student_id', $studentId)
                ->where('exercise_id', $exerciseId)
                ->orderBy('created_at', 'asc')
                ->get();"""
new_order = """            $logs = QuizActivityLog::where('student_id', $studentId)
                ->where('exercise_id', $exerciseId)
                ->orderBy('created_at', 'desc')
                ->get();"""
content = content.replace(old_order, new_order)

# Export CSV replace friendlyName logic
old_csv = """                    $eventName = $log->event_type;
                    $friendlyName = $eventName;
                    if (in_array($eventName, ['START', 'QUIZ_ENTER'])) $friendlyName = 'Mulai Kuis';
                    if (in_array($eventName, ['APP_BACKGROUND', 'QUIZ_EXIT', 'TAB_SWITCH', 'WINDOW_BLUR'])) $friendlyName = 'Keluar Aplikasi';
                    if (in_array($eventName, ['APP_RESUME', 'QUIZ_REJOIN', 'WINDOW_FOCUS'])) $friendlyName = 'Kembali ke Aplikasi';
                    if ($eventName === 'RECONNECTED') $friendlyName = 'Koneksi Tersambung Kembali';
                    if ($eventName === 'BACK_BUTTON_BLOCKED') $friendlyName = 'Tombol Kembali Diblokir';
                    if ($eventName === 'SUBMIT') $friendlyName = 'Kuis Diselesaikan';"""
new_csv = """                    $eventName = $log->event_type;
                    $friendlyName = $this->translateEvent($eventName);"""
content = content.replace(old_csv, new_csv)

with open('app/Http/Controllers/Guru/QuizMonitoringController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Controller updated successfully!")

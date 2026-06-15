<?php
$content = file_get_contents('app/Http/Controllers/Guru/RekapNilaiController.php');

// 1. Rename showStudent to getStudentDetailAjax and adjust it
$showStudentRegex = '/public function showStudent\(.*?\n    \}/s';
preg_match($showStudentRegex, $content, $matches);
if (!empty($matches)) {
    $method = $matches[0];
    $method = str_replace('public function showStudent', 'public function getStudentDetailAjax', $method);
    $method = str_replace('Student::findOrFail($studentId);', 'Student::with(["tasks.post", "exercisePoints.exercise.exerciseType"])->findOrFail($studentId);', $method);
    $method = str_replace("return view('guru.rekap-nilai.show-student', compact('serial', 'classroom', 'student', 'rekapDetail', 'overallScore'));", "return view('guru.rekap-nilai.partials.student-modal-body', compact('serial', 'classroom', 'student', 'rekapDetail', 'overallScore'))->render();", $method);
    
    // Replace the original showStudent with the new method + deprecated old method
    $newCode = $method . "\n\n    /**\n     * @deprecated Digantikan oleh AJAX Modal pada v2.1\n     */\n    " . $matches[0];
    $content = str_replace($matches[0], $newCode, $content);
}

// 2. Simplify showLesson
$showLessonRegex = '/public function showLesson\(.*?return view\(\'guru\.rekap-nilai\.show-class\', \$viewData\);\n    \}/s';
preg_match($showLessonRegex, $content, $matches2);
if (!empty($matches2)) {
    $m = $matches2[0];
    
    // Remove Detail Penilaian logic
    $m = preg_replace('/\s*\/\/ Build unique columns for Detail Penilaian Tab.*?foreach \(\$uniqueExercises as \$ex\) \{.*?\}/s', '', $m);
    $m = preg_replace('/\s*\$studentDetails = \[.*?\];/s', '', $m);
    $m = preg_replace('/\$studentDetails\[\'tasks\'\]\[\$t->post_id\] = \$t->point;/s', '', $m);
    $m = preg_replace('/\$studentDetails\[\'akm\'\]\[\$ex->exercise_id\] = \$ex->exercise_point;/s', '', $m);
    $m = preg_replace('/\$studentDetails\[\'uh\'\]\[\$ex->exercise_id\] = \$ex->exercise_point;/s', '', $m);
    $m = preg_replace('/\$studentDetails\[\'pts\'\]\[\$ex->exercise_id\] = \$ex->exercise_point;/s', '', $m);
    $m = preg_replace('/\$studentDetails\[\'pas\'\]\[\$ex->exercise_id\] = \$ex->exercise_point;/s', '', $m);
    
    $m = preg_replace('/\s*\'detail\' => \$studentDetails/s', '', $m);
    
    $m = preg_replace('/\s*\$detailAverages = \[.*?\];\n        foreach \(\[\'tasks\', \'akm\', \'uh\', \'pts\', \'pas\'\] as \$cat\) \{.*?\}/s', '', $m);
    
    $m = preg_replace('/\'detailColumns\' => \$detailColumns,\s*\'detailAverages\' => collect\(\$detailAverages\)->sortBy\(\'student\.name\'\)->values(),/s', '', $m);
    
    $content = str_replace($matches2[0], $m, $content);
}

file_put_contents('app/Http/Controllers/Guru/RekapNilaiController.php', $content);
echo "Modification complete.\n";

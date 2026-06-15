<?php
$content = file_get_contents('app/Http/Controllers/Guru/RekapNilaiController.php');
$lines = explode("\n", $content);
foreach($lines as $i => $line) {
    if (strpos($line, '$cleanStudentDetails[] =') !== false) {
        $start = max(0, $i - 5);
        for ($j = $start; $j <= $i; $j++) {
            echo "Line " . ($j+1) . ": " . $lines[$j] . "\n";
        }
        break;
    }
}

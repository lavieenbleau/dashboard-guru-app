<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');
$lines = explode("\n", $content);
foreach($lines as $i => $line) {
    if (strpos($line, '<!-- Student Detail Modal -->') !== false) {
        $start = max(0, $i - 10);
        for ($j = $start; $j < $i; $j++) {
            echo "Line " . ($j+1) . ": " . $lines[$j] . "\n";
        }
        break;
    }
}

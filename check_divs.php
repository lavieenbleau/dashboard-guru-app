<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');
$lines = explode("\n", $content);
$open = 0;
$close = 0;
foreach($lines as $i => $line) {
    if (strpos($line, '<!-- Student Detail Modal -->') !== false) {
        break; // Stop before modal
    }
    $open += substr_count($line, '<div');
    $close += substr_count($line, '</div');
}
echo "Open divs: $open\n";
echo "Close divs: $close\n";

<?php
$content = file_get_contents('app/Http/Controllers/Guru/RekapNilaiController.php');
// I just want to see how $rekapData is populated currently.
preg_match('/\$rekapData\[\] = \[(.*?)\];/s', $content, $matches);
echo "REKAP DATA MATCH: \n" . (isset($matches[1]) ? $matches[1] : 'Not found');

<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');
echo substr($content, strpos($content, '@section(\'scripts\')'), 1500);

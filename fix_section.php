<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');
$content = str_replace("@section('page-script')", "@section('scripts')", $content);
file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "Section name fixed.\n";

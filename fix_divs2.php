<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');
// use regex to remove multiple empty closing divs safely before @endif
$content = preg_replace('/<\/div>\s*<\/div>\s*<\/div>\s*@endif/s', "</div>\n                    @endif", $content);
file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "Regex fix applied.\n";

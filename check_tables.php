<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES');
foreach($tables as $t) {
    $v = array_values((array)$t)[0];
    if(str_contains($v, 'class') || str_contains($v, 'post') || str_contains($v, 'share') || str_contains($v, 'task') || str_contains($v, 'exercise')) {
        echo $v . PHP_EOL;
    }
}

<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ExercisePoint;

echo "Tables:\n";
$tables = DB::select('SHOW TABLES');
foreach($tables as $t) {
    $vals = array_values((array)$t);
    if(str_contains($vals[0], 'exercise_point')) {
        echo $vals[0] . "\n";
    }
}

echo "\nColumns in exercise_points:\n";
$cols = DB::select('SHOW COLUMNS FROM exercise_points');
foreach($cols as $c) {
    echo $c->Field . "\n";
}

echo "\nFirst row answer:\n";
$first = DB::table('exercise_points')->first();
if ($first) {
    echo json_encode($first, JSON_PRETTY_PRINT);
} else {
    echo "No rows found";
}

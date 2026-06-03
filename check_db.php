<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['exercise_items', 'exercise_models', 'exercise_points', 'exercise_types', 'exercises', 'quiz_activity_logs', 'share_exercises'];

foreach($tables as $table) {
    try {
        $schema = \Illuminate\Support\Facades\DB::select('SHOW CREATE TABLE ' . $table)[0]->{'Create Table'};
        echo "\n--- Table: $table ---\n$schema\n";
    } catch(\Exception $e) {
        echo "\n--- Table: $table ---\nNot Found\n";
    }
}

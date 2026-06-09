<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Exercise;
use Illuminate\Support\Facades\DB;

$exercises = Exercise::with('exerciseItems')->get();
$count = 0;

foreach ($exercises as $exercise) {
    if ($exercise->exerciseItems->isEmpty()) {
        continue;
    }

    $serial_id = $exercise->serial_id ?? 13;
    $student_id = 2; // Hardcoded existing student

    // Create random answers
    $answers = [];
    $totalScore = 0;
    foreach ($exercise->exerciseItems as $item) {
        $options = ['a', 'b', 'c', 'd'];
        $ans = $options[array_rand($options)];
        $answers[$item->id] = $ans;
        $totalScore += rand(5, 10);
    }

    DB::table('exercise_points')->updateOrInsert(
        [
            'serial_id' => $serial_id,
            'exercise_id' => $exercise->id,
            'student_id' => $student_id
        ],
        [
            'answer' => json_encode($answers),
            'exercise_point' => min($totalScore, 100),
            'updated_at' => now()
        ]
    );
    $count++;
}

echo "Updated/Inserted $count dummy records directly via DB for student 2.\n";

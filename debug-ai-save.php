<?php
// Quick debug script to test AI save functionality

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug AI Questions Save ===\n\n";

// Check if session has data
session_start();

if (isset($_SESSION['ai_generated_questions'])) {
    $data = $_SESSION['ai_generated_questions'];
    echo "✅ Session data found:\n";
    echo "   - Question type: " . ($data['question_type'] ?? 'N/A') . "\n";
    echo "   - Questions count: " . count($data['questions'] ?? []) . "\n";
    echo "   - Mapel ID: " . ($data['mapel_id'] ?? 'N/A') . "\n";
    echo "   - Exercise type ID: " . ($data['exercise_type_id'] ?? 'N/A') . "\n";
} else {
    echo "❌ No session data found\n";
    echo "   This means preview page was not accessed or session expired.\n";
}

echo "\n--- Checking Database Tables ---\n\n";

// Check recent exercises
try {
    $recentExercises = \App\Models\Exercise::orderBy('created_at', 'desc')->take(5)->get(['id', 'title', 'created_at', 'is_admin']);
    
    echo "Recent 5 exercises in database:\n";
    foreach ($recentExercises as $exercise) {
        $type = $exercise->is_admin ? 'Admin' : 'Custom';
        echo "  • ID: {$exercise->id} | {$exercise->title} | {$type} | {$exercise->created_at}\n";
    }
    
    echo "\nTotal custom exercises (is_admin=0): " . \App\Models\Exercise::where('is_admin', 0)->count() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error checking database: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";

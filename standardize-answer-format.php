<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ExerciseItem;
use Illuminate\Support\Facades\DB;

echo "\n=== STANDARDISASI FORMAT JAWABAN PILIHAN GANDA ===\n\n";

// Mulai transaction
DB::beginTransaction();

try {
    // Ambil semua pilihan ganda (exercise_model_id = 1)
    $items = ExerciseItem::where('exercise_model_id', 1)->get();
    
    echo "📊 Total soal pilihan ganda: " . $items->count() . "\n\n";
    
    $updated = 0;
    $errors = 0;
    
    foreach ($items as $item) {
        $answer = $item->answer;
        
        // Skip jika null
        if ($answer === null) {
            continue;
        }
        
        // Cek apakah sudah array
        $decoded = json_decode($answer, true);
        $isArray = $decoded !== null && json_last_error() === JSON_ERROR_NONE;
        
        if ($isArray && is_array($decoded)) {
            // Sudah array, skip
            echo "✓ ID {$item->id}: Sudah array - {$answer}\n";
        } else {
            // Plain text, ubah ke array
            $newAnswer = json_encode([$answer]);
            $item->answer = $newAnswer;
            $item->save();
            $updated++;
            
            echo "→ ID {$item->id}: '{$answer}' → {$newAnswer}\n";
        }
    }
    
    DB::commit();
    
    echo "\n" . str_repeat("=", 100) . "\n";
    echo "✅ SELESAI!\n";
    echo "   - Total diupdate: {$updated} soal\n";
    echo "   - Error: {$errors} soal\n";
    echo "   - Status: BERHASIL\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ExerciseItem;
use App\Models\ExerciseModel;

echo "\n=== CEK FORMAT JAWABAN DI SISTEM ===\n\n";

// Ambil sample exercise items
$items = ExerciseItem::select('id', 'exercise_model_id', 'answer', 'selection', 'question')
    ->with('exerciseModel')
    ->limit(10)
    ->get();

if ($items->isEmpty()) {
    echo "❌ Tidak ada data exercise items\n\n";
    exit;
}

$formats = [
    'pilihan_ganda_array' => 0,
    'pilihan_ganda_object' => 0,
    'pilihan_ganda_text' => 0,
    'essay_text' => 0,
    'essay_array' => 0,
    'essay_object' => 0,
    'singkat_text' => 0,
    'singkat_array' => 0,
];

echo "Sampel " . count($items) . " soal:\n";
echo str_repeat("=", 100) . "\n\n";

foreach ($items as $index => $item) {
    $modelName = $item->exerciseModel ? $item->exerciseModel->name : 'Unknown (ID: ' . $item->exercise_model_id . ')';
    
    echo "[$index] ID: {$item->id} | Tipe: {$modelName}\n";
    echo "   Pertanyaan: " . substr($item->question, 0, 50) . "...\n";
    $selectionStr = is_array($item->selection) ? json_encode($item->selection) : (string)$item->selection;
    echo "   Selection: " . ($item->selection ? substr($selectionStr, 0, 40) : 'null') . "\n";
    
    // Analisis format jawaban
    $answer = $item->answer;
    if ($answer === null) {
        echo "   Answer: NULL\n";
    } else {
        $isJson = json_decode($answer, true) !== null && json_last_error() === JSON_ERROR_NONE;
        $decoded = $isJson ? json_decode($answer, true) : null;
        
        // Tentukan tipe soal
        $typeKey = '';
        if ($item->exercise_model_id == 1) { // Pilihan Ganda
            $typeKey = 'pilihan_ganda_';
        } elseif ($item->exercise_model_id == 2) { // Essay
            $typeKey = 'essay_';
        } else {
            $typeKey = 'singkat_';
        }
        
        // Tentukan format
        if ($isJson) {
            if (is_array($decoded)) {
                if (isset($decoded[0])) {
                    $formatType = $typeKey . 'array';
                    echo "   Answer: [Array dengan " . count($decoded) . " item] - {$answer}\n";
                } elseif (isset($decoded['A']) || isset($decoded['a'])) {
                    $formatType = $typeKey . 'object';
                    echo "   Answer: [Object dengan keys] - {$answer}\n";
                } else {
                    $formatType = $typeKey . 'array';
                    echo "   Answer: [Array lain] - {$answer}\n";
                }
            } else {
                $formatType = $typeKey . 'text';
                echo "   Answer: [JSON Text] - {$answer}\n";
            }
        } else {
            $formatType = $typeKey . 'text';
            echo "   Answer: [Plain Text] - {$answer}\n";
        }
        
        $formats[$formatType]++;
    }
    echo "\n";
}

echo str_repeat("=", 100) . "\n\n";
echo "📊 RINGKASAN FORMAT JAWABAN:\n\n";

echo "PILIHAN GANDA:\n";
echo "  - Array: " . $formats['pilihan_ganda_array'] . " soal\n";
echo "  - Object: " . $formats['pilihan_ganda_object'] . " soal\n";
echo "  - Text: " . $formats['pilihan_ganda_text'] . " soal\n\n";

echo "ESSAY:\n";
echo "  - Array: " . $formats['essay_array'] . " soal\n";
echo "  - Object: " . $formats['essay_object'] . " soal\n";
echo "  - Text: " . $formats['essay_text'] . " soal\n\n";

echo "JAWABAN SINGKAT:\n";
echo "  - Array: " . ($formats['singkat_array'] ?? 0) . " soal\n";
echo "  - Object: " . ($formats['singkat_object'] ?? 0) . " soal\n";
echo "  - Text: " . ($formats['singkat_text'] ?? 0) . " soal\n\n";

// Conclusion
echo "✓ KESIMPULAN:\n";
$pgFormat = '';
if (($formats['pilihan_ganda_array'] ?? 0) > 0) $pgFormat .= 'Array ';
if (($formats['pilihan_ganda_object'] ?? 0) > 0) $pgFormat .= 'Object ';
if (($formats['pilihan_ganda_text'] ?? 0) > 0) $pgFormat .= 'Text ';

$essayFormat = '';
if (($formats['essay_array'] ?? 0) > 0) $essayFormat .= 'Array ';
if (($formats['essay_object'] ?? 0) > 0) $essayFormat .= 'Object ';
if (($formats['essay_text'] ?? 0) > 0) $essayFormat .= 'Text ';

echo "  Pilihan Ganda: " . ($pgFormat ? $pgFormat : 'Tidak ada data') . "\n";
echo "  Essay: " . ($essayFormat ? $essayFormat : 'Tidak ada data') . "\n\n";

$totalFormats = count(array_filter($formats));
if ($totalFormats > 2) {
    echo "⚠️  INCONSISTENCY TERDETEKSI!\n";
    echo "   Ada format jawaban yang berbeda-beda dalam 1 tipe soal.\n\n";
} else {
    echo "✅ FORMAT KONSISTEN\n";
    echo "   Semua jawaban dalam format yang sama.\n\n";
}

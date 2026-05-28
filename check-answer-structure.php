<?php
$pdo = new PDO('mysql:host=localhost;dbname=scimediaonline_laravel_learning_management_system_db_1', 'root', '');

// Get sample exercise items dengan jawaban dan pilihan
$query = <<<SQL
SELECT 
    ei.id,
    ei.exercise_number,
    ei.question,
    ei.selection,
    ei.answer,
    em.name as exercise_model_name
FROM exercise_items ei
LEFT JOIN exercise_models em ON ei.exercise_model_id = em.id
LIMIT 5
SQL;

$result = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

echo "\n=== STRUKTUR JAWABAN SOAL ===\n";
echo str_repeat("=", 80) . "\n\n";

foreach ($result as $item) {
    echo "ID: " . $item['id'] . " | Soal #" . $item['exercise_number'] . "\n";
    echo "Tipe Model: " . ($item['exercise_model_name'] ?? '-') . "\n";
    echo "Pertanyaan: " . substr($item['question'], 0, 60) . "...\n";
    echo "---\n";
    
    // Check selection (opsi jawaban)
    if (!empty($item['selection'])) {
        echo "SELECTION (Pilihan Ganda):\n";
        $selection = json_decode($item['selection'], true);
        if (is_array($selection)) {
            echo "  Format: ARRAY/JSON\n";
            echo "  Isi: " . json_encode($selection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "  Format: TEXT\n";
            echo "  Isi: " . $item['selection'] . "\n";
        }
    } else {
        echo "SELECTION: (kosong/NULL)\n";
    }
    
    echo "---\n";
    
    // Check answer
    if (!empty($item['answer'])) {
        echo "ANSWER (Kunci Jawaban):\n";
        
        // Try to decode as JSON
        $decoded = json_decode($item['answer'], true);
        if ($decoded !== null && is_array($decoded)) {
            echo "  Format: ARRAY/JSON\n";
            echo "  Isi: " . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "  Format: TEXT/STRING\n";
            echo "  Isi: " . $item['answer'] . "\n";
        }
    } else {
        echo "ANSWER: (kosong/NULL)\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

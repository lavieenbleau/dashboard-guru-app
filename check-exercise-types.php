<?php
$pdo = new PDO('mysql:host=localhost;dbname=scimediaonline_laravel_learning_management_system_db_1', 'root', '');

// Get all exercise types
$result = $pdo->query('SELECT id, name, kode FROM exercise_types ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

echo "\n=== SEMUA TIPE SOAL DI DATABASE ===\n\n";
echo "ID | Kode | Nama\n";
echo str_repeat("-", 50) . "\n";

foreach ($result as $type) {
    echo $type['id'] . " | " . $type['kode'] . " | " . $type['name'] . "\n";
}

echo "\n Total: " . count($result) . " tipe soal\n";

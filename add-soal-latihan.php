<?php
$pdo = new PDO('mysql:host=localhost;dbname=scimediaonline_laravel_learning_management_system_db_1', 'root', '');

// Check if 'SL' already exists
$check = $pdo->prepare('SELECT id FROM exercise_types WHERE kode = ?');
$check->execute(['SL']);

if ($check->rowCount() > 0) {
    echo "✓ Tipe soal 'Soal Latihan' (SL) sudah ada di database\n";
} else {
    // Insert new exercise type
    $insert = $pdo->prepare('INSERT INTO exercise_types (name, kode, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
    $insert->execute(['Soal Latihan', 'SL']);
    
    echo "✓ Tipe soal 'Soal Latihan' (SL) berhasil ditambahkan!\n";
}

// Show all exercise types
echo "\n=== DAFTAR TIPE SOAL SEKARANG ===\n\n";
$result = $pdo->query('SELECT id, name, kode FROM exercise_types ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

echo "ID | Kode | Nama\n";
echo str_repeat("-", 50) . "\n";

foreach ($result as $type) {
    echo $type['id'] . " | " . $type['kode'] . " | " . $type['name'] . "\n";
}

echo "\nTotal: " . count($result) . " tipe soal\n";

<?php
$pdo = new PDO('mysql:host=localhost;dbname=scimediaonline_laravel_learning_management_system_db_1', 'root', '');
$result = $pdo->query('DESCRIBE exercises')->fetchAll(PDO::FETCH_ASSOC);

$found = false;
foreach ($result as $col) {
    if ($col['Field'] === 'time_limit') {
        echo "time_limit column EXISTS: " . $col['Type'] . "\n";
        $found = true;
        break;
    }
}

if (!$found) {
    echo "time_limit column NOT FOUND\n";
    // Add it manually
    try {
        $pdo->exec('ALTER TABLE exercises ADD COLUMN time_limit SMALLINT UNSIGNED NULL DEFAULT NULL AFTER title');
        echo "✓ time_limit column ADDED successfully!\n";
    } catch (Exception $e) {
        echo "✗ Error adding column: " . $e->getMessage() . "\n";
    }
}

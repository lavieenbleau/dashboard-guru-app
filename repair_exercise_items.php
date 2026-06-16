<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

function fixDoubleEncoding($data) {
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if (is_string($decoded)) {
            // It was double encoded! "[\"A\"]" -> '["A"]' -> ["A"]
            $deepDecoded = json_decode($decoded, true);
            if (is_array($deepDecoded)) {
                return json_encode($deepDecoded);
            }
        } elseif (is_array($decoded)) {
            // It was single encoded.
            return json_encode($decoded);
        }
    }
    return $data; // Return original if not fixable
}

echo "Memulai perbaikan data double encoding...\n";

$items = DB::table('exercise_items')->whereNotNull('user_id')->get();
$fixedCount = 0;

foreach ($items as $item) {
    $newSelection = fixDoubleEncoding($item->selection);
    $newAnswer = fixDoubleEncoding($item->answer);

    if ($newSelection !== $item->selection || $newAnswer !== $item->answer) {
        DB::table('exercise_items')->where('id', $item->id)->update([
            'selection' => $newSelection,
            'answer' => $newAnswer
        ]);
        echo "Row ID {$item->id} diperbaiki.\n";
        $fixedCount++;
    }
}

echo "Selesai. Total baris diperbaiki: $fixedCount\n";

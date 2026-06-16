<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$latestItem = \App\Models\ExerciseItem::orderBy('id', 'desc')->first();
echo "Latest ID: " . $latestItem->id . "\n";
echo "Question: " . $latestItem->question . "\n";
echo "Selection: " . $latestItem->selection . "\n";
echo "Answer: " . $latestItem->answer . "\n";

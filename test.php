<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = count(App\Models\Post::where('serial_id', 13)->where('category', 'like', '%"lesson_id":1%')->where('is_task', 0)->get());
echo "Count: " . $count . "\n";

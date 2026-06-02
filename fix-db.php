<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$posts = App\Models\Post::where('id', '>=', 20)->get();
foreach($posts as $p) { 
    $raw = $p->getRawOriginal('category');
    echo "ID " . $p->id . ": " . $raw . "\n";
    if (is_string($raw) && (strpos($raw, '"[') === 0 || strpos($raw, '"{') === 0)) {
        $decoded = json_decode($raw, true);
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        $p->category = $decoded; 
        $p->save(); 
        echo "Fixed post " . $p->id . "\n";
    }
}

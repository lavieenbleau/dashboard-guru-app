<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$serials = App\Models\Serial::with('product')->whereHas('classrooms', function($q) { $q->where('name', '4C'); })->get();
foreach ($serials as $s) {
    echo "Paket: {$s->paket} | Product: " . ($s->product ? $s->product->name : 'NULL') . "\n";
}

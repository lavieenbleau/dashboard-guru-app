<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/aplikasi/1/rekap-nilai/kelas/1/lesson/5', 'GET')
);
$content = $response->getContent();
file_put_contents('test_output.html', $content);

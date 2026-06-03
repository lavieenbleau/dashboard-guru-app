<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "lesson_classroom:\n";
print_r(Illuminate\Support\Facades\Schema::getColumnListing("lesson_classroom"));
echo "tasks:\n";
print_r(Illuminate\Support\Facades\Schema::getColumnListing("tasks"));

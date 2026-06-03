<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "share_exercises:\n";
print_r(Illuminate\Support\Facades\Schema::getColumnListing("share_exercises"));
echo "\nposts:\n";
print_r(Illuminate\Support\Facades\Schema::getColumnListing("posts"));

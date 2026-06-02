<?php
$tables = DB::select('SHOW TABLES');
foreach($tables as $table) {
    $tableName = $table->Tables_in_laravel_db;
    $cols = Schema::getColumnListing($tableName);
    if(in_array('classroom_id', $cols) || in_array('classrooms', $cols)) {
        echo $tableName . "\n";
    }
}

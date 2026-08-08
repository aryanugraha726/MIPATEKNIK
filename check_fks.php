<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fks = Illuminate\Support\Facades\DB::select("
    SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE REFERENCED_TABLE_NAME IS NOT NULL 
    AND (COLUMN_NAME = 'job_id' OR REFERENCED_COLUMN_NAME = 'job_id') 
    AND TABLE_SCHEMA = DATABASE()
");

echo json_encode($fks, JSON_PRETTY_PRINT);

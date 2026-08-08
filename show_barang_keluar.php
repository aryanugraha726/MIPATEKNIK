<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['project', 'subproject', 'work_order_release', 'work_order_release_details'];
foreach ($tables as $t) {
    try {
        $result = Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE $t");
        echo $result[0]->{'Create Table'} . "\n\n";
    } catch (\Exception $e) {}
}

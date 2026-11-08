<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$requests = App\Models\MaterialRequest::whereHas('karyawan.divisi', function($q) {
    $q->whereIn('divisi.id_divisi', [1]);
})->get();
echo "OK: " . $requests->count();

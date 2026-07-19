<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$barangs = App\Models\Barang::all();
foreach($barangs as $item) {
    echo $item->id_barang . " - " . $item->id_kategori . "\n";
}

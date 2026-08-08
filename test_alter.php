<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::statement('SET foreign_key_checks=0;');
    Illuminate\Support\Facades\DB::statement('ALTER TABLE `barang_keluar` MODIFY `job_id` VARCHAR(7) DEFAULT NULL');
    Illuminate\Support\Facades\DB::statement('ALTER TABLE `barang_keluar` ADD CONSTRAINT `brg_keluar_project_FK` FOREIGN KEY (`job_id`) REFERENCES `project`(`job_id`)');
    Illuminate\Support\Facades\DB::statement('ALTER TABLE `subproject` ADD CONSTRAINT `subproject_project_FK` FOREIGN KEY (`job_id`) REFERENCES `project`(`job_id`)');
    Illuminate\Support\Facades\DB::statement('ALTER TABLE `work_order_release_details` ADD CONSTRAINT `work_order_release_details_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `work_order_release`(`job_id`) ON DELETE CASCADE');
    Illuminate\Support\Facades\DB::statement('SET foreign_key_checks=1;');
    echo "SUCCESS ALL\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

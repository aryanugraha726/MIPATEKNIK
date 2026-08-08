<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop Foreign Key tugas -> subproject sebelum alter kolom
        DB::statement("ALTER TABLE `tugas` DROP FOREIGN KEY `tugas_subproject_FK`");

        // 2. Ubah tipe kolom subproject_id (PK & FK) ke VARCHAR(9) dan tugas_id ke VARCHAR(11)
        DB::statement("ALTER TABLE `subproject` MODIFY `subproject_id` VARCHAR(9) NOT NULL");
        DB::statement("ALTER TABLE `tugas` MODIFY `subproject_id` VARCHAR(9) NOT NULL");
        DB::statement("ALTER TABLE `tugas` MODIFY `tugas_id` VARCHAR(11) NOT NULL");

        // 3. Re-add Foreign Key tugas -> subproject
        DB::statement("ALTER TABLE `tugas` ADD CONSTRAINT `tugas_subproject_FK` FOREIGN KEY (`subproject_id`) REFERENCES `subproject`(`subproject_id`) ON DELETE CASCADE ON UPDATE RESTRICT");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `tugas` DROP FOREIGN KEY `tugas_subproject_FK`");
        DB::statement("ALTER TABLE `subproject` MODIFY `subproject_id` VARCHAR(7) NOT NULL");
        DB::statement("ALTER TABLE `tugas` MODIFY `subproject_id` VARCHAR(7) NOT NULL");
        DB::statement("ALTER TABLE `tugas` MODIFY `tugas_id` VARCHAR(9) NOT NULL");
        DB::statement("ALTER TABLE `tugas` ADD CONSTRAINT `tugas_subproject_FK` FOREIGN KEY (`subproject_id`) REFERENCES `subproject`(`subproject_id`) ON DELETE CASCADE ON UPDATE RESTRICT");
    }
};

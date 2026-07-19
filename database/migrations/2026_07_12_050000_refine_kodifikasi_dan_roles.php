<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Refine kodifikasi (subproject_id → VARCHAR(7), tugas_id → VARCHAR(9))
     * dan update roles (MANAGEMENT → PPIC, OPERATOR → PURCHASING, + KARYAWAN, DIREKTUR UTAMA)
     */
    public function up(): void
    {
        // ============================================
        // 1. Perbesar kolom role.nama_role agar muat "DIREKTUR UTAMA"
        // ============================================
        DB::statement("ALTER TABLE `role` MODIFY `nama_role` VARCHAR(20) DEFAULT NULL");

        // ============================================
        // 2. Drop Foreign Key tugas → subproject sebelum alter kolom
        // ============================================
        DB::statement("ALTER TABLE `tugas` DROP FOREIGN KEY `tugas_subproject_FK`");

        // ============================================
        // 3. Ubah tipe kolom subproject_id (PK & FK) ke VARCHAR(7)
        // ============================================
        DB::statement("ALTER TABLE `subproject` MODIFY `subproject_id` VARCHAR(7) NOT NULL");
        DB::statement("ALTER TABLE `tugas` MODIFY `subproject_id` VARCHAR(7) NOT NULL");

        // ============================================
        // 4. Ubah tipe kolom tugas_id ke VARCHAR(9)
        // ============================================
        DB::statement("ALTER TABLE `tugas` MODIFY `tugas_id` VARCHAR(9) NOT NULL");

        // ============================================
        // 5. Re-add Foreign Key tugas → subproject
        // ============================================
        DB::statement("ALTER TABLE `tugas` ADD CONSTRAINT `tugas_subproject_FK` FOREIGN KEY (`subproject_id`) REFERENCES `subproject`(`subproject_id`)");

        // ============================================
        // 6. Update data roles
        // ============================================
        DB::table('role')->where('nama_role', 'MANAGEMENT')->update(['nama_role' => 'PPIC']);
        DB::table('role')->where('nama_role', 'OPERATOR')->update(['nama_role' => 'PURCHASING']);

        // Tambah role baru (cek dulu apakah sudah ada)
        $maxRoleId = DB::table('role')->max('role_id');
        
        if (!DB::table('role')->where('nama_role', 'KARYAWAN')->exists()) {
            DB::table('role')->insert([
                'role_id' => $maxRoleId + 1,
                'nama_role' => 'KARYAWAN'
            ]);
        }
        
        if (!DB::table('role')->where('nama_role', 'DIREKTUR UTAMA')->exists()) {
            DB::table('role')->insert([
                'role_id' => $maxRoleId + 2,
                'nama_role' => 'DIREKTUR UTAMA'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FK dulu
        DB::statement("ALTER TABLE `tugas` DROP FOREIGN KEY `tugas_subproject_FK`");

        // Kembalikan tipe kolom
        DB::statement("ALTER TABLE `tugas` MODIFY `tugas_id` INT(11) NOT NULL");
        DB::statement("ALTER TABLE `tugas` MODIFY `subproject_id` INT(11) NOT NULL");
        DB::statement("ALTER TABLE `subproject` MODIFY `subproject_id` INT(11) NOT NULL");

        // Re-add FK
        DB::statement("ALTER TABLE `tugas` ADD CONSTRAINT `tugas_subproject_FK` FOREIGN KEY (`subproject_id`) REFERENCES `subproject`(`subproject_id`)");

        // Kembalikan role
        DB::table('role')->where('nama_role', 'PPIC')->update(['nama_role' => 'MANAGEMENT']);
        DB::table('role')->where('nama_role', 'PURCHASING')->update(['nama_role' => 'OPERATOR']);
        DB::table('role')->where('nama_role', 'KARYAWAN')->delete();
        DB::table('role')->where('nama_role', 'DIREKTUR UTAMA')->delete();

        // Kembalikan ukuran kolom
        DB::statement("ALTER TABLE `role` MODIFY `nama_role` VARCHAR(10) DEFAULT NULL");
    }
};

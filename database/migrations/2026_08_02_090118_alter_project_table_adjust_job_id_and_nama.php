<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop the foreign keys that depend on project.job_id
        // (Sudah di-drop di percobaan migrate sebelumnya, jadi dikomen)
        /*
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign('brg_keluar_project_FK');
        });

        Schema::table('subproject', function (Blueprint $table) {
            $table->dropForeign('subproject_project_FK');
        });
        */

        // 2. Modify project table (collation for job_id, size for nama_project)
        Schema::table('project', function (Blueprint $table) {
            $table->string('job_id', 7)->collation('utf8mb4_unicode_ci')->change();
            $table->string('nama_project', 255)->change();
            
            // Add foreign key from project to work_order_release
            // (Sudah berhasil terbuat di percobaan pertama, jadi dikomen)
            /*
            $table->foreign('job_id')->references('job_id')->on('work_order_release')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            */
        });

        // 3. Modify child tables to match the new collation
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->string('job_id', 7)->collation('utf8mb4_unicode_ci')->nullable()->change();
        });

        Schema::table('subproject', function (Blueprint $table) {
            $table->string('job_id', 7)->collation('utf8mb4_unicode_ci')->change();
        });

        // 4. Restore FKs
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->foreign('job_id', 'brg_keluar_project_FK')->references('job_id')->on('project');
        });

        Schema::table('subproject', function (Blueprint $table) {
            $table->foreign('job_id', 'subproject_project_FK')->references('job_id')->on('project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->string('nama_project', 50)->change();
        });
    }
};

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
        Schema::table('material_request', function (Blueprint $table) {
            $table->string('kategori')->default('Project')->after('no_nota');
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE material_request MODIFY job_id INT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_request', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE material_request MODIFY job_id INT NOT NULL;');
    }
};

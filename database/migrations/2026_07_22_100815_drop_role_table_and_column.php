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
        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign('user_role_FK');
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('role');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->integer('role_id')->primary();
            $table->string('nama_role', 10);
        });

        Schema::table('user', function (Blueprint $table) {
            $table->integer('role_id')->after('id_karyawan');
            $table->foreign('role_id', 'user_role_FK')->references('role_id')->on('role');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_request', function (Blueprint $table) {
            $table->string('id_divisi')->nullable()->change();
        });

        Schema::table('material_request_detail', function (Blueprint $table) {
            $table->string('id_barang')->nullable()->change();
            $table->string('nama_barang_baru')->nullable();
            $table->string('satuan_baru')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('material_request', function (Blueprint $table) {
            $table->string('id_divisi')->nullable(false)->change();
        });

        Schema::table('material_request_detail', function (Blueprint $table) {
            $table->dropColumn(['nama_barang_baru', 'satuan_baru']);
            $table->string('id_barang')->nullable(false)->change();
        });
    }
};

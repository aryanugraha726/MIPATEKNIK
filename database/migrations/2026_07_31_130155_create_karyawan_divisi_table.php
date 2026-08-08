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
        Schema::create('karyawan_divisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_karyawan');
            $table->unsignedInteger('id_divisi');
        });

        $karyawans = \Illuminate\Support\Facades\DB::table('karyawan')->whereNotNull('id_divisi')->get();
        foreach ($karyawans as $karyawan) {
            \Illuminate\Support\Facades\DB::table('karyawan_divisi')->insert([
                'id_karyawan' => $karyawan->id_karyawan,
                'id_divisi' => $karyawan->id_divisi,
            ]);
        }

        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropForeign('karyawan_ibfk_1');
            $table->dropColumn('id_divisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->unsignedInteger('id_divisi')->nullable();
        });

        $karyawanDivisis = \Illuminate\Support\Facades\DB::table('karyawan_divisi')->get();
        foreach ($karyawanDivisis as $kd) {
            \Illuminate\Support\Facades\DB::table('karyawan')
                ->where('id_karyawan', $kd->id_karyawan)
                ->update(['id_divisi' => $kd->id_divisi]);
        }

        Schema::dropIfExists('karyawan_divisi');
    }
};

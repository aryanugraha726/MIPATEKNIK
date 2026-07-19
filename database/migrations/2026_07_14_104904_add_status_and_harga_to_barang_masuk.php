<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->integer('harga_masuk')->nullable()->after('id_barang');
            $table->enum('status', ['ACTIVE', 'PENDING'])->default('ACTIVE')->after('ket_masuk');
        });

        DB::statement("
            CREATE OR REPLACE VIEW view_stock_aktual AS
            SELECT 
                s.id_stock AS id_stock,
                s.id_barang AS id_barang,
                b.nama_barang AS nama_barang,
                IFNULL(s.stock_awal, 0) AS stock_awal,
                b.id_satuan AS id_satuan,
                (SELECT IFNULL(SUM(jml_masuk), 0) FROM barang_masuk WHERE id_barang = s.id_barang AND status = 'ACTIVE') AS stock_masuk,
                (SELECT IFNULL(SUM(jumlah_keluar), 0) FROM barang_keluar WHERE id_barang = s.id_barang) AS stock_keluar,
                (
                    IFNULL(s.stock_awal, 0) + 
                    (SELECT IFNULL(SUM(jml_masuk), 0) FROM barang_masuk WHERE id_barang = s.id_barang AND status = 'ACTIVE') - 
                    (SELECT IFNULL(SUM(jumlah_keluar), 0) FROM barang_keluar WHERE id_barang = s.id_barang)
                ) AS sisa_stock,
                (
                    IFNULL(s.stock_awal, 0) + 
                    (SELECT IFNULL(SUM(jml_masuk), 0) FROM barang_masuk WHERE id_barang = s.id_barang AND status = 'ACTIVE') - 
                    (SELECT IFNULL(SUM(jumlah_keluar), 0) FROM barang_keluar WHERE id_barang = s.id_barang)
                ) * IFNULL(b.harga, 0) AS jumlah_nilai
            FROM stock_opname s
            JOIN barang b ON s.id_barang = b.id_barang;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('harga_masuk');
        });

        // Revert view to previous logic (without status filter)
        DB::statement("
            CREATE OR REPLACE VIEW view_stock_aktual AS
            SELECT 
                s.id_stock AS id_stock,
                s.id_barang AS id_barang,
                b.nama_barang AS nama_barang,
                IFNULL(s.stock_awal, 0) AS stock_awal,
                b.id_satuan AS id_satuan,
                (SELECT IFNULL(SUM(jml_masuk), 0) FROM barang_masuk WHERE id_barang = s.id_barang) AS stock_masuk,
                (SELECT IFNULL(SUM(jumlah_keluar), 0) FROM barang_keluar WHERE id_barang = s.id_barang) AS stock_keluar,
                (
                    IFNULL(s.stock_awal, 0) + 
                    (SELECT IFNULL(SUM(jml_masuk), 0) FROM barang_masuk WHERE id_barang = s.id_barang) - 
                    (SELECT IFNULL(SUM(jumlah_keluar), 0) FROM barang_keluar WHERE id_barang = s.id_barang)
                ) AS sisa_stock,
                (
                    IFNULL(s.stock_awal, 0) + 
                    (SELECT IFNULL(SUM(jml_masuk), 0) FROM barang_masuk WHERE id_barang = s.id_barang) - 
                    (SELECT IFNULL(SUM(jumlah_keluar), 0) FROM barang_keluar WHERE id_barang = s.id_barang)
                ) * IFNULL(b.harga, 0) AS jumlah_nilai
            FROM stock_opname s
            JOIN barang b ON s.id_barang = b.id_barang;
        ");
    }
};

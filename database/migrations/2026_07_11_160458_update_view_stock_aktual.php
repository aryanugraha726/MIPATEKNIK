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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original view definition if rolled back
        DB::statement("
            CREATE OR REPLACE VIEW view_stock_aktual AS 
            SELECT 
                `s`.`id_stock` AS `id_stock`, 
                `s`.`id_barang` AS `id_barang`, 
                `b`.`nama_barang` AS `nama_barang`, 
                IFNULL(`s`.`stock_awal`,0) AS `stock_awal`, 
                `b`.`id_satuan` AS `id_satuan`, 
                IFNULL(`s`.`stock_masuk`,0) AS `stock_masuk`, 
                IFNULL(`s`.`stock_keluar`,0) AS `stock_keluar`, 
                IFNULL(`s`.`stock_awal`,0) + IFNULL(`s`.`stock_masuk`,0) - IFNULL(`s`.`stock_keluar`,0) AS `sisa_stock`, 
                (IFNULL(`s`.`stock_awal`,0) + IFNULL(`s`.`stock_masuk`,0) - IFNULL(`s`.`stock_keluar`,0)) * IFNULL(`b`.`harga`,0) AS `jumlah_nilai` 
            FROM (`stock_opname` `s` JOIN `barang` `b` ON(`s`.`id_barang` = `b`.`id_barang`));
        ");
    }
};

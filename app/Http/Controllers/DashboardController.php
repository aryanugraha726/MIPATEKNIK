<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAktual;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Peringatan Stok Minimum (Sisa Stok <= 3)
        $lowStocks = StockAktual::where('sisa_stock', '<=', 3)->get();

        // 2. Keseluruhan Nilai Aset (Dari View)
        $totalAset = StockAktual::sum('jumlah_nilai');

        // 3. Total Nilai Barang Masuk (Harga x Jumlah Masuk)
        $nilaiMasuk = DB::table('barang_masuk')
            ->join('barang', 'barang_masuk.id_barang', '=', 'barang.id_barang')
            ->sum(DB::raw('barang_masuk.jml_masuk * barang.harga'));

        // 4. Total Nilai Barang Keluar (Harga x Jumlah Keluar)
        $nilaiKeluar = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.id_barang', '=', 'barang.id_barang')
            ->sum(DB::raw('barang_keluar.jumlah_keluar * barang.harga'));

        // 5. Data Mutasi Terakhir (5 Transaksi Masuk & 5 Keluar)
        $mutasiMasuk = BarangMasuk::with('barang')
            ->orderBy('tgl_masuk', 'desc')
            ->limit(5)
            ->get();

        $mutasiKeluar = BarangKeluar::with('barang')
            ->orderBy('tgl_keluar', 'desc')
            ->limit(5)
            ->get();

        // 6. Data Chart (Pergerakan 6 Bulan Terakhir)
        $chartLabels = [];
        $chartMasuk = [];
        $chartKeluar = [];

        $sixMonthsAgo = date('Y-m-01', strtotime("-5 months"));
        
        $masukData = DB::table('barang_masuk')
            ->where('tgl_masuk', '>=', $sixMonthsAgo)
            ->selectRaw('YEAR(tgl_masuk) as year, MONTH(tgl_masuk) as month, SUM(jml_masuk) as total')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $keluarData = DB::table('barang_keluar')
            ->where('tgl_keluar', '>=', $sixMonthsAgo)
            ->selectRaw('YEAR(tgl_keluar) as year, MONTH(tgl_keluar) as month, SUM(jumlah_keluar) as total')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        for ($i = 5; $i >= 0; $i--) {
            $month = date('m', strtotime("-$i months"));
            $year = date('Y', strtotime("-$i months"));
            $key = "$year-$month";
            $label = date('M Y', strtotime("-$i months"));
            
            $chartLabels[] = $label;
            $chartMasuk[] = (float) ($masukData->get($key)->total ?? 0);
            $chartKeluar[] = (float) ($keluarData->get($key)->total ?? 0);
        }

        return view('dashboard.index', compact(
            'lowStocks', 'totalAset', 'nilaiMasuk', 'nilaiKeluar', 'mutasiMasuk', 'mutasiKeluar',
            'chartLabels', 'chartMasuk', 'chartKeluar'
        ));
    }
}
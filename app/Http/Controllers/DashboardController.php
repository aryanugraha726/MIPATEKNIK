<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAktual;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
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

        // 6. Data Chart (Pergerakan Barang) dengan Filter
        $filter = $request->get('filter', '6_bulan');
        $chartLabels = [];
        $chartMasuk = [];
        $chartKeluar = [];
        $chartTitle = "6 Bulan Terakhir";

        if ($filter === 'mingguan') {
            $chartTitle = "7 Hari Terakhir";
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $masukData = DB::table('barang_masuk')->where('tgl_masuk', '>=', $startDate)->selectRaw('DATE(tgl_masuk) as date, SUM(jml_masuk) as total')->groupBy('date')->pluck('total', 'date');
            $keluarData = DB::table('barang_keluar')->where('tgl_keluar', '>=', $startDate)->selectRaw('DATE(tgl_keluar) as date, SUM(jumlah_keluar) as total')->groupBy('date')->pluck('total', 'date');

            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $chartLabels[] = date('d M', strtotime($date));
                $chartMasuk[] = (float) ($masukData[$date] ?? 0);
                $chartKeluar[] = (float) ($keluarData[$date] ?? 0);
            }
        } elseif ($filter === 'bulanan') {
            $chartTitle = "4 Minggu Terakhir";
            for ($i = 3; $i >= 0; $i--) {
                $start = date('Y-m-d', strtotime("-".($i*7 + 6)." days"));
                $end = date('Y-m-d', strtotime("-".($i*7)." days"));
                $chartLabels[] = date('d M', strtotime($start)) . ' - ' . date('d M', strtotime($end));
                $chartMasuk[] = (float) DB::table('barang_masuk')->whereBetween('tgl_masuk', [$start, $end])->sum('jml_masuk');
                $chartKeluar[] = (float) DB::table('barang_keluar')->whereBetween('tgl_keluar', [$start, $end])->sum('jumlah_keluar');
            }
        } else {
            if ($filter === 'quarter') {
                $months = 3;
                $chartTitle = "3 Bulan Terakhir";
            } elseif ($filter === 'tahunan') {
                $months = 12;
                $chartTitle = "1 Tahun Terakhir";
            } else {
                $months = 6;
                $chartTitle = "6 Bulan Terakhir";
                $filter = '6_bulan';
            }

            $startDate = date('Y-m-01', strtotime("-".($months-1)." months"));
            
            $masukData = DB::table('barang_masuk')
                ->where('tgl_masuk', '>=', $startDate)
                ->selectRaw('YEAR(tgl_masuk) as year, MONTH(tgl_masuk) as month, SUM(jml_masuk) as total')
                ->groupBy('year', 'month')
                ->get()
                ->keyBy(function($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });

            $keluarData = DB::table('barang_keluar')
                ->where('tgl_keluar', '>=', $startDate)
                ->selectRaw('YEAR(tgl_keluar) as year, MONTH(tgl_keluar) as month, SUM(jumlah_keluar) as total')
                ->groupBy('year', 'month')
                ->get()
                ->keyBy(function($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });

            for ($i = $months - 1; $i >= 0; $i--) {
                $month = date('m', strtotime("-$i months"));
                $year = date('Y', strtotime("-$i months"));
                $key = "$year-$month";
                $label = date('M Y', strtotime("-$i months"));
                
                $chartLabels[] = $label;
                $chartMasuk[] = (float) ($masukData->get($key)->total ?? 0);
                $chartKeluar[] = (float) ($keluarData->get($key)->total ?? 0);
            }
        }

        // 7. Stok per Kategori
        $stokPerKategori = DB::table('view_stock_aktual')
            ->join('barang', 'view_stock_aktual.id_barang', '=', 'barang.id_barang')
            ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
            ->select('kategori_barang.nama_kategori', DB::raw('SUM(view_stock_aktual.sisa_stock) as total_stok'))
            ->groupBy('kategori_barang.id_kategori', 'kategori_barang.nama_kategori')
            ->orderBy('total_stok', 'desc')
            ->get();
            
        $totalStokKeseluruhan = $stokPerKategori->sum('total_stok');
        
        $kategoriLabels = $stokPerKategori->pluck('nama_kategori')->toArray();
        $kategoriData = $stokPerKategori->pluck('total_stok')->toArray();

        // 8. Statistik Tambahan
        $totalJenisBarang = DB::table('barang')->count();
        $totalTransaksiMasuk = DB::table('barang_masuk')->count();
        $totalTransaksiKeluar = DB::table('barang_keluar')->count();

        return view('dashboard.index', compact(
            'lowStocks', 'totalAset', 'nilaiMasuk', 'nilaiKeluar', 'mutasiMasuk', 'mutasiKeluar',
            'chartLabels', 'chartMasuk', 'chartKeluar', 'filter', 'chartTitle',
            'stokPerKategori', 'totalStokKeseluruhan', 'kategoriLabels', 'kategoriData',
            'totalJenisBarang', 'totalTransaksiMasuk', 'totalTransaksiKeluar'
        ));
    }
}
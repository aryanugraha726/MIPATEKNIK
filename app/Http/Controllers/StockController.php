<?php

namespace App\Http\Controllers;

use App\Models\StockAktual; 
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        // Sekarang Laravel tahu bahwa StockAktual diambil dari App\Models
        $stocks = StockAktual::with(['satuan', 'barang'])->get();

        return view('stock.index', compact('stocks'));
    }

    public function export()
    {
        $stocks = StockAktual::with(['satuan', 'barang'])->get();

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $fileName = 'Laporan_Stock_Opname_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->openToFile('php://output');

        // Header Row
        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'ID Barang', 'Nama Barang', 'Harga Satuan', 'Stok Awal', 'Satuan', 'Masuk', 'Keluar', 'Sisa Stok', 'Total Nilai (Rp)'
        ]));

        $totalNilai = 0;
        foreach ($stocks as $stock) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $stock->id_barang,
                $stock->nama_barang,
                $stock->barang->harga ?? 0,
                $stock->stock_awal,
                $stock->satuan->nama_satuan ?? '-',
                $stock->stock_masuk,
                $stock->stock_keluar,
                $stock->sisa_stock,
                $stock->jumlah_nilai
            ]));
            $totalNilai += $stock->jumlah_nilai;
        }

        // Footer Row
        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            '', '', '', '', '', '', '', 'Total Keseluruhan:', $totalNilai
        ]));

        $writer->close();
        exit;
    }
}
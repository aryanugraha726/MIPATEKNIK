<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Project;
use App\Models\KategoriBarang;
use App\Models\Satuan;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockAktual;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Menampilkan Form
    public function create()
    {
        $barangs = Barang::select('id_barang', 'nama_barang', 'harga')->get();
        $projects = Project::select('job_id', 'nama_project')->get();
        $kategoris = KategoriBarang::all();
        $satuans = Satuan::all();
        return view('transaksi.create', compact('barangs', 'projects', 'kategoris', 'satuans'));
    }

    // Memproses Data dari Form
    public function store(Request $request)
    {
        $request->validate([
            'jenis_transaksi' => 'required|in:masuk,keluar',
            'id_barang' => 'required|exists:barang,id_barang',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:30',
        ]);

        $barang = Barang::findOrFail($request->id_barang);
        $stockAktual = StockAktual::where('id_barang', $request->id_barang)->first();
        $currentActiveStock = $stockAktual ? $stockAktual->sisa_stock : 0;

        if ($request->jenis_transaksi == 'masuk') {
            $request->validate(['harga_masuk' => 'required|numeric|min:0']);
            
            $status = 'ACTIVE';
            
            // Logic: if active stock > 0 and price differs, goes to queue
            if ($currentActiveStock > 0 && $request->harga_masuk != $barang->harga) {
                $status = 'PENDING';
            } else if ($currentActiveStock <= 0 && $request->harga_masuk != $barang->harga) {
                // If active stock is 0, it becomes active immediately and updates master price
                $barang->update(['harga' => $request->harga_masuk]);
            }

            $newId = BarangMasuk::max('id_masuk') + 1; 

            BarangMasuk::create([
                'id_masuk'  => $newId ?: 1,
                'id_barang' => $request->id_barang,
                'tgl_masuk' => $request->tanggal,
                'jml_masuk' => $request->jumlah,
                'ket_masuk' => $request->keterangan,
                'harga_masuk' => $request->harga_masuk,
                'status' => $status
            ]);

            if ($status == 'PENDING') {
                return redirect()->route('stock.index')->with('success', 'Barang berhasil disimpan di Stok Antrean (PENDING) karena harga berbeda dan stok lama belum habis.');
            } else {
                return redirect()->route('stock.index')->with('success', 'Transaksi barang masuk berhasil disimpan! Stok telah terupdate.');
            }

        } else {
            $request->validate(['job_id' => 'required|exists:project,job_id']);
            
            // Opsi 1: Validasi Hard Limit
            // Mengecek apakah stok mencukupi
            $sisaStock = $stockAktual ? $stockAktual->sisa_stock : 0;
            if ($request->jumlah > $sisaStock) {
                return redirect()->back()->withInput()->withErrors(['jumlah' => "Jumlah barang keluar melebihi stok yang tersedia (Sisa Stok: $sisaStock)."]);
            }
            
            $newId = BarangKeluar::max('id_keluar') + 1;

            BarangKeluar::create([
                'id_keluar'     => $newId ?: 1,
                'id_barang'     => $request->id_barang,
                'tgl_keluar'    => $request->tanggal,
                'job_id'    => $request->job_id,
                'jumlah_keluar' => $request->jumlah,
                'ket_keluar'    => $request->keterangan,
            ]);

            // Auto-Activate Logic
            // Fetch updated active stock
            $updatedStockAktual = StockAktual::where('id_barang', $request->id_barang)->first();
            $newActiveStock = $updatedStockAktual ? $updatedStockAktual->sisa_stock : 0;

            // Loop while active stock <= 0 to activate queues
            while ($newActiveStock <= 0) {
                $oldestPending = BarangMasuk::where('id_barang', $request->id_barang)
                                            ->where('status', 'PENDING')
                                            ->orderBy('tgl_masuk', 'asc')
                                            ->orderBy('id_masuk', 'asc')
                                            ->first();
                
                if ($oldestPending) {
                    // Activate this batch
                    $oldestPending->update(['status' => 'ACTIVE']);
                    
                    // Update master price
                    $barang->update(['harga' => $oldestPending->harga_masuk]);
                    
                    // Recheck stock
                    $updatedStockAktual = StockAktual::where('id_barang', $request->id_barang)->first();
                    $newActiveStock = $updatedStockAktual ? $updatedStockAktual->sisa_stock : 0;
                } else {
                    // No more pending items
                    break;
                }
            }

            return redirect()->route('stock.index')->with('success', 'Transaksi barang keluar berhasil disimpan! Stok telah terupdate.');
        }
    }
    // Menampilkan Riwayat Barang Masuk
    public function historyMasuk()
    {
        $masuk = BarangMasuk::with('barang')
            ->orderBy('tgl_masuk', 'desc')
            ->get();

        return view('transaksi.masuk', compact('masuk'));
    }

    // Menampilkan Riwayat Barang Keluar
    public function historyKeluar(Request $request)
    {
        $query = BarangKeluar::with(['barang', 'project']);

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        if ($request->filled('nama_barang')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->nama_barang . '%');
            });
        }

        $keluar = $query->orderBy('tgl_keluar', 'desc')->get();
        $projects = Project::select('job_id', 'nama_project')->get();

        return view('transaksi.keluar', compact('keluar', 'projects'));
    }

    // Menampilkan Antrean Barang Masuk (Pending)
    public function pendingList()
    {
        $pending = BarangMasuk::with('barang')
            ->where('status', 'PENDING')
            ->orderBy('tgl_masuk', 'asc')
            ->get();

        return view('transaksi.pending', compact('pending'));
    }
}
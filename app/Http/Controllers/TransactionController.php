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
use App\Models\PO;
use App\Models\PODetail;
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
        $pos = PO::where('status', 'APPROVED')->orderBy('tgl_po', 'desc')->get();
        return view('transaksi.create', compact('barangs', 'projects', 'kategoris', 'satuans', 'pos'));
    }

    public function getPoDetails($no_po)
    {
        $po = PO::with(['details.barang.satuan'])->where('no_po', $no_po)->first();
        if (!$po) {
            return response()->json(['error' => 'PO tidak ditemukan'], 404);
        }

        // Ambil ID Barang yang sudah diterima dari ket_masuk
        $receivedItemIds = BarangMasuk::where('ket_masuk', $no_po)->pluck('id_barang')->toArray();

        $items = $po->details->map(function($detail) use ($receivedItemIds) {
            return [
                'id_barang' => $detail->id_barang,
                'nama_barang' => $detail->barang ? $detail->barang->nama_barang : '-',
                'qty_po' => $detail->qty_po,
                'satuan' => $detail->barang && $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : ($detail->id_satuan ?? '-'),
                'is_received' => in_array($detail->id_barang, $receivedItemIds)
            ];
        });

        return response()->json($items);
    }

    // Memproses Data dari Form
    public function store(Request $request)
    {
        if ($request->jenis_transaksi == 'masuk') {
            $request->validate([
                'no_po' => 'required|exists:po,no_po',
                'items' => 'required|array|min:1',
            ]);

            $noPo = $request->no_po;
            $po = PO::where('no_po', $noPo)->firstOrFail();
            
            DB::beginTransaction();
            try {
                $receivedItemIds = BarangMasuk::where('ket_masuk', $noPo)->pluck('id_barang')->toArray();
                
                foreach ($request->items as $idBarang) {
                    // Skip if already received
                    if (in_array($idBarang, $receivedItemIds)) continue;

                    $detail = PODetail::where('no_po', $noPo)->where('id_barang', $idBarang)->first();
                    $barang = Barang::findOrFail($idBarang);
                    
                    $hargaBeli = ($detail && $detail->harga) ? $detail->harga : $barang->harga;
                    $isPriceDifferent = ($hargaBeli != $barang->harga);
                    $status = $isPriceDifferent ? 'PENDING' : 'ACTIVE';
                    
                    $newId = BarangMasuk::max('id_masuk') + 1; 

                    BarangMasuk::create([
                        'id_masuk'  => $newId ?: 1,
                        'id_barang' => $idBarang,
                        'tgl_masuk' => now()->format('Y-m-d'),
                        'jml_masuk' => $detail ? $detail->qty_po : 0,
                        'ket_masuk' => $noPo, // Store PO Number here
                        'harga_masuk' => $isPriceDifferent ? $hargaBeli : $barang->harga,
                        'status' => $status
                    ]);
                }

                // Check if all items in PO are received now
                $totalItemsInPo = PODetail::where('no_po', $noPo)->count();
                $totalReceived = BarangMasuk::where('ket_masuk', $noPo)->distinct('id_barang')->count('id_barang');

                if ($totalReceived >= $totalItemsInPo) {
                    $po->update(['status' => 'SELESAI']);
                }

                DB::commit();
                return redirect()->route('stock.index')->with('success', 'Transaksi penerimaan barang dari PO berhasil.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error('PO Penerimaan Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal memproses penerimaan: ' . $e->getMessage())->withInput();
            }

        } else {
            $request->validate([
                'jenis_transaksi' => 'required|in:masuk,keluar',
                'id_barang' => 'required|exists:barang,id_barang',
                'tanggal' => 'required|date',
                'jumlah' => 'required|integer|min:1',
                'keterangan' => 'nullable|string|max:30',
                'job_id' => 'required|exists:project,job_id'
            ]);

            $barang = Barang::findOrFail($request->id_barang);
            $stockAktual = StockAktual::where('id_barang', $request->id_barang)->first();
            
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

    // Mengambil detail riwayat keluar untuk satu barang
    public function detailBarangKeluar($id)
    {
        $barang = Barang::findOrFail($id);
        
        $keluar = BarangKeluar::with('project')
            ->where('id_barang', $id)
            ->orderBy('tgl_keluar', 'desc')
            ->get();

        $summary = [];
        $grouped = $keluar->groupBy('job_id');
        
        foreach ($grouped as $job_id => $items) {
            $summary[] = [
                'job_id' => $job_id,
                'nama_project' => $items->first()->project->nama_project ?? 'N/A',
                'total_keluar' => $items->sum('jumlah_keluar'),
                'rincian' => $items->map(function($i) {
                    return [
                        'tanggal' => date('d M Y', strtotime($i->tgl_keluar)),
                        'jumlah' => $i->jumlah_keluar,
                        'keterangan' => $i->ket_keluar ?: '-'
                    ];
                })->values()
            ];
        }

        return response()->json([
            'barang' => $barang,
            'summary' => $summary
        ]);
    }

    // Mengambil detail riwayat keluar untuk satu project
    public function detailProjectKeluar($id)
    {
        $project = Project::findOrFail($id);
        
        $keluar = BarangKeluar::with('barang')
            ->where('job_id', $id)
            ->orderBy('tgl_keluar', 'desc')
            ->get();

        $summary = [];
        $grouped = $keluar->groupBy('id_barang');
        
        foreach ($grouped as $id_barang => $items) {
            $summary[] = [
                'id_barang' => $id_barang,
                'nama_barang' => $items->first()->barang->nama_barang ?? 'N/A',
                'total_keluar' => $items->sum('jumlah_keluar'),
                'rincian' => $items->map(function($i) {
                    return [
                        'tanggal' => date('d M Y', strtotime($i->tgl_keluar)),
                        'jumlah' => $i->jumlah_keluar,
                        'keterangan' => $i->ket_keluar ?: '-'
                    ];
                })->values()
            ];
        }

        return response()->json([
            'project' => $project,
            'summary' => $summary
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\BarangKeluar;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class PurchaseNeedsController extends Controller
{
    private function checkAccess()
    {
        $user = Auth::user();
        return array_intersect(['ADMIN', 'PURCHASING', 'DIREKTUR UTAMA'], $user->roles());
    }

    public function index()
    {
        if (!$this->checkAccess()) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        // Get all pending stock MRs
        $pendingMrs = MaterialRequest::with('details.barang.satuan')->where('status', 'PENDING_STOCK')->get();
        
        $needs = [];
        
        foreach ($pendingMrs as $mr) {
            foreach ($mr->details as $detail) {
                if (!$detail->id_barang) continue;
                
                $totalKeluar = BarangKeluar::where('ket_keluar', 'MR: ' . $mr->no_nota)
                                        ->where('id_barang', $detail->id_barang)
                                        ->sum('jumlah_keluar');
                                        
                $remainingQty = max(0, $detail->req_qty - $totalKeluar);
                
                if ($remainingQty > 0) {
                    $idBarang = $detail->id_barang;
                    if (!isset($needs[$idBarang])) {
                        $needs[$idBarang] = [
                            'id_barang' => $idBarang,
                            'nama_barang' => $detail->barang ? $detail->barang->nama_barang : '-',
                            'satuan' => $detail->barang && $detail->barang->satuan ? $detail->barang->satuan->nama_satuan : '-',
                            'total_kebutuhan' => 0,
                            'sumber_mr' => []
                        ];
                    }
                    $needs[$idBarang]['total_kebutuhan'] += $remainingQty;
                    $needs[$idBarang]['sumber_mr'][] = [
                        'no_nota' => $mr->no_nota,
                        'qty' => $remainingQty
                    ];
                }
            }
        }
        
        // Convert to object for easier blade iteration
        $needs = collect($needs)->values();

        return view('po.needs', compact('needs'));
    }
}

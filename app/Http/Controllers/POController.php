<?php

namespace App\Http\Controllers;

use App\Models\PO;
use App\Models\PODetail;
use App\Models\Vendor;
use App\Models\ShippingAddress;
use App\Models\Project;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class POController extends Controller
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

        $pos = PO::with(['vendor', 'shippingAddress'])->orderBy('tgl_po', 'desc')->get();
        return view('po.index', compact('pos'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Hanya Purchasing yang dapat membuat PO.');
        }

        $vendors = Vendor::all();
        $shippingAddresses = ShippingAddress::all();
        $projects = Project::all();
        $barangs = Barang::with('satuan')->get();
        $kategoris = \App\Models\KategoriBarang::all();
        $satuans = \App\Models\Satuan::all();

        return view('po.create', compact('vendors', 'shippingAddresses', 'projects', 'barangs', 'kategoris', 'satuans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'no_po' => 'required|string|unique:po,no_po',
            'id_vendor' => 'required',
            'id_lokasi' => 'required',
            'job_id' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.qty_po' => 'required|numeric|min:1',
        ], [
            'no_po.unique' => 'Nomor PO ini sudah digunakan, silakan masukkan nomor PO yang lain.',
            'items.required' => 'Minimal harus ada 1 barang yang ditambahkan.',
        ]);

        $noPo = $request->no_po;

        DB::beginTransaction();
        try {
            $po = PO::create([
                'no_po' => $noPo,
                'tgl_po' => Carbon::now()->format('Y-m-d'),
                'id_vendor' => $request->id_vendor,
                'id_lokasi' => $request->id_lokasi,
                'job_id' => $request->job_id,
                'currency' => 'IDR',
                'ppn' => $request->has('is_ppn') ? 11 : 0,
                'pph' => $request->has('is_pph') ? 2 : 0,
                'grand_total' => 0,
                'status' => 'DRAFT'
            ]);

            foreach ($request->items as $item) {
                $barang = Barang::find($item['id_barang']);
                if ($barang) {
                    $hargaBeli = isset($item['harga']) ? $item['harga'] : $barang->harga;
                    
                    PODetail::create([
                        'no_po' => $noPo,
                        'id_barang' => $item['id_barang'],
                        'qty_po' => $item['qty_po'],
                        'id_satuan' => $barang->id_satuan,
                        'harga' => $hargaBeli
                    ]);
                }
            }
            
            DB::commit();
            
            $this->recalculateGrandTotal($noPo);

            return redirect()->route('po.show', $noPo)->with('success', 'Draft PO berhasil dibuat beserta itemnya.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan PO: ' . $e->getMessage());
        }
    }

    public function show($no_po)
    {
        if (!$this->checkAccess()) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $po = PO::with(['vendor', 'shippingAddress', 'details', 'workOrderRelease.project'])->findOrFail($no_po);
        
        // Fetch all barang for the dropdown
        $barangs = Barang::all();

        return view('po.show', compact('po', 'barangs'));
    }

    public function addDetail(Request $request, $no_po)
    {
        $po = PO::findOrFail($no_po);
        
        if ($po->status !== 'DRAFT') {
            return redirect()->back()->with('error', 'Tidak dapat menambah barang pada PO yang sudah diajukan/disetujui.');
        }

        $request->validate([
            'id_barang' => 'required',
            'qty_po' => 'required|numeric|min:1',
            'harga' => 'nullable|numeric|min:0'
        ]);

        $barang = Barang::findOrFail($request->id_barang);
        $hargaBeli = $request->has('harga') ? $request->harga : $barang->harga;

        // Check if item already exists in PO
        $existing = PODetail::where('no_po', $no_po)->where('id_barang', $request->id_barang)->first();
        if ($existing) {
            $existing->qty_po += $request->qty_po;
            $existing->harga = $hargaBeli;
            $existing->save();
        } else {
            PODetail::create([
                'no_po' => $no_po,
                'id_barang' => $request->id_barang,
                'qty_po' => $request->qty_po,
                'id_satuan' => $barang->id_satuan,
                'harga' => $hargaBeli
            ]);
        }

        $this->recalculateGrandTotal($no_po);

        return redirect()->route('po.show', $no_po)->with('success', 'Barang berhasil ditambahkan.');
    }

    public function removeDetail($id_po_detail)
    {
        $detail = PODetail::findOrFail($id_po_detail);
        $no_po = $detail->no_po;
        
        $po = PO::findOrFail($no_po);
        if ($po->status !== 'DRAFT') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus barang pada PO yang sudah diajukan.');
        }

        $detail->delete();
        $this->recalculateGrandTotal($no_po);

        return redirect()->route('po.show', $no_po)->with('success', 'Barang dihapus dari PO.');
    }

    private function recalculateGrandTotal($no_po)
    {
        $po = PO::findOrFail($no_po);
        $details = PODetail::where('no_po', $no_po)->get();
        
        $total_barang = 0;
        foreach($details as $d) {
            $barang = Barang::find($d->id_barang);
            if ($barang) {
                $hargaDetail = $d->harga ?? $barang->harga;
                $total_barang += ($hargaDetail * $d->qty_po);
            }
        }

        $nilai_ppn = ($po->ppn / 100) * $total_barang;
        $nilai_pph = ($po->pph / 100) * $total_barang;
        
        $po->grand_total = $total_barang + $nilai_ppn - $nilai_pph;
        $po->save();
    }

    public function submit($no_po)
    {
        $po = PO::findOrFail($no_po);
        $po->status = 'PENDING_APPROVAL';
        $po->save();

        return redirect()->route('po.show', $no_po)->with('success', 'PO berhasil diajukan untuk persetujuan Direktur Utama.');
    }

    public function approve($no_po)
    {
        $user = Auth::user();
        if (!in_array('DIREKTUR UTAMA', $user->roles())) {
            return redirect()->back()->with('error', 'Hanya Direktur Utama yang dapat menyetujui PO.');
        }

        $po = PO::findOrFail($no_po);
        $po->status = 'APPROVED';
        $po->approved_by = $user->id_karyawan;
        $po->approved_at = Carbon::now();
        $po->save();

        return redirect()->route('po.show', $no_po)->with('success', 'PO berhasil disetujui.');
    }

    public function reject(Request $request, $no_po)
    {
        $user = Auth::user();
        if (!in_array('DIREKTUR UTAMA', $user->roles())) {
            return redirect()->back()->with('error', 'Hanya Direktur Utama yang dapat menolak PO.');
        }

        $po = PO::findOrFail($no_po);
        $po->status = 'REJECTED';
        $po->rejection_reason = $request->input('reason', 'Ditolak oleh Direktur Utama');
        $po->save();

        return redirect()->route('po.show', $no_po)->with('success', 'PO telah ditolak.');
    }

    public function exportPdf(Request $request)
    {
        if (!$this->checkAccess()) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $no_po = $request->query('no_po');
        if (!$no_po) {
            abort(400, 'No PO diperlukan.');
        }

        $po = PO::with(['vendor', 'shippingAddress', 'details.barang.satuan', 'details.satuan', 'workOrderRelease.project'])
                 ->findOrFail($no_po);

        $pdf = Pdf::loadView('po.pdf', compact('po'))
                  ->setPaper('a4', 'portrait')
                  ->setOption('isHtml5ParserEnabled', true)
                  ->setOption('isPhpEnabled', false)
                  ->setOption('defaultFont', 'arial')
                  ->setOption('dpi', 96);

        $filename = 'PO_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $no_po) . '.pdf';
        return $pdf->download($filename);
    }

    public function destroy($no_po)
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $po = PO::findOrFail($no_po);
        if ($po->status === 'APPROVED') {
            return redirect()->back()->with('error', 'PO yang sudah disetujui tidak dapat dihapus.');
        }

        PODetail::where('no_po', $no_po)->delete();
        $po->delete();

        return redirect()->route('po.index')->with('success', 'PO berhasil dihapus.');
    }
}

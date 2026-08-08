<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrderRelease;
use App\Models\WorkOrderReleaseDetail;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;

class WorkOrderReleaseController extends Controller
{
    public function index()
    {
        $wor = WorkOrderRelease::with('details')->orderBy('job_id', 'desc')->get();
        return view('wor.index', compact('wor'));
    }

    public function create()
    {
        $year = date('y');
        $lastWor = WorkOrderRelease::where('job_id', 'like', $year.'%')
                    ->orderBy('job_id', 'desc')
                    ->first();
        if ($lastWor) {
            $seq = (int) substr($lastWor->job_id, 2, 3);
            $nextSeq = str_pad($seq + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '001';
        }
        $nextId = $year . $nextSeq;
        
        $satuans = Satuan::all();
        return view('wor.create', compact('nextId', 'satuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|string|max:7|unique:work_order_release,job_id',
            'customer' => 'required|string|max:255',
            'jobdesc' => 'required|string',
            'po_proyek' => 'required|string|max:255',
            'tgl_order' => 'required|date',
            'jadwal_kirim' => 'required|date',
            'alamat_kirim' => 'required|string',
            'cp_customer' => 'required|string|max:255',
            'sales' => 'nullable|string|max:255',
            'mail_address' => 'nullable|string|max:255',
            'estimate_man_hour' => 'nullable|numeric',
            'priority' => 'nullable|string|in:High Priority,Standard Time,Low Priority',
            'priority_days' => 'nullable|integer',
            'material' => 'nullable|string',
            'model' => 'nullable|string',
            'equip_qty' => 'nullable|integer',
            'power' => 'nullable|string',
            
            // Details
            'details' => 'array',
            'details.*.desc_part' => 'nullable|string',
            'details.*.part_qty' => 'nullable|integer',
            'details.*.id_satuan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $wor = WorkOrderRelease::create($request->only([
                'job_id', 'customer', 'jobdesc', 'po_proyek', 'tgl_order', 'jadwal_kirim',
                'alamat_kirim', 'cp_customer', 'sales', 'mail_address', 'estimate_man_hour', 'priority', 'priority_days',
                'material', 'model', 'equip_qty', 'power'
            ]));

            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    $detail['job_id'] = $wor->job_id;
                    WorkOrderReleaseDetail::create($detail);
                }
            }

            DB::commit();
            return redirect()->route('wor.index')->with('success', 'Work Order Release berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan WOR: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $wor = WorkOrderRelease::with('details.satuan')->findOrFail($id);
        return view('wor.show', compact('wor'));
    }

    public function edit($id)
    {
        $wor = WorkOrderRelease::with('details')->findOrFail($id);
        $satuans = Satuan::all();
        return view('wor.edit', compact('wor', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer' => 'required|string|max:255',
            'jobdesc' => 'required|string',
            'po_proyek' => 'required|string|max:255',
            'tgl_order' => 'required|date',
            'jadwal_kirim' => 'required|date',
            'alamat_kirim' => 'required|string',
            'cp_customer' => 'required|string|max:255',
            'sales' => 'nullable|string|max:255',
            'mail_address' => 'nullable|string|max:255',
            'estimate_man_hour' => 'nullable|numeric',
            'priority' => 'nullable|string|in:High Priority,Standard Time,Low Priority',
            'priority_days' => 'nullable|integer',
            'material' => 'nullable|string',
            'model' => 'nullable|string',
            'equip_qty' => 'nullable|integer',
            'power' => 'nullable|string',
            
            // Details
            'details' => 'array',
            'details.*.desc_part' => 'nullable|string',
            'details.*.part_qty' => 'nullable|integer',
            'details.*.id_satuan' => 'nullable|string',
        ]);

        $wor = WorkOrderRelease::findOrFail($id);

        try {
            DB::beginTransaction();

            $wor->update(array_merge($request->only([
                'customer', 'jobdesc', 'po_proyek', 'tgl_order', 'jadwal_kirim',
                'alamat_kirim', 'cp_customer', 'sales', 'mail_address', 'estimate_man_hour', 'priority', 'priority_days',
                'material', 'model', 'equip_qty', 'power'
            ]), [
                'status' => 'Pending',
                'approved_by' => null,
                'approved_at' => null,
                'reject_reason' => null
            ]));

            // Delete old details
            $wor->details()->delete();

            // Insert new details
            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    $detail['job_id'] = $wor->job_id;
                    WorkOrderReleaseDetail::create($detail);
                }
            }

            DB::commit();
            return redirect()->route('wor.index')->with('success', 'Work Order Release berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate WOR: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $wor = WorkOrderRelease::findOrFail($id);
        $wor->delete(); // Details should cascade delete based on migration
        return redirect()->route('wor.index')->with('success', 'Work Order Release berhasil dihapus.');
    }

    public function approve($id)
    {
        if (!in_array('DIREKTUR UTAMA', auth()->user()->roles())) {
            abort(403, 'Hanya Direktur Utama yang dapat menyetujui WOR.');
        }

        $wor = WorkOrderRelease::findOrFail($id);
        $wor->update([
            'status' => 'Approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reject_reason' => null
        ]);

        return redirect()->back()->with('success', 'Work Order Release berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        if (!in_array('DIREKTUR UTAMA', auth()->user()->roles())) {
            abort(403, 'Hanya Direktur Utama yang dapat menolak WOR.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ]);

        $wor = WorkOrderRelease::findOrFail($id);
        $wor->update([
            'status' => 'Rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reject_reason' => $request->reject_reason
        ]);

        return redirect()->back()->with('success', 'Work Order Release telah ditolak.');
    }
}

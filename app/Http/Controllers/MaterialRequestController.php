<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\Subproject;
use App\Models\Tugas;
use App\Models\Project;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class MaterialRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = $user->roles();

        // Karyawan hanya bisa create
        if (in_array('KARYAWAN', $roles) && empty(array_intersect(['ADMIN', 'PURCHASING'], $roles))) {
            $requests = \App\Models\MaterialRequest::where('id_karyawan', $user->id_karyawan)->orderBy('no_nota', 'desc')->get();
            $isPurchasing = false;
        } else {
            // Purchasing / Admin bisa lihat semua
            $requests = \App\Models\MaterialRequest::orderBy('no_nota', 'desc')->get();
            $isPurchasing = true;
        }

        return view('material_request.index', compact('requests', 'isPurchasing'));
    }

    public function create()
    {
        $user = auth()->user();
        $roles = $user->roles();
        $isPurchasing = !empty(array_intersect(['PURCHASING', 'ADMIN'], $roles));

        if ($isPurchasing) {
            $jobIds = \App\Models\Project::pluck('job_id')->toArray();
        } else {
            $id_karyawan = $user->id_karyawan;
            $subprojectJobs = \App\Models\Subproject::where('id_karyawan', $id_karyawan)->pluck('job_id')->toArray();
            $tugasJobs = \App\Models\Tugas::with('subproject')->where('id_karyawan', $id_karyawan)->get()->pluck('subproject.job_id')->toArray();
            $jobIds = collect(array_merge($subprojectJobs, $tugasJobs))->filter()->unique();
        }

        $barangs = \App\Models\Barang::all();
        return view('material_request.create', compact('jobIds', 'barangs', 'isPurchasing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required',
            'tanggal' => 'required|date',
            'items' => 'required|array',
            'items.*.req_qty' => 'required|numeric|min:1'
        ]);

        $user = auth()->user();
        $roles = $user->roles();
        $isPurchasing = !empty(array_intersect(['PURCHASING', 'ADMIN'], $roles));

        $mr = MaterialRequest::create([
            'job_id' => $request->job_id,
            'id_karyawan' => $user->id_karyawan,
            'tanggal' => $request->tanggal,
            'status' => $isPurchasing ? 'APPROVED_MANAGER' : 'PENDING_MANAGER'
        ]);

        foreach ($request->items as $item) {
            $isNewItem = !empty($item['nama_barang_baru']);
            
            MaterialRequestDetail::create([
                'no_nota' => $mr->no_nota,
                'id_barang' => $isNewItem ? null : $item['id_barang'],
                'nama_barang_baru' => $isNewItem ? $item['nama_barang_baru'] : null,
                'satuan_baru' => $isNewItem ? $item['satuan_baru'] : null,
                'req_qty' => $item['req_qty'],
            ]);
        }

        return redirect()->route('material_request.index')->with('success', 'Nota Permintaan Barang berhasil dibuat.');
    }
}

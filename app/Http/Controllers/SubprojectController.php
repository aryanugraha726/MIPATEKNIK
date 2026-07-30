<?php

namespace App\Http\Controllers;

use App\Models\Subproject;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\Management;

class SubprojectController extends Controller
{
    public function index()
    {
        // Subprojects are usually viewed from within a Project, so index could redirect or show all.
        return redirect()->route('projects.index');
    }

    public function create(Request $request)
    {
        $job_id = $request->query('job_id');
        $project = Project::findOrFail($job_id);
        
        $projectCode = str_pad($job_id, 5, '0', STR_PAD_LEFT);
        $lastSub = Subproject::where('job_id', $job_id)->orderBy('subproject_id', 'desc')->first();
        if ($lastSub) {
            $lastSeq = (int) substr($lastSub->subproject_id, -2);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }
        $nextId = $projectCode . str_pad($nextSeq, 2, '0', STR_PAD_LEFT);

        $karyawans = Karyawan::select('id_karyawan', 'nm_karyawan')->get();
        $managements = Management::with(['karyawan:id_karyawan,nm_karyawan', 'divisi:id_divisi,nama_divisi'])
            ->select('management_id', 'id_karyawan', 'id_divisi')->get();
        return view('subprojects.create', compact('project', 'karyawans', 'managements', 'nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subproject_id' => 'required|string|max:7|unique:subproject,subproject_id',
            'job_id' => 'required|exists:project,job_id',
            'nama_subproject' => 'required|string|max:50',
            'start_subproject' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'management_id' => 'required|exists:management,management_id',
        ]);

        Subproject::create([
            'subproject_id' => $request->subproject_id,
            'job_id' => $request->job_id,
            'nama_subproject' => $request->nama_subproject,
            'start_subproject' => $request->start_subproject,
            'target_subproject' => date('Y-m-d', strtotime($request->start_subproject . ' + ' . $request->durasi_hari . ' days')),
            'id_karyawan' => $request->id_karyawan,
            'management_id' => $request->management_id,
        ]);

        return redirect()->route('projects.show', $request->job_id)->with('success', 'Subproject berhasil ditambahkan');
    }

    public function show($id)
    {
        $subproject = Subproject::with(['project', 'karyawan', 'management.karyawan', 'tugas.karyawan', 'tugas.vendor'])->findOrFail($id);
        return view('subprojects.show', compact('subproject'));
    }

    public function edit($id)
    {
        $subproject = Subproject::findOrFail($id);
        $project = Project::findOrFail($subproject->job_id);
        $karyawans = Karyawan::select('id_karyawan', 'nm_karyawan')->get();
        $managements = Management::with(['karyawan:id_karyawan,nm_karyawan', 'divisi:id_divisi,nama_divisi'])
            ->select('management_id', 'id_karyawan', 'id_divisi')->get();
        return view('subprojects.edit', compact('subproject', 'project', 'karyawans', 'managements'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_subproject' => 'required|string|max:50',
            'start_subproject' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'management_id' => 'required|exists:management,management_id',
        ]);

        $subproject = Subproject::findOrFail($id);
        $subproject->update($request->only([
            'nama_subproject', 'start_subproject', 'target_subproject', 'id_karyawan', 'management_id'
        ]));

        return redirect()->route('projects.show', $subproject->job_id)->with('success', 'Subproject berhasil diupdate');
    }

    public function destroy($id)
    {
        $subproject = Subproject::findOrFail($id);
        $projectId = $subproject->job_id;
        
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            // Hapus semua tugas yang bernaung di subproject ini
            $subproject->tugas()->delete();
            
            // Hapus subproject
            $subproject->delete();
            
            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('projects.show', $projectId)->with('success', 'Subproject beserta seluruh tugas di dalamnya berhasil dihapus');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('projects.show', $projectId)->with('error', 'Gagal menghapus Subproject.');
        }
    }

    public function toggleStatus($id)
    {
        $subproject = Subproject::findOrFail($id);
        
        // Cek Role
        $user = auth()->user();
        $roles = $user->roles();
        
        // Karyawan biasa hanya bisa update subproject miliknya
        if (in_array('KARYAWAN', $roles) && empty(array_intersect(['ADMIN', 'PPIC'], $roles))) {
            if ($subproject->id_karyawan !== $user->id_karyawan) {
                return redirect()->back()->with('error', 'Anda tidak berhak mengubah status subproject ini.');
            }
        } elseif (empty(array_intersect(['ADMIN', 'PPIC'], $roles))) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengubah status subproject ini.');
        }

        $subproject->is_completed = !$subproject->is_completed;
        if ($subproject->is_completed) {
            $subproject->tanggal_selesai = now()->toDateString();
        } else {
            $subproject->tanggal_selesai = null;
        }
        $subproject->save();

        return redirect()->back()->with('success', 'Status subproject berhasil diperbarui.');
    }
}

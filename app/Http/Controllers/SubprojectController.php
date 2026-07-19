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
        $project_id = $request->query('project_id');
        $project = Project::findOrFail($project_id);
        
        $projectCode = str_pad($project_id, 5, '0', STR_PAD_LEFT);
        $lastSub = Subproject::where('project_id', $project_id)->orderBy('subproject_id', 'desc')->first();
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
            'project_id' => 'required|exists:project,project_id',
            'nama_subproject' => 'required|string|max:50',
            'start_subproject' => 'required|date',
            'target_subproject' => 'required|date',
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'management_id' => 'required|exists:management,management_id',
        ]);

        Subproject::create([
            'subproject_id' => $request->subproject_id,
            'project_id' => $request->project_id,
            'nama_subproject' => $request->nama_subproject,
            'start_subproject' => $request->start_subproject,
            'target_subproject' => $request->target_subproject,
            'id_karyawan' => $request->id_karyawan,
            'management_id' => $request->management_id,
        ]);

        return redirect()->route('projects.show', $request->project_id)->with('success', 'Subproject berhasil ditambahkan');
    }

    public function show($id)
    {
        $subproject = Subproject::with(['project', 'karyawan', 'management.karyawan', 'tugas.karyawan', 'tugas.vendor'])->findOrFail($id);
        return view('subprojects.show', compact('subproject'));
    }

    public function edit($id)
    {
        $subproject = Subproject::findOrFail($id);
        $project = Project::findOrFail($subproject->project_id);
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
            'target_subproject' => 'required|date',
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'management_id' => 'required|exists:management,management_id',
        ]);

        $subproject = Subproject::findOrFail($id);
        $subproject->update($request->only([
            'nama_subproject', 'start_subproject', 'target_subproject', 'id_karyawan', 'management_id'
        ]));

        return redirect()->route('projects.show', $subproject->project_id)->with('success', 'Subproject berhasil diupdate');
    }

    public function destroy($id)
    {
        $subproject = Subproject::findOrFail($id);
        $projectId = $subproject->project_id;
        $subproject->delete();
        return redirect()->route('projects.show', $projectId)->with('success', 'Subproject berhasil dihapus');
    }
}

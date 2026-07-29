<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Management;
class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('management.karyawan')->orderBy('start_project', 'desc')->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $nextId = Project::max('job_id') + 1;
        $managements = Management::with(['karyawan:id_karyawan,nm_karyawan', 'divisi:id_divisi,nama_divisi'])
            ->select('management_id', 'id_karyawan', 'id_divisi')->get();
        return view('projects.create', compact('managements', 'nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|integer|unique:project,job_id',
            'nama_project' => 'required|string|max:50',
            'prioritas' => 'required|string|max:8',
            'start_project' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
            'management_id' => 'required|exists:management,management_id',
        ]);
        
        Project::create([
            'job_id' => $request->job_id,
            'nama_project' => $request->nama_project,
            'prioritas' => $request->prioritas,
            'start_project' => $request->start_project,
            'target_project' => date('Y-m-d', strtotime($request->start_project . ' + ' . $request->durasi_hari . ' days')),
            'management_id' => $request->management_id,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project berhasil ditambahkan');
    }

    public function show($id)
    {
        $project = Project::with(['management.karyawan', 'subprojects.karyawan'])->findOrFail($id);
        return view('projects.show', compact('project'));
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $managements = Management::with(['karyawan:id_karyawan,nm_karyawan', 'divisi:id_divisi,nama_divisi'])
            ->select('management_id', 'id_karyawan', 'id_divisi')->get();
        return view('projects.edit', compact('project', 'managements'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_project' => 'required|string|max:50',
            'prioritas' => 'required|string|max:8',
            'start_project' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
            'management_id' => 'required|exists:management,management_id',
        ]);

        $project = Project::findOrFail($id);
        $data = $request->only(['nama_project', 'prioritas', 'start_project', 'management_id']);
        $data['target_project'] = date('Y-m-d', strtotime($request->start_project . ' + ' . $request->durasi_hari . ' days'));
        $project->update($data);

        return redirect()->route('projects.index')->with('success', 'Project berhasil diupdate');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus');
    }
}

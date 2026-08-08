<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;
use App\Models\Subproject;
use App\Models\Karyawan;
use App\Models\Vendor;

class TugasController extends Controller
{
    public function index()
    {
        return redirect()->route('projects.index');
    }



    public function show($id)
    {
        // Tugas doesn't typically need its own show page since it's displayed in subproject.
        return redirect()->route('projects.index');
    }

    public function edit($id)
    {
        $tugas = Tugas::findOrFail($id);
        $subproject = Subproject::findOrFail($tugas->subproject_id);
        $project = \App\Models\Project::with('workOrderRelease')->findOrFail($subproject->job_id);
        $workScope = [];
        if ($project->workOrderRelease && $project->workOrderRelease->work_scope) {
            $workScope = json_decode($project->workOrderRelease->work_scope, true) ?: [];
        }
        $karyawans = Karyawan::select('id_karyawan', 'nm_karyawan')
            ->withCount(['subproject' => function ($query) {
                $query->where('is_completed', 0);
            }, 'tugas' => function ($query) {
                $query->where('is_completed', 0);
            }])->get();
        $vendors = Vendor::select('id_vendor', 'nama_vendor')->get();
        return view('tugas.edit', compact('tugas', 'subproject', 'karyawans', 'vendors', 'workScope'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tugas' => 'required|string|max:50',
            'qty' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:50',
            'start_tugas' => 'required|date',
            'durasi_hari' => 'required|integer|min:0',
            'id_karyawan' => 'nullable|exists:karyawan,id_karyawan',
            'id_vendor' => 'nullable|exists:vendor,id_vendor',
        ]);

        $tugas = Tugas::findOrFail($id);
        $tugas->update([
            'tugas' => $request->tugas,
            'qty' => $request->qty,
            'unit' => $request->unit,
            'start_tugas' => $request->start_tugas,
            'target_tugas' => date('Y-m-d', strtotime($request->start_tugas . ' + ' . $request->durasi_hari . ' days')),
            'id_karyawan' => $request->id_karyawan ?: null,
            'id_vendor' => $request->id_vendor ?: null,
        ]);

        return redirect()->route('subprojects.show', $tugas->subproject_id)->with('success', 'Rincian tugas berhasil diupdate');
    }

    public function toggleStatus($id)
    {
        $tugas = Tugas::findOrFail($id);
        
        // Cek Role
        $user = auth()->user();
        $roles = $user->roles();
        
        // Karyawan biasa hanya bisa update tugas miliknya
        if (in_array('KARYAWAN', $roles) && empty(array_intersect(['ADMIN', 'PPIC'], $roles))) {
            if ($tugas->id_karyawan !== $user->id_karyawan) {
                return redirect()->back()->with('error', 'Anda tidak berhak mengubah status tugas ini.');
            }
        } elseif (empty(array_intersect(['ADMIN', 'PPIC'], $roles))) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengubah status tugas ini.');
        }

        $tugas->is_completed = !$tugas->is_completed;
        if ($tugas->is_completed) {
            $tugas->tanggal_selesai = now()->toDateString();
        } else {
            $tugas->tanggal_selesai = null;
        }
        $tugas->save();

        $this->cascadeCompletion($tugas->subproject_id);

        return redirect()->back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        $subprojectId = $tugas->subproject_id;
        $tugas->delete();
        
        $this->cascadeCompletion($subprojectId);
        
        return redirect()->route('subprojects.show', $subprojectId)->with('success', 'Rincian tugas berhasil dihapus');
    }

    private function cascadeCompletion($subprojectId)
    {
        $subproject = Subproject::find($subprojectId);
        if ($subproject) {
            $totalTugas = Tugas::where('subproject_id', $subprojectId)->count();
            $completedTugas = Tugas::where('subproject_id', $subprojectId)->where('is_completed', 1)->count();
            
            $wasCompleted = $subproject->is_completed;
            $nowCompleted = ($totalTugas > 0 && $totalTugas == $completedTugas);
            
            // If subproject had no tasks (all deleted), it shouldn't auto-complete unless we consider 0/0 as complete, 
            // but let's default to false if no tasks.
            if ($totalTugas == 0) {
                $nowCompleted = false;
            }
            
            if ($wasCompleted != $nowCompleted) {
                $subproject->update([
                    'is_completed' => $nowCompleted ? 1 : 0,
                    'tanggal_selesai' => $nowCompleted ? now()->toDateString() : null
                ]);
                
                // Cascade to Project
                $project = \App\Models\Project::find($subproject->job_id);
                if ($project) {
                    $totalSub = Subproject::where('job_id', $project->job_id)->count();
                    $completedSub = Subproject::where('job_id', $project->job_id)->where('is_completed', 1)->count();
                    
                    $projNowCompleted = ($totalSub > 0 && $totalSub == $completedSub);
                    
                    $project->update([
                        'is_completed' => $projNowCompleted ? 1 : 0,
                        'tanggal_selesai' => $projNowCompleted ? now()->toDateString() : null
                    ]);
                }
            }
        }
    }
}

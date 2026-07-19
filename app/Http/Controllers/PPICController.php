<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class PPICController extends Controller
{
    public function index()
    {
        // Get all projects for the dashboard
        $projects = Project::with(['management.karyawan', 'management.divisi'])->orderBy('project_id', 'desc')->get();
        return view('ppic.index', compact('projects'));
    }

    public function gantt($id)
    {
        $project = Project::with(['subprojects.tugas'])->findOrFail($id);

        $ganttTasks = [];
        
        // Add Subprojects
        foreach ($project->subprojects as $sub) {
            $ganttTasks[] = [
                'id' => 'Subproject-' . $sub->subproject_id,
                'name' => 'SP: ' . $sub->nama_subproject,
                'start' => date('Y-m-d', strtotime($sub->start_subproject)),
                'end' => date('Y-m-d', strtotime($sub->target_subproject)),
                'progress' => 0, 
                'dependencies' => '',
                'custom_class' => 'bar-subproject'
            ];
            
            // Add Tugas under this Subproject
            foreach ($sub->tugas as $tugas) {
                $ganttTasks[] = [
                    'id' => 'Tugas-' . $tugas->tugas_id,
                    'name' => 'Tugas: ' . $tugas->tugas,
                    'start' => date('Y-m-d', strtotime($tugas->start_tugas)),
                    'end' => date('Y-m-d', strtotime($tugas->target_tugas)),
                    'progress' => 0,
                    'dependencies' => '',
                    'custom_class' => 'bar-tugas'
                ];
            }
        }

        return view('ppic.gantt', compact('project', 'ganttTasks'));
    }

    /**
     * Menu Karyawan: Menampilkan project yang terkait dengan karyawan yang login
     */
    public function myProjects()
    {
        $karyawanId = auth()->user()->id_karyawan;
        
        // Cari project yang punya subproject/tugas dimana karyawan ini terlibat
        $projects = Project::whereHas('subprojects', function($q) use ($karyawanId) {
            $q->where('id_karyawan', $karyawanId)
              ->orWhereHas('tugas', function($q2) use ($karyawanId) {
                  $q2->where('id_karyawan', $karyawanId);
              });
        })->with(['management.karyawan', 'management.divisi'])->orderBy('project_id', 'desc')->get();
        
        return view('karyawan.projects', compact('projects'));
    }

    /**
     * Menu Karyawan: Gantt chart untuk project karyawan
     */
    public function myGantt($id)
    {
        // Gunakan method gantt yang sama
        return $this->gantt($id);
    }
}

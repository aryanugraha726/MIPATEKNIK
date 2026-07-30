<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class PPICController extends Controller
{
    public function index()
    {
        // Get all projects for the dashboard
        $projects = Project::with(['management.karyawan', 'management.divisi'])->orderBy('job_id', 'desc')->get();
        return view('ppic.index', compact('projects'));
    }

    public function gantt($id)
    {
        $project = Project::with(['subprojects.tugas'])->findOrFail($id);

        $ganttTasks = [];
        $subIndex = 1;
        $maxEndDate = strtotime($project->target_project);
        
        // Add Subprojects
        foreach ($project->subprojects as $sub) {
            $subEnd = strtotime($sub->target_subproject);
            if ($subEnd > $maxEndDate) $maxEndDate = $subEnd;

            $ganttTasks[] = [
                'id' => 'Subproject-' . $sub->subproject_id,
                'name' => $subIndex . '. ' . $sub->nama_subproject,
                'start' => date('Y-m-d', strtotime($sub->start_subproject)),
                'end' => date('Y-m-d', strtotime('+1 day', $subEnd)),
                'progress' => 0, 
                'dependencies' => '',
                'custom_class' => 'bar-subproject'
            ];
            
            // Add Tugas under this Subproject
            foreach ($sub->tugas as $tugas) {
                $tugasEnd = strtotime($tugas->target_tugas);
                if ($tugasEnd > $maxEndDate) $maxEndDate = $tugasEnd;

                $ganttTasks[] = [
                    'id' => 'Tugas-' . $tugas->tugas_id,
                    'name' => $tugas->tugas,
                    'start' => date('Y-m-d', strtotime($tugas->start_tugas)),
                    'end' => date('Y-m-d', strtotime('+1 day', $tugasEnd)),
                    'progress' => 0,
                    'dependencies' => '',
                    'custom_class' => 'bar-tugas'
                ];
            }
            $subIndex++;
        }

        // Add Project as the root task at the VERY BEGINNING of the array
        array_unshift($ganttTasks, [
            'id' => 'Project-' . $project->job_id,
            'name' => $project->nama_project,
            'start' => date('Y-m-d', strtotime($project->start_project)),
            'end' => date('Y-m-d', strtotime('+1 day', $maxEndDate)),
            'progress' => 0, 
            'dependencies' => '',
            'custom_class' => 'bar-project'
        ]);

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
        })->with(['management.karyawan', 'management.divisi'])->orderBy('job_id', 'desc')->get();
        
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

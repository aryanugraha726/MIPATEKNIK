<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Management;

class PPICController extends Controller
{
    public function index()
    {
        // Get all projects for the dashboard
        $projects = Project::with(['management.karyawan.divisi'])->orderBy('job_id', 'desc')->get();
        return view('ppic.index', compact('projects'));
    }

    public function gantt($id)
    {
        $project = Project::with(['subprojects' => function ($query) {
            $query->orderBy('subproject_id', 'asc');
        }, 'subprojects.tugas' => function ($query) {
            $query->orderBy('tugas_id', 'asc');
        }])->findOrFail($id);

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
                'qty' => '',
                'unit' => '',
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
                    'qty' => $tugas->qty ?? '-',
                    'unit' => $tugas->unit ?? '-',
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
            'qty' => '',
            'unit' => '',
            'start' => date('Y-m-d', strtotime($project->start_project)),
            'end' => date('Y-m-d', strtotime('+1 day', $maxEndDate)),
            'progress' => 0, 
            'dependencies' => '',
            'custom_class' => 'bar-project'
        ]);

        $managements = Management::with(['karyawan.divisi'])->select('management_id', 'id_karyawan')->get();
        $karyawans = \App\Models\Karyawan::select('id_karyawan', 'nm_karyawan')
            ->withCount(['subproject' => function ($q) { $q->where('is_completed', 0); },
                         'tugas'      => function ($q) { $q->where('is_completed', 0); }])
            ->get();
        $vendors = \App\Models\Vendor::select('id_vendor', 'nama_vendor')->get();

        // Work Scope dari WOR yang terhubung ke project ini
        $workScope = [];
        $projectWithWor = \App\Models\Project::with('workOrderRelease.details.satuan')->find($project->job_id);
        if ($projectWithWor && $projectWithWor->workOrderRelease && $projectWithWor->workOrderRelease->details) {
            foreach ($projectWithWor->workOrderRelease->details as $detail) {
                $workScope[] = [
                    'part_description' => $detail->desc_part,
                    'qty'  => $detail->part_qty,
                    'unit' => $detail->satuan ? $detail->satuan->nama_satuan : $detail->id_satuan,
                ];
            }
        }

        // Hitung jumlah subproyek yang dibawahi setiap penanggung jawab (management)
        $subprojectCounts = \App\Models\Subproject::selectRaw('management_id, COUNT(*) as total')
            ->groupBy('management_id')
            ->pluck('total', 'management_id');

        // Hitung jumlah tugas yang dikerjakan setiap operator (karyawan)
        $tugasCounts = \App\Models\Tugas::selectRaw('id_karyawan, COUNT(*) as total')
            ->whereNotNull('id_karyawan')
            ->groupBy('id_karyawan')
            ->pluck('total', 'id_karyawan');

        return view('ppic.gantt', compact('project', 'ganttTasks', 'managements', 'karyawans', 'vendors', 'workScope', 'subprojectCounts', 'tugasCounts'));
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
        })->with(['management.karyawan.divisi'])->orderBy('job_id', 'desc')->get();
        
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

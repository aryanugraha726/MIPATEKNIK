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



    public function show($id)
    {
        $subproject = Subproject::with(['project', 'karyawan', 'management.karyawan', 'tugas.karyawan', 'tugas.vendor'])->findOrFail($id);
        return view('subprojects.show', compact('subproject'));
    }

    public function edit($id)
    {
        $subproject = Subproject::findOrFail($id);
        $project = Project::with('workOrderRelease.details.satuan')->findOrFail($subproject->job_id);
        
        $workScope = [];
        if ($project->workOrderRelease && $project->workOrderRelease->details) {
            foreach ($project->workOrderRelease->details as $detail) {
                $workScope[] = [
                    'part_description' => $detail->desc_part,
                    'qty' => $detail->part_qty,
                    'unit' => $detail->satuan ? $detail->satuan->nama_satuan : $detail->id_satuan
                ];
            }
        }
        $karyawans = Karyawan::select('id_karyawan', 'nm_karyawan')
            ->withCount(['subproject' => function ($query) {
                $query->where('is_completed', 0);
            }, 'tugas' => function ($query) {
                $query->where('is_completed', 0);
            }])->get();
        $managements = Management::with(['karyawan.divisi'])
            ->select('management_id', 'id_karyawan')->get();
        return view('subprojects.edit', compact('subproject', 'project', 'karyawans', 'managements', 'workScope'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_subproject' => 'required|string|max:50',
            'start_subproject' => 'required|date',
            'durasi_hari' => 'required|integer|min:0',
            'id_karyawan' => 'nullable|exists:karyawan,id_karyawan',
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

        $this->cascadeProjectCompletion($subproject->job_id);

        return redirect()->back()->with('success', 'Status subproject berhasil diperbarui.');
    }

    public function bulkStore(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        if (!$request->has('subproyek')) {
            return redirect()->back()->with('error', 'Tidak ada data subproyek yang dikirim.');
        }

        // Job ID di-pad 5 digit. Subproject akan jadi 7 digit (sisa 2 digit dari VARCHAR 9 dibiarkan kosong)
        $projectCode = str_pad($id, 5, '0', STR_PAD_LEFT); 
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $submittedSubIds = [];
            $submittedTugasIds = [];

            foreach ($request->subproyek as $subData) {
                if (empty($subData['nama']) || empty($subData['mulai']) || empty($subData['selesai']) || empty($subData['management_id'])) {
                    continue;
                }

                $nextSubId = null;

                if (!empty($subData['id'])) {
                    // Update existing
                    $subproject = Subproject::where('subproject_id', $subData['id'])->first();
                    if ($subproject) {
                        $subproject->update([
                            'nama_subproject' => $subData['nama'],
                            'start_subproject' => $subData['mulai'],
                            'target_subproject' => $subData['selesai'], 
                            'id_karyawan' => $subData['id_karyawan'] ?? null,
                            'management_id' => $subData['management_id'],
                        ]);
                        $nextSubId = $subproject->subproject_id;
                    }
                }

                if (!$nextSubId) {
                    // Generate subproject_id (7 karakter default)
                    $lastSub = Subproject::where('job_id', $id)->orderBy('subproject_id', 'desc')->first();
                    if ($lastSub) {
                        // Ambil urutan berdasarkan panjang normal (7 karakter) atau ambil 2 digit terakhir dari ID
                        $lastSeq = (int) substr(trim($lastSub->subproject_id), -2);
                        $nextSubSeq = $lastSeq + 1;
                    } else {
                        $nextSubSeq = 1;
                    }
                    $nextSubId = $projectCode . str_pad($nextSubSeq, 2, '0', STR_PAD_LEFT);

                    $subproject = Subproject::create([
                        'subproject_id' => $nextSubId,
                        'job_id' => $id,
                        'nama_subproject' => $subData['nama'],
                        'start_subproject' => $subData['mulai'],
                        'target_subproject' => $subData['selesai'], 
                        'id_karyawan' => $subData['id_karyawan'] ?? null,
                        'management_id' => $subData['management_id'],
                    ]);
                }
                
                $submittedSubIds[] = $nextSubId;

                // Save Nested Tugas
                if (!empty($subData['tugas']) && is_array($subData['tugas'])) {
                    $nextTugasSeq = 1;
                    
                    // Cek ID Tugas terakhir di subproject ini jika ada (walau bulk insert biasanya baru)
                    $lastTugas = \App\Models\Tugas::where('subproject_id', $nextSubId)->orderBy('tugas_id', 'desc')->first();
                    if ($lastTugas) {
                        $nextTugasSeq = ((int) substr(trim($lastTugas->tugas_id), -2)) + 1;
                    }

                    foreach ($subData['tugas'] as $tugasData) {
                        if (empty($tugasData['nama']) || empty($tugasData['mulai']) || empty($tugasData['selesai'])) {
                            continue;
                        }

                        $nextTugasId = null;

                        if (!empty($tugasData['id'])) {
                            // Update existing
                            $tugas = \App\Models\Tugas::where('tugas_id', $tugasData['id'])->first();
                            if ($tugas) {
                                $tugas->update([
                                    'tugas'        => $tugasData['nama'],
                                    'qty'          => $tugasData['qty'] ?? null,
                                    'unit'         => $tugasData['unit'] ?? null,
                                    'start_tugas'  => $tugasData['mulai'],
                                    'target_tugas' => $tugasData['selesai'],
                                    'id_karyawan'  => $tugasData['id_karyawan'] ?? null,
                                    'id_vendor'    => $tugasData['id_vendor'] ?? null,
                                ]);
                                $nextTugasId = $tugas->tugas_id;
                            }
                        }

                        if (!$nextTugasId) {
                            // Generate tugas_id (9 karakter default)
                            $nextTugasId = $nextSubId . str_pad($nextTugasSeq, 2, '0', STR_PAD_LEFT);

                            \App\Models\Tugas::create([
                                'tugas_id'     => $nextTugasId,
                                'subproject_id'=> $nextSubId,
                                'tugas'        => $tugasData['nama'],
                                'qty'          => $tugasData['qty'] ?? null,
                                'unit'         => $tugasData['unit'] ?? null,
                                'start_tugas'  => $tugasData['mulai'],
                                'target_tugas' => $tugasData['selesai'],
                                'id_karyawan'  => $tugasData['id_karyawan'] ?? null,
                                'id_vendor'    => $tugasData['id_vendor'] ?? null,
                            ]);
                            $nextTugasSeq++;
                        }
                        
                        $submittedTugasIds[] = $nextTugasId;
                    }
                }
            }

            // DELETE unsubmitted tasks for this project
            $allProjectSubIds = Subproject::where('job_id', $id)->pluck('subproject_id')->toArray();
            
            \App\Models\Tugas::whereIn('subproject_id', $allProjectSubIds)
                ->whereNotIn('tugas_id', $submittedTugasIds)
                ->delete();
                
            // Delete subprojects that belong to this project but were not submitted
            Subproject::where('job_id', $id)
                ->whereNotIn('subproject_id', $submittedSubIds)
                ->delete();

            $this->cascadeProjectCompletion($id);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Timeline & Subproyek beserta Tugas berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function cascadeProjectCompletion($jobId)
    {
        $project = \App\Models\Project::find($jobId);
        if ($project) {
            $totalSub = Subproject::where('job_id', $jobId)->count();
            $completedSub = Subproject::where('job_id', $jobId)->where('is_completed', 1)->count();
            
            $wasCompleted = $project->is_completed;
            $nowCompleted = ($totalSub > 0 && $totalSub == $completedSub);
            
            if ($totalSub == 0) {
                $nowCompleted = false;
            }
            
            if ($wasCompleted != $nowCompleted) {
                $project->update([
                    'is_completed' => $nowCompleted ? 1 : 0,
                    'tanggal_selesai' => $nowCompleted ? now()->toDateString() : null
                ]);
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ManagerApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = $user->roles();
        
        $managementIds = \App\Models\Management::where('id_karyawan', $user->id_karyawan)->pluck('management_id')->toArray();

        // Admin has full access, otherwise must be in Management
        if (empty($managementIds) && empty(array_intersect(['ADMIN', 'MANAGEMENT'], $roles))) {
            return redirect()->route('dashboard.index')->with('error', 'Anda tidak memiliki divisi di manajemen.');
        }

        if (in_array('ADMIN', $roles)) {
            $requests = \App\Models\MaterialRequest::with(['karyawan', 'details.barang', 'details.barang.satuan'])
                        ->where('status', 'PENDING_MANAGER')
                        ->orderBy('tanggal', 'desc')
                        ->get();
        } else {
            $managedJobIds = Project::whereIn('management_id', $managementIds)->pluck('job_id');
            $requests = MaterialRequest::with(['details.barang', 'workOrderRelease', 'karyawan'])
                        ->whereIn('job_id', $managedJobIds)
                        ->where('status', 'PENDING_MANAGER')
                        ->orderBy('tanggal', 'desc')
                        ->get();
        }

        return view('manager_approval.index', compact('requests'));
    }

    public function approve($no_nota)
    {
        $mr = MaterialRequest::findOrFail($no_nota);
        $mr->update(['status' => 'APPROVED_MANAGER']);
        return redirect()->route('manager_approval.index')->with('success', 'Permintaan disetujui.');
    }

    public function reject($no_nota)
    {
        $mr = MaterialRequest::findOrFail($no_nota);
        $mr->update(['status' => 'REJECTED_MANAGER']);
        return redirect()->route('manager_approval.index')->with('success', 'Permintaan ditolak.');
    }
}

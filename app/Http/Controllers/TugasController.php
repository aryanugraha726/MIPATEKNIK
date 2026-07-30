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

    public function create(Request $request)
    {
        $subproject_id = $request->query('subproject_id');
        $subproject = Subproject::findOrFail($subproject_id);
        
        $lastTugas = Tugas::where('subproject_id', $subproject_id)->orderBy('tugas_id', 'desc')->first();
        if ($lastTugas) {
            $lastSeq = (int) substr($lastTugas->tugas_id, -2);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }
        $nextId = $subproject_id . str_pad($nextSeq, 2, '0', STR_PAD_LEFT);

        $karyawans = Karyawan::select('id_karyawan', 'nm_karyawan')->get();
        $vendors = Vendor::select('id_vendor', 'nama_vendor')->get();
        return view('tugas.create', compact('subproject', 'karyawans', 'vendors', 'nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|string|max:9|unique:tugas,tugas_id',
            'subproject_id' => 'required|exists:subproject,subproject_id',
            'tugas' => 'required|string|max:50',
            'start_tugas' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
            'id_karyawan' => 'nullable|exists:karyawan,id_karyawan',
            'id_vendor' => 'nullable|exists:vendor,id_vendor',
        ]);
        
        Tugas::create([
            'tugas_id' => $request->tugas_id,
            'subproject_id' => $request->subproject_id,
            'tugas' => $request->tugas,
            'start_tugas' => $request->start_tugas,
            'target_tugas' => date('Y-m-d', strtotime($request->start_tugas . ' + ' . $request->durasi_hari . ' days')),
            'id_karyawan' => $request->id_karyawan ?: null,
            'id_vendor' => $request->id_vendor ?: null,
        ]);

        return redirect()->route('subprojects.show', $request->subproject_id)->with('success', 'Rincian tugas berhasil ditambahkan');
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
        $karyawans = Karyawan::select('id_karyawan', 'nm_karyawan')->get();
        $vendors = Vendor::select('id_vendor', 'nama_vendor')->get();
        return view('tugas.edit', compact('tugas', 'subproject', 'karyawans', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tugas' => 'required|string|max:50',
            'start_tugas' => 'required|date',
            'durasi_hari' => 'required|integer|min:1',
            'id_karyawan' => 'nullable|exists:karyawan,id_karyawan',
            'id_vendor' => 'nullable|exists:vendor,id_vendor',
        ]);

        $tugas = Tugas::findOrFail($id);
        $tugas->update([
            'tugas' => $request->tugas,
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

        return redirect()->back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        $subprojectId = $tugas->subproject_id;
        $tugas->delete();
        return redirect()->route('subprojects.show', $subprojectId)->with('success', 'Rincian tugas berhasil dihapus');
    }
}

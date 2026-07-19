<?php

namespace App\Http\Controllers;

use App\Models\Management;
use App\Models\Karyawan;
use App\Models\Divisi;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function index()
    {
        $managements = Management::with(['karyawan', 'divisi'])->get();
        return view('management.index', compact('managements'));
    }

    public function create()
    {
        $karyawans = Karyawan::all();
        $divisis = Divisi::all();
        return view('management.create', compact('karyawans', 'divisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|integer',
            'id_divisi' => 'required|integer',
        ]);
        
        $newId = Management::max('management_id') + 1;

        Management::create([
            'management_id' => $newId ?: 1,
            'id_karyawan' => $request->id_karyawan,
            'id_divisi' => $request->id_divisi,
        ]);

        return redirect()->route('management.index')->with('success', 'Management PIC berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $management = Management::findOrFail($id);
        $karyawans = Karyawan::all();
        $divisis = Divisi::all();
        return view('management.edit', compact('management', 'karyawans', 'divisis'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_karyawan' => 'required|integer',
            'id_divisi' => 'required|integer',
        ]);

        $management = Management::findOrFail($id);
        $management->update([
            'id_karyawan' => $request->id_karyawan,
            'id_divisi' => $request->id_divisi,
        ]);

        return redirect()->route('management.index')->with('success', 'Management PIC berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $management = Management::findOrFail($id);
        try {
            $management->delete();
            return redirect()->route('management.index')->with('success', 'Management PIC berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('management.index')->with('error', 'Gagal menghapus Management karena sedang digunakan oleh entitas lain.');
        }
    }
}

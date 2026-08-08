<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Divisi;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karyawans = Karyawan::with('divisi')->get();
        return view('karyawan.index', compact('karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisis = Divisi::all();
        return view('karyawan.create', compact('divisis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nm_karyawan' => 'required|string|max:25',
            'id_divisi' => 'required|array',
            'id_divisi.*' => 'integer',
        ]);
        
        $newId = Karyawan::max('id_karyawan') + 1;

        $karyawan = Karyawan::create([
            'id_karyawan' => $newId ?: 1,
            'nm_karyawan' => $request->nm_karyawan,
        ]);

        $karyawan->divisi()->sync($request->id_divisi);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $divisis = Divisi::all();
        return view('karyawan.edit', compact('karyawan', 'divisis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nm_karyawan' => 'required|string|max:25',
            'id_divisi' => 'required|array',
            'id_divisi.*' => 'integer',
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update([
            'nm_karyawan' => $request->nm_karyawan,
        ]);

        $karyawan->divisi()->sync($request->id_divisi);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        try {
            $karyawan->delete();
            return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('karyawan.index')->with('error', 'Gagal menghapus Karyawan karena sedang digunakan oleh entitas lain.');
        }
    }
}

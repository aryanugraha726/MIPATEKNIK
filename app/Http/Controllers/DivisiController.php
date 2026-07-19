<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();
        return view('divisi.index', compact('divisis'));
    }

    public function create()
    {
        return view('divisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:50',
        ]);
        
        $newId = Divisi::max('id_divisi') + 1;

        Divisi::create([
            'id_divisi' => $newId ?: 1,
            'nama_divisi' => $request->nama_divisi,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $divisi = Divisi::findOrFail($id);
        return view('divisi.edit', compact('divisi'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:50',
        ]);

        $divisi = Divisi::findOrFail($id);
        $divisi->update([
            'nama_divisi' => $request->nama_divisi,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $divisi = Divisi::findOrFail($id);
        try {
            $divisi->delete();
            return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('divisi.index')->with('error', 'Gagal menghapus Divisi karena sedang digunakan oleh entitas lain.');
        }
    }
}

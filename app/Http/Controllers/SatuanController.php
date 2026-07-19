<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = Satuan::all();
        return view('satuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:10',
        ]);
        
        $newId = Satuan::max('id_satuan') + 1;

        Satuan::create([
            'id_satuan' => $newId ?: 1,
            'nama_satuan' => strtoupper($request->nama_satuan),
        ]);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:10',
        ]);

        $satuan = Satuan::findOrFail($id);
        $satuan->update([
            'nama_satuan' => strtoupper($request->nama_satuan),
        ]);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $satuan = Satuan::findOrFail($id);
        try {
            $satuan->delete();
            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('satuan.index')->with('error', 'Gagal menghapus Satuan karena sedang digunakan oleh entitas lain.');
        }
    }
}

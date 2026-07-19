<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return view('vendor.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:20',
        ]);
        
        $newId = Vendor::max('id_vendor') + 1;

        Vendor::create([
            'id_vendor' => $newId ?: 1,
            'nama_vendor' => $request->nama_vendor,
        ]);

        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('vendor.edit', compact('vendor'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:20',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update([
            'nama_vendor' => $request->nama_vendor,
        ]);

        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        try {
            $vendor->delete();
            return redirect()->route('vendor.index')->with('success', 'Vendor berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('vendor.index')->with('error', 'Gagal menghapus Vendor karena sedang digunakan oleh entitas lain.');
        }
    }
}

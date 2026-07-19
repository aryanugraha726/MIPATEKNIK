<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $kategoris = KategoriBarang::all();
        $satuans = Satuan::all();
        return view('barang.create', compact('kategoris', 'satuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'nullable|string|max:50|unique:barang,id_barang',
            'nama_barang' => 'required|string|max:100',
            'id_kategori' => 'required|string',
            'id_satuan' => 'required|integer',
            'harga' => 'required|numeric|min:0',
        ]);

        $id_barang = $request->id_barang;
        if (empty($id_barang)) {
            $id_barang = $this->generateNextId($request->id_kategori);
        }

        // Karena Barang menggunakan incrementing=false dan timestamps=false
        // Kita hanya perlu memasukkan data secara eksplisit.
        Barang::insert([
            'id_barang' => $id_barang,
            'nama_barang' => $request->nama_barang,
            'id_kategori' => $request->id_kategori,
            'id_satuan' => $request->id_satuan,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'id_barang' => 'nullable|string|max:50|unique:barang,id_barang',
            'nama_barang' => 'required|string|max:100',
            'id_kategori' => 'required|string',
            'id_satuan' => 'required|integer',
            'harga' => 'required|numeric|min:0',
        ]);

        $id_barang = $request->id_barang;
        if (empty($id_barang)) {
            $id_barang = $this->generateNextId($request->id_kategori);
        }

        try {
            Barang::insert([
                'id_barang' => $id_barang,
                'nama_barang' => $request->nama_barang,
                'id_kategori' => $request->id_kategori,
                'id_satuan' => $request->id_satuan,
                'harga' => $request->harga,
            ]);

            return response()->json([
                'success' => true,
                'barang' => [
                    'id_barang' => $id_barang,
                    'nama_barang' => $request->nama_barang,
                    'harga' => $request->harga,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan barang: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateNextId($id_kategori) 
    {
        $lastBarang = Barang::where('id_kategori', $id_kategori)
            ->orderByRaw('LENGTH(id_barang) DESC')
            ->orderBy('id_barang', 'desc')
            ->first();
            
        $nextNumber = 1;
        if ($lastBarang) {
            $numberStr = str_replace($id_kategori, '', $lastBarang->id_barang);
            $nextNumber = intval($numberStr) + 1;
        }
        return $id_kategori . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getNextId($id_kategori)
    {
        return response()->json([
            'next_id' => $this->generateNextId($id_kategori)
        ]);
    }

    public function edit(string $id)
    {
        $barang = Barang::findOrFail($id);
        $kategoris = KategoriBarang::all();
        $satuans = Satuan::all();
        return view('barang.edit', compact('barang', 'kategoris', 'satuans'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'id_kategori' => 'required|string',
            'id_satuan' => 'required|integer',
            'harga' => 'required|numeric|min:0',
        ]);

        Barang::where('id_barang', $id)->update([
            'nama_barang' => $request->nama_barang,
            'id_kategori' => $request->id_kategori,
            'id_satuan' => $request->id_satuan,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        try {
            Barang::where('id_barang', $id)->delete();
            return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('barang.index')->with('error', 'Gagal menghapus Barang karena sedang digunakan oleh transaksi atau entitas lain.');
        }
    }
}

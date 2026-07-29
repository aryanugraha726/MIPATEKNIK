<?php

namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $shippingAddresses = ShippingAddress::all();
        return view('shipping_address.index', compact('shippingAddresses'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return view('shipping_address.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'id_lokasi' => 'required|unique:shipping_address,id_lokasi|max:255',
            'nama_lokasi' => 'required|max:255',
            'alamat_mipa' => 'required',
            'phone_mipa' => 'nullable|max:255',
            'cp_mipa' => 'nullable|max:255',
            'email_mipa' => 'nullable|max:255',
        ]);

        ShippingAddress::create($request->all());

        return redirect()->route('shipping-address.index')->with('success', 'Alamat Pengiriman berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $shippingAddress = ShippingAddress::findOrFail($id);
        return view('shipping_address.edit', compact('shippingAddress'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $shippingAddress = ShippingAddress::findOrFail($id);

        $request->validate([
            'nama_lokasi' => 'required|max:255',
            'alamat_mipa' => 'required',
            'phone_mipa' => 'nullable|max:255',
            'cp_mipa' => 'nullable|max:255',
            'email_mipa' => 'nullable|max:255',
        ]);

        $shippingAddress->update($request->all());

        return redirect()->route('shipping-address.index')->with('success', 'Alamat Pengiriman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!array_intersect(['ADMIN', 'PURCHASING'], $user->roles())) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $shippingAddress = ShippingAddress::findOrFail($id);
        
        try {
            $shippingAddress->delete();
            return redirect()->route('shipping-address.index')->with('success', 'Alamat Pengiriman berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('shipping-address.index')->with('error', 'Gagal menghapus alamat pengiriman, mungkin karena data sedang digunakan oleh Purchase Order.');
        }
    }
}

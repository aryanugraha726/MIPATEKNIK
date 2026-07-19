<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        return view('role.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string|max:10',
        ]);
        
        $newId = Role::max('role_id') + 1;

        Role::create([
            'role_id' => $newId ?: 1,
            'nama_role' => strtoupper($request->nama_role),
        ]);

        return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('role.edit', compact('role'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_role' => 'required|string|max:10',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'nama_role' => strtoupper($request->nama_role),
        ]);

        return redirect()->route('role.index')->with('success', 'Role berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        try {
            $role->delete();
            return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('role.index')->with('error', 'Gagal menghapus Role karena sedang digunakan oleh entitas lain.');
        }
    }
}

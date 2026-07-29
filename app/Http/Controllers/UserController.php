<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['karyawan'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $karyawans = Karyawan::all();
        // Generate new user_id (e.g., U004)
        $lastUser = User::orderBy('user_id', 'desc')->first();
        if ($lastUser) {
            $lastNum = (int) substr($lastUser->user_id, 1);
            $newId = 'U' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = 'U001';
        }

        return view('users.create', compact('karyawans', 'newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:user,user_id',
            'username' => 'required|unique:user,username',
            'password' => 'required|min:6',
            'id_karyawan' => 'required'
        ]);

        User::create([
            'user_id' => $request->user_id,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_karyawan' => $request->id_karyawan,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $karyawans = Karyawan::all();
        return view('users.edit', compact('user', 'karyawans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|unique:user,username,'.$id.',user_id',
            'id_karyawan' => 'required'
        ]);

        $user = User::findOrFail($id);
        
        $data = [
            'username' => $request->username,
            'id_karyawan' => $request->id_karyawan,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->user_id == $id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }
}

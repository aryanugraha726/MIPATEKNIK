@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Akun Baru</h1>
            <p class="text-gray-500 text-sm mt-1">Daftarkan pengguna baru ke dalam sistem</p>
        </div>
        <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm">
            &larr; Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc pl-5 text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ID Akun (Otomatis)</label>
                <input type="text" name="user_id" value="{{ $newId }}" class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 focus:border-blue-500 focus:ring-blue-500 cursor-not-allowed" readonly>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                <select name="id_karyawan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>
                            {{ $k->id_karyawan }} - {{ $k->nm_karyawan }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih karyawan yang akan dihubungkan dengan akun ini.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role (Hak Akses)</label>
                <select name="role_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->role_id }}" {{ old('role_id') == $r->role_id ? 'selected' : '' }}>
                            {{ $r->nama_role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr class="border-gray-200">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Username Login</label>
                <input type="text" name="username" value="{{ old('username') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password Default</label>
                <input type="text" name="password" value="password123" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <p class="mt-1 text-xs text-gray-500">Password standar untuk awal pembuatan akun.</p>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

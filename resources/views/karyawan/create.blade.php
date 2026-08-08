@extends('layout.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold border-l-4 border-blue-600 pl-3">Tambah Karyawan</h1>
        <a href="{{ route('karyawan.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded shadow-sm border border-gray-100 p-6">
        <form action="{{ route('karyawan.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Karyawan</label>
                <input type="text" name="nm_karyawan" value="{{ old('nm_karyawan') }}" placeholder="Contoh: Budi Santoso" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-indigo-300" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Divisi (Pilih bisa lebih dari 1 dengan Ctrl/Cmd)</label>
                <select name="id_divisi[]" multiple class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-indigo-300 h-32" required>
                    @foreach($divisis as $d)
                        <option value="{{ $d->id_divisi }}" {{ (is_array(old('id_divisi')) && in_array($d->id_divisi, old('id_divisi'))) ? 'selected' : '' }}>
                            {{ $d->nama_divisi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

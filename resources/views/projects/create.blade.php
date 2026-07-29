@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Tambah Project Baru</h1>
        <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
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
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ID Project (Job ID)</label>
                <input type="number" name="job_id" value="{{ old('job_id', $nextId) }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                <p class="text-xs text-gray-500 mt-1">ID otomatis digenerate, namun dapat Anda ubah manual.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Project</label>
                <input type="text" name="nama_project" value="{{ old('nama_project') }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Prioritas</label>
                <select name="prioritas" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="TINGGI" {{ old('prioritas') == 'TINGGI' ? 'selected' : '' }}>TINGGI</option>
                    <option value="SEDANG" {{ old('prioritas') == 'SEDANG' ? 'selected' : '' }}>SEDANG</option>
                    <option value="RENDAH" {{ old('prioritas') == 'RENDAH' ? 'selected' : '' }}>RENDAH</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_project" value="{{ old('start_project') }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Durasi Pengerjaan (Hari)</label>
                    <div class="flex items-center">
                        <input type="number" name="durasi_hari" min="1" value="{{ old('durasi_hari', 1) }}" class="shadow-sm appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r py-2 px-4 text-gray-600">Hari</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Management PIC</label>
                <select name="management_id" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih PIC Management --</option>
                    @foreach($managements as $m)
                        <option value="{{ $m->management_id }}" {{ old('management_id') == $m->management_id ? 'selected' : '' }}>
                            {{ $m->karyawan->nm_karyawan ?? 'Unknown' }} ({{ $m->divisi->nama_divisi ?? 'Unknown Divisi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

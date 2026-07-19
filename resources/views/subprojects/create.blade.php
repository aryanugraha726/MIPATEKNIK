@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Tambah Subproject</h1>
        <a href="{{ route('projects.show', $project->project_id) }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali ke Project</a>
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
        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded">
            <p class="text-sm text-gray-500">Project Induk:</p>
            <p class="font-bold text-lg">{{ $project->nama_project }}</p>
        </div>

        <form action="{{ route('subprojects.store') }}" method="POST">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->project_id }}">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ID Subproject (Job ID)</label>
                <input type="text" name="subproject_id" value="{{ old('subproject_id', $nextId) }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                <p class="text-xs text-gray-500 mt-1">ID otomatis digenerate (Project ID + 2 digit urutan), namun dapat Anda ubah manual.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Subproject</label>
                <input type="text" name="nama_subproject" value="{{ old('nama_subproject') }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_subproject" value="{{ old('start_subproject') }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Target Selesai</label>
                    <input type="date" name="target_subproject" value="{{ old('target_subproject') }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan (Pelaksana Utama)</label>
                <select name="id_karyawan" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>
                            {{ $k->nm_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Management (Pengawas)</label>
                <select name="management_id" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Pengawas --</option>
                    @foreach($managements as $m)
                        <option value="{{ $m->management_id }}" {{ old('management_id') == $m->management_id ? 'selected' : '' }}>
                            {{ $m->karyawan->nm_karyawan ?? 'Unknown' }} ({{ $m->divisi->nama_divisi ?? 'Unknown Divisi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Subproject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

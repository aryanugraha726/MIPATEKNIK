@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Edit Subproject #{{ $subproject->subproject_id }}</h1>
        <a href="{{ route('projects.show', $subproject->job_id) }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali ke Project</a>
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

        <form action="{{ route('subprojects.update', $subproject->subproject_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Subproject</label>
                <input type="text" name="nama_subproject" value="{{ old('nama_subproject', $subproject->nama_subproject) }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_subproject" value="{{ old('start_subproject', $subproject->start_subproject) }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div>
                    @php $durasi = \Carbon\Carbon::parse($subproject->start_subproject)->diffInDays(\Carbon\Carbon::parse($subproject->target_subproject)); @endphp
                    <label class="block text-gray-700 text-sm font-bold mb-2">Durasi Pengerjaan (Hari)</label>
                    <div class="flex items-center">
                        <input type="number" name="durasi_hari" min="1" value="{{ old('durasi_hari', $durasi) }}" class="shadow-sm appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r py-2 px-4 text-gray-600">Hari</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Operator</label>
                <select name="id_karyawan" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Operator --</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan', $subproject->id_karyawan) == $k->id_karyawan ? 'selected' : '' }}>
                            {{ $k->nm_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Penanggung Jawab</label>
                <select name="management_id" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Penanggung Jawab --</option>
                    @foreach($managements as $m)
                        <option value="{{ $m->management_id }}" {{ old('management_id', $subproject->management_id) == $m->management_id ? 'selected' : '' }}>
                            {{ $m->karyawan->nm_karyawan ?? 'Unknown' }} ({{ $m->divisi->nama_divisi ?? 'Unknown Divisi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Subproject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

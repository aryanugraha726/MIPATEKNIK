@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold flex items-center">
                <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-gray-900 mr-2">&larr;</a>
                Project: {{ $project->nama_project }}
            </h1>
        </div>
        <div>
            <a href="{{ route('ppic.gantt', $project->project_id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded font-bold shadow-sm mr-2">Gantt Chart</a>
            <a href="{{ route('projects.edit', $project->project_id) }}" class="bg-gray-100 border border-gray-300 text-gray-800 hover:bg-gray-200 py-2 px-4 rounded font-bold shadow-sm mr-2">Edit Project</a>
        </div>
    </div>

    <!-- Project Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">ID Project</p>
                <p class="font-bold text-lg">#{{ $project->project_id }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Prioritas</p>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $project->prioritas == 'TINGGI' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                    {{ $project->prioritas }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Jadwal</p>
                <p class="font-medium text-gray-800">
                    {{ date('d M Y', strtotime($project->start_project)) }} <span class="text-gray-400 mx-2">s/d</span> {{ date('d M Y', strtotime($project->target_project)) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Management PIC</p>
                <p class="font-medium text-gray-800">
                    {{ $project->management->karyawan->nm_karyawan ?? 'N/A' }} 
                    <span class="text-gray-400 text-sm">({{ $project->management->divisi->nama_divisi ?? '-' }})</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Subprojects Section -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Subproject</h2>
        <a href="{{ route('subprojects.create', ['project_id' => $project->project_id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow text-sm">
            + Tambah Subproject
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm rounded text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Subproject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC Karyawan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($project->subprojects as $sub)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $sub->subproject_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $sub->nama_subproject }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ date('d M Y', strtotime($sub->start_subproject)) }} - {{ date('d M Y', strtotime($sub->target_subproject)) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $sub->karyawan->nm_karyawan ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('subprojects.show', $sub->subproject_id) }}" class="text-indigo-600 hover:text-indigo-900 mx-1 border border-indigo-200 px-2 py-1 rounded bg-indigo-50">Detail / Tugas</a>
                        <a href="{{ route('subprojects.edit', $sub->subproject_id) }}" class="text-blue-600 hover:text-blue-900 mx-1">Edit</a>
                        <form action="{{ route('subprojects.destroy', $sub->subproject_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus subproject ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Belum ada subproject untuk project ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

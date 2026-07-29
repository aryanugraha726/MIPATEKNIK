@extends('layout.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Project</h1>
        <a href="{{ route('projects.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">Tambah Project</a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Management PIC</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $p->job_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $p->nama_project }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $p->prioritas == 'TINGGI' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $p->prioritas }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ date('d M Y', strtotime($p->start_project)) }} - {{ date('d M Y', strtotime($p->target_project)) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $p->management->karyawan->nm_karyawan ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('projects.show', $p->job_id) }}" class="text-indigo-600 hover:text-indigo-900 mx-1 border border-indigo-200 px-2 py-1 rounded bg-indigo-50">Detail</a>
                        <a href="{{ route('ppic.gantt', $p->job_id) }}" class="text-yellow-600 hover:text-yellow-900 mx-1 border border-yellow-200 px-2 py-1 rounded bg-yellow-50">Gantt</a>
                        <a href="{{ route('projects.edit', $p->job_id) }}" class="text-blue-600 hover:text-blue-900 mx-1">Edit</a>
                        <form action="{{ route('projects.destroy', $p->job_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data project</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

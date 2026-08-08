@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold flex items-center">
                <a href="{{ route('projects.show', $subproject->job_id) }}" class="text-gray-500 hover:text-gray-900 mr-2">&larr;</a>
                Subproject: {{ $subproject->nama_subproject }}
            </h1>
        </div>
        <div>
            <a href="{{ route('subprojects.edit', $subproject->subproject_id) }}" class="bg-gray-100 border border-gray-300 text-gray-800 hover:bg-gray-200 py-2 px-4 rounded font-bold shadow-sm mr-2">Edit Subproject</a>
        </div>
    </div>

    <!-- Subproject Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Project Induk</p>
                <p class="font-bold text-indigo-700">
                    <a href="{{ route('projects.show', $subproject->job_id) }}">{{ $subproject->project->nama_project }}</a>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Operator</p>
                <p class="font-medium text-gray-800">{{ $subproject->karyawan->nm_karyawan ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Jadwal</p>
                <p class="font-medium text-gray-800">
                    {{ date('d M Y', strtotime($subproject->start_subproject)) }} <span class="text-gray-400 mx-2">s/d</span> {{ date('d M Y', strtotime($subproject->target_subproject)) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Penanggung Jawab</p>
                <p class="font-medium text-gray-800">
                    {{ $subproject->management->karyawan->nm_karyawan ?? 'N/A' }} 
                </p>
            </div>
        </div>
    </div>

    <!-- Tugas Section -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Rincian Tugas (Opsional)</h2>
    </div>
    <p class="text-sm text-gray-500 mb-4">Jika subproject ini tidak memiliki pecahan tugas, Anda tidak perlu menambahkan apa-apa di bawah ini.</p>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm rounded text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-sm rounded text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tugas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelaksana (Karyawan/Vendor)</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subproject->tugas as $t)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $t->tugas_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $t->tugas }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ date('d M Y', strtotime($t->start_tugas)) }} - {{ date('d M Y', strtotime($t->target_tugas)) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        @php
                            $diff = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($t->target_tugas)->startOfDay(), false);
                            $color = $diff < 0 ? 'text-red-600 font-bold' : ($diff <= 7 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold');
                            $text = $diff < 0 ? 'Terlambat ' . abs(intval($diff)) . ' hari' : ($diff == 0 ? 'Hari Ini' : intval($diff) . ' Hari');
                        @endphp
                        <span class="{{ $color }}">{{ $text }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        @if($t->id_karyawan)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Internal: {{ $t->karyawan->nm_karyawan }}</span>
                        @elseif($t->id_vendor)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Vendor: {{ $t->vendor->nama_vendor }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <form action="{{ route('tugas.toggle-status', $t->tugas_id) }}" method="POST" class="inline">
                            @csrf
                            <input type="checkbox" onchange="this.form.submit()" {{ $t->is_completed ? 'checked' : '' }} class="w-5 h-5 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 focus:ring-2 cursor-pointer transition">
                            <span class="block text-xs mt-1 font-semibold {{ $t->is_completed ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $t->is_completed ? 'Selesai' : 'To Do' }}
                            </span>
                            @if($t->is_completed && $t->tanggal_selesai)
                            <span class="block text-xs mt-1 text-gray-500 italic">
                                {{ date('d M Y', strtotime($t->tanggal_selesai)) }}
                            </span>
                            @endif
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('tugas.edit', $t->tugas_id) }}" class="text-blue-600 hover:text-blue-900 mx-1">Edit</a>
                        <form action="{{ route('tugas.destroy', $t->tugas_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 italic">Subproject ini belum / tidak memiliki rincian tugas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold border-l-4 border-blue-600 pl-3">Master Karyawan</h1>
        <a href="{{ route('karyawan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm">
            + Tambah Karyawan
        </a>
    </div>

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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($karyawans as $k)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">{{ $k->id_karyawan }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800 font-bold">{{ $k->nm_karyawan }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $k->divisi->pluck('nama_divisi')->join(', ') ?: '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('karyawan.edit', $k->id_karyawan) }}" class="text-indigo-600 hover:text-indigo-900 mx-2">Edit</a>
                        <form action="{{ route('karyawan.destroy', $k->id_karyawan) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus Karyawan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 mx-2">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data karyawan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

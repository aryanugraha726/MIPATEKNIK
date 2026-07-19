@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline">✅ {{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline">❌ {{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
        
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <div class="flex items-center space-x-3 mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-800">Master Barang</h1>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('barang.create') }}" class="bg-blue-600 text-white font-bold py-2 px-4 rounded shadow hover:bg-blue-700 hover:shadow-lg transition duration-200">
                    + Tambah Barang
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-sm">ID Barang</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Nama Barang</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Kategori</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Satuan</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Harga (Rp)</th>
                        <th class="py-3 px-4 text-center font-semibold text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse($barangs as $barang)
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="py-3 px-4 text-sm font-medium">{{ $barang->id_barang }}</td>
                            <td class="py-3 px-4 text-sm">{{ $barang->nama_barang }}</td>
                            <td class="py-3 px-4 text-sm">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm">{{ $barang->satuan->nama_satuan ?? '-' }}</td>
                            <td class="py-3 px-4 text-right text-sm font-medium text-blue-700">{{ number_format($barang->harga, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('barang.edit', $barang->id_barang) }}" class="text-indigo-600 hover:text-indigo-900 mx-1" title="Edit">
                                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 mx-1" title="Hapus">
                                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 italic">Belum ada data barang yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

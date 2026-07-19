@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
        
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Barang Masuk</h1>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-blue-50 border-b border-blue-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-sm">ID Masuk</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Tanggal</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Barang</th>
                        <th class="py-3 px-4 text-center font-semibold text-sm">Jumlah</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($masuk as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">IN-{{ $item->id_masuk }}</td>
                            <td class="py-3 px-4 text-sm">{{ date('d M Y', strtotime($item->tgl_masuk)) }}</td>
                            <td class="py-3 px-4 text-sm">
                                <span class="font-bold text-blue-600">{{ $item->id_barang }}</span><br>
                                {{ $item->barang->nama_barang ?? 'Barang tidak ditemukan' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-green-100 text-green-800 font-bold px-3 py-1 rounded-full text-xs">
                                    +{{ $item->jml_masuk }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $item->ket_masuk ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 italic">Belum ada riwayat barang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
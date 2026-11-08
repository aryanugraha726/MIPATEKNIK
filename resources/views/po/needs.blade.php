@extends('layout.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kebutuhan Pembelian</h1>
        <p class="text-sm text-gray-500">Daftar rekapitulasi barang yang menunggu dibeli dari seluruh Material Request.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-sm">
                <th class="p-4 font-semibold text-gray-600">ID Barang</th>
                <th class="p-4 font-semibold text-gray-600">Nama Barang</th>
                <th class="p-4 font-semibold text-gray-600 text-center">Total Dibutuhkan</th>
                <th class="p-4 font-semibold text-gray-600">Sumber Kebutuhan (MR)</th>
                <th class="p-4 font-semibold text-gray-600 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
            @forelse($needs as $item)
            <tr class="hover:bg-gray-50">
                <td class="p-4 font-medium text-gray-900">{{ $item['id_barang'] }}</td>
                <td class="p-4 text-gray-600">{{ $item['nama_barang'] }}</td>
                <td class="p-4 text-center">
                    <span class="font-bold text-red-600 text-lg">{{ $item['total_kebutuhan'] }}</span>
                    <span class="text-gray-500 text-xs ml-1">{{ $item['satuan'] }}</span>
                </td>
                <td class="p-4 text-gray-600">
                    <ul class="list-disc pl-4 text-xs space-y-1">
                        @foreach($item['sumber_mr'] as $mr)
                            <li>MR #{{ $mr['no_nota'] }} <span class="text-gray-400">({{ $mr['qty'] }} {{ $item['satuan'] }})</span></li>
                        @endforeach
                    </ul>
                </td>
                <td class="p-4 text-center">
                    <a href="{{ route('po.create', ['id_barang' => $item['id_barang'], 'qty' => $item['total_kebutuhan']]) }}" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat PO
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <p class="font-medium text-gray-600">Tidak ada kebutuhan pembelian saat ini.</p>
                    <p class="text-sm mt-1">Seluruh pesanan material request telah terpenuhi stoknya.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

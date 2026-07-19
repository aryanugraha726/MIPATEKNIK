@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Notifikasi Sukses Transaksi -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Kotak Utama Pembungkus Tabel -->
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
        
        <!-- Header Tabel & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <div class="flex items-center space-x-3 mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-800">Stock Opname</h1>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                    Sistem MIPA TEKNIK
                </span>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('stock.export') }}" class="bg-blue-600 text-white font-bold py-2 px-4 rounded shadow hover:bg-blue-700 hover:shadow-lg transition duration-200">
                    Ekspor XLSX
                </a>
                <a href="{{ route('transaksi.create') }}" class="bg-green-600 text-white font-bold py-2 px-4 rounded shadow hover:bg-green-700 hover:shadow-lg transition duration-200">
                    + Input Transaksi Baru
                </a>
            </div>
        </div>

        <!-- Tabel Data Stok -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-sm">ID Barang</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Nama Barang</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Harga Satuan</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Stok Awal</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Satuan</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Masuk</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Keluar</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm text-yellow-300">Sisa Stok</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Total Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="py-3 px-4 text-sm font-medium">{{ $stock->id_barang }}</td>
                            <td class="py-3 px-4 text-sm">{{ $stock->nama_barang }}</td>
                            <td class="py-3 px-4 text-right text-sm">{{ number_format($stock->barang->harga ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-sm">{{ $stock->stock_awal }}</td>
                            <td class="py-3 px-4 text-right text-sm">{{ $stock->satuan->nama_satuan ?? '-' }}</td>
                            <td class="py-3 px-4 text-right text-sm font-semibold text-green-600">+{{ $stock->stock_masuk }}</td>
                            <td class="py-3 px-4 text-right text-sm font-semibold text-red-600">-{{ $stock->stock_keluar }}</td>
                            <td class="py-3 px-4 text-right font-bold text-blue-700 bg-blue-50/50">{{ $stock->sisa_stock }}</td>
                            <td class="py-3 px-4 text-right text-sm font-medium">{{ number_format($stock->jumlah_nilai, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 italic">Belum ada data stok yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-300">
                    <tr>
                        <td colspan="8" class="py-3 px-4 text-right text-gray-800">Total Keseluruhan Nilai Aset:</td>
                        <td class="py-3 px-4 text-right text-blue-700">Rp {{ number_format($stocks->sum('jumlah_nilai'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>
@endsection
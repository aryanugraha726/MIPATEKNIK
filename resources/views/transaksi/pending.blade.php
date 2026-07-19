@extends('layout.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Stok Antrean (FIFO)</h1>
        <p class="text-gray-500 text-sm mt-1">Barang dengan harga baru yang sedang menunggu stok aktif (lama) habis di gudang.</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
        
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-sm">ID Masuk</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Tanggal</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Nama Barang</th>
                        <th class="py-3 px-4 text-center font-semibold text-sm">Jumlah Antrean</th>
                        <th class="py-3 px-4 text-right font-semibold text-sm">Harga Baru (Rp)</th>
                        <th class="py-3 px-4 text-center font-semibold text-sm">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse($pending as $item)
                        <tr class="hover:bg-yellow-50 transition duration-150">
                            <td class="py-3 px-4 text-sm font-medium">#{{ $item->id_masuk }}</td>
                            <td class="py-3 px-4 text-sm">{{ date('d M Y', strtotime($item->tgl_masuk)) }}</td>
                            <td class="py-3 px-4 text-sm font-semibold">{{ $item->barang->nama_barang ?? $item->id_barang }}</td>
                            <td class="py-3 px-4 text-center text-sm font-bold text-blue-600">+{{ $item->jml_masuk }}</td>
                            <td class="py-3 px-4 text-right text-sm font-medium text-yellow-700">{{ number_format($item->harga_masuk, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-yellow-300">
                                    MENUNGGU STOK LAMA HABIS
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 italic">
                                Tidak ada barang dalam antrean. Semua stok sudah sinkron.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 p-4 bg-blue-50 rounded border border-blue-100 text-sm text-blue-800">
            <strong>Info Sistem Cerdas:</strong> Anda tidak perlu melakukan aktivasi manual. Sistem akan secara otomatis mengaktifkan antrean teratas ketika transaksi <b>Barang Keluar</b> menyebabkan sisa stok aktif menyentuh angka 0.
        </div>

    </div>
</div>
@endsection

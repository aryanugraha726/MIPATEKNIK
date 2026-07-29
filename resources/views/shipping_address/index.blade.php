@extends('layout.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Master Alamat Pengiriman (Shipping Address)</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola daftar lokasi pengiriman untuk Purchase Order</p>
    </div>
    <a href="{{ route('shipping-address.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
        + Tambah Lokasi
    </a>
</div>

@if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm">
        <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
        <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                    <th class="p-4 font-medium">ID Lokasi</th>
                    <th class="p-4 font-medium">Nama Lokasi</th>
                    <th class="p-4 font-medium">Alamat</th>
                    <th class="p-4 font-medium">Kontak Person</th>
                    <th class="p-4 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                @forelse ($shippingAddresses as $address)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-medium text-gray-900">{{ $address->id_lokasi }}</td>
                        <td class="p-4 font-semibold text-blue-600">{{ $address->nama_lokasi }}</td>
                        <td class="p-4 max-w-xs truncate" title="{{ $address->alamat_mipa }}">{{ $address->alamat_mipa }}</td>
                        <td class="p-4">
                            <div>{{ $address->cp_mipa ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $address->phone_mipa ?? '-' }}</div>
                        </td>
                        <td class="p-4">
                            <div class="flex gap-3">
                                <a href="{{ route('shipping-address.edit', $address->id_lokasi) }}" class="text-blue-500 hover:text-blue-700 font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('shipping-address.destroy', $address->id_lokasi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi pengiriman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Belum ada data alamat pengiriman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

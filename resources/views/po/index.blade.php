@extends('layout.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Purchase Order (PO)</h1>
        <p class="text-gray-500 text-sm mt-1">Daftar semua Purchase Order</p>
    </div>
    @if(in_array('PURCHASING', Auth::user()->roles()) || in_array('ADMIN', Auth::user()->roles()))
    <a href="{{ route('po.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
        + Buat PO Baru
    </a>
    @endif
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
                    <th class="p-4 font-medium">No. PO</th>
                    <th class="p-4 font-medium">Tanggal</th>
                    <th class="p-4 font-medium">Vendor</th>
                    <th class="p-4 font-medium">Total Nilai</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                @forelse ($pos as $po)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-gray-900">{{ $po->no_po }}</td>
                        <td class="p-4">{{ \Carbon\Carbon::parse($po->tgl_po)->format('d M Y') }}</td>
                        <td class="p-4 font-medium text-blue-600">{{ $po->vendor->nama_vendor ?? '-' }}</td>
                        <td class="p-4 font-semibold text-emerald-600">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                        <td class="p-4">
                            @if($po->status === 'DRAFT')
                                <span class="bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs font-semibold">DRAFT</span>
                            @elseif($po->status === 'PENDING_APPROVAL')
                                <span class="bg-yellow-100 text-yellow-700 py-1 px-3 rounded-full text-xs font-semibold">MENUNGGU PERSETUJUAN</span>
                            @elseif($po->status === 'APPROVED')
                                <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">DISETUJUI</span>
                            @elseif($po->status === 'REJECTED')
                                <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">DITOLAK</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex gap-3">
                                <a href="{{ route('po.show', $po->no_po) }}" class="text-blue-500 hover:text-blue-700 font-medium">
                                    Detail
                                </a>
                                @if(in_array('ADMIN', Auth::user()->roles()) || in_array('PURCHASING', Auth::user()->roles()))
                                    @if($po->status !== 'APPROVED')
                                    <form action="{{ route('po.destroy', $po->no_po) }}" method="POST" onsubmit="return confirm('Hapus PO ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            Belum ada dokumen Purchase Order.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

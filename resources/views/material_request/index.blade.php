@extends('layout.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Nota Permintaan Barang</h1>
        <p class="text-sm text-gray-500">Daftar riwayat permintaan barang Anda</p>
    </div>
    <a href="{{ route('material-requests.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Buat Nota Permintaan
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-sm">
                <th class="p-4 font-semibold text-gray-600">No. Nota</th>
                <th class="p-4 font-semibold text-gray-600">Tanggal</th>
                <th class="p-4 font-semibold text-gray-600">Kategori</th>
                <th class="p-4 font-semibold text-gray-600">Job ID (Project)</th>
                <th class="p-4 font-semibold text-gray-600">Status</th>
                <th class="p-4 font-semibold text-gray-600">Detail Items</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50">
                <td class="p-4 font-medium text-gray-900">#{{ $req->no_nota }}</td>
                <td class="p-4 text-gray-600">{{ $req->tanggal }}</td>
                <td class="p-4 text-gray-600">
                    <span class="px-2 py-1 rounded bg-gray-200 text-xs font-semibold">{{ $req->kategori }}</span>
                </td>
                <td class="p-4 text-gray-600">{{ $req->job_id ?? '-' }}</td>
                <td class="p-4">
                    @if($req->status == 'PENDING_MANAGER')
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-semibold">Pending Approval</span>
                    @elseif($req->status == 'APPROVED_MANAGER')
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">Approved (Proses PO)</span>
                    @elseif($req->status == 'REJECTED_MANAGER')
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-semibold">Rejected</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full font-semibold">{{ $req->status }}</span>
                    @endif
                </td>
                <td class="p-4 text-gray-600">
                    <ul class="list-disc pl-4">
                        @foreach($req->details as $d)
                            <li>
                                {{ $d->id_barang ? $d->barang->nama_barang : $d->nama_barang_baru . ' (Baru)' }} 
                                <span class="font-semibold ml-1">({{ $d->req_qty }} {{ $d->id_barang ? $d->barang->satuan->nama_satuan : $d->satuan_baru }})</span>
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">Belum ada nota permintaan barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

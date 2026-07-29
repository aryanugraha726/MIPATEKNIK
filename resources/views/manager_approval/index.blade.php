@extends('layout.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Persetujuan Manager (Approval)</h1>
        <p class="text-sm text-gray-500">Daftar permintaan barang untuk project di bawah pengawasan Anda</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-sm">
                <th class="p-4 font-semibold text-gray-600">Nota & Tgl</th>
                <th class="p-4 font-semibold text-gray-600">Job ID (Project)</th>
                <th class="p-4 font-semibold text-gray-600">Peminta</th>
                <th class="p-4 font-semibold text-gray-600">Detail Items</th>
                <th class="p-4 font-semibold text-gray-600 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50">
                <td class="p-4 font-medium text-gray-900">
                    <div class="font-bold text-blue-600">#{{ $req->no_nota }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $req->tanggal }}</div>
                </td>
                <td class="p-4 font-medium text-gray-800">{{ $req->job_id }}</td>
                <td class="p-4 text-gray-600">{{ $req->karyawan->nm_karyawan ?? '-' }}</td>
                <td class="p-4 text-gray-600">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($req->details as $d)
                            <li>
                                {{ $d->id_barang ? $d->barang->nama_barang : $d->nama_barang_baru . ' (Baru)' }} 
                                <span class="font-semibold ml-1">({{ $d->req_qty }} {{ $d->id_barang ? $d->barang->satuan->nama_satuan : $d->satuan_baru }})</span>
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="p-4 text-right">
                    @if($req->status == 'PENDING_MANAGER')
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('manager_approval.approve', $req->no_nota) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded shadow-sm font-medium">Approve</button>
                            </form>
                            <form action="{{ route('manager_approval.reject', $req->no_nota) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded shadow-sm font-medium">Reject</button>
                            </form>
                        </div>
                    @else
                        <span class="text-gray-500 italic text-xs">{{ $req->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">Belum ada nota permintaan yang perlu disetujui.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

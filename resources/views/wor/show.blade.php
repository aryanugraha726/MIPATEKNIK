@extends('layout.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Detail Work Order Release #{{ $wor->job_id }}</h1>
    <a href="{{ route('wor.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold border-b pb-2 mb-4">Informasi Utama</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><span class="text-gray-500 font-medium text-sm">Job ID:</span> <p class="text-gray-900">{{ $wor->job_id }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">Customer:</span> <p class="text-gray-900">{{ $wor->customer ?: '-' }}</p></div>
        <div class="md:col-span-2"><span class="text-gray-500 font-medium text-sm">Job Description:</span> <p class="text-gray-900">{{ $wor->jobdesc ?: '-' }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">PO Proyek:</span> <p class="text-gray-900">{{ $wor->po_proyek ?: '-' }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">Sales:</span> <p class="text-gray-900">{{ $wor->sales ?: '-' }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">Tanggal Order:</span> <p class="text-gray-900">{{ $wor->tgl_order ?: '-' }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">Jadwal Kirim:</span> <p class="text-gray-900">{{ $wor->jadwal_kirim ?: '-' }}</p></div>
        <div class="md:col-span-2"><span class="text-gray-500 font-medium text-sm">Alamat Kirim:</span> <p class="text-gray-900">{{ $wor->alamat_kirim ?: '-' }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">CP Customer:</span> <p class="text-gray-900">{{ $wor->cp_customer ?: '-' }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">Mail Address:</span> <p class="text-gray-900">{{ $wor->mail_address ?: '-' }}</p></div>
        <div><span class="text-gray-500 font-medium text-sm">Estimasi Jam Kerja:</span> <p class="text-gray-900">{{ $wor->estimate_man_hour ?: '-' }}</p></div>
        
        <div class="md:col-span-2 border-t pt-4 mt-2">
            <h3 class="font-semibold mb-2">Equipment Detail & Prioritas</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div><span class="text-gray-500 font-medium text-sm">Material:</span> <p class="text-gray-900">{{ $wor->material ?: '-' }}</p></div>
                <div><span class="text-gray-500 font-medium text-sm">Model:</span> <p class="text-gray-900">{{ $wor->model ?: '-' }}</p></div>
                <div><span class="text-gray-500 font-medium text-sm">Power:</span> <p class="text-gray-900">{{ $wor->power ?: '-' }}</p></div>
                <div><span class="text-gray-500 font-medium text-sm">Equip Qty:</span> <p class="text-gray-900">{{ $wor->equip_qty ?: '-' }}</p></div>
                <div><span class="text-gray-500 font-medium text-sm">Prioritas:</span> <p class="text-gray-900">{{ $wor->priority ?: '-' }}</p></div>
                <div><span class="text-gray-500 font-medium text-sm">Jml Hari Kerja:</span> <p class="text-gray-900">{{ $wor->priority_days ? $wor->priority_days . ' hari' : '-' }}</p></div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-semibold border-b pb-2 mb-4">Status Persetujuan</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <span class="text-gray-500 font-medium text-sm">Status:</span> 
            <p class="mt-1">
                @if($wor->status == 'Approved')
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                @elseif($wor->status == 'Rejected')
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                @else
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                @endif
            </p>
        </div>
        <div>
            <span class="text-gray-500 font-medium text-sm">Disetujui/Ditolak Oleh:</span>
            <p class="text-gray-900 font-medium mt-1">{{ $wor->approver ? $wor->approver->username : '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500 font-medium text-sm">Tanggal Keputusan:</span>
            <p class="text-gray-900 mt-1">{{ $wor->approved_at ? \Carbon\Carbon::parse($wor->approved_at)->format('d M Y, H:i') : '-' }}</p>
        </div>
        @if($wor->status == 'Rejected')
        <div class="md:col-span-3 mt-2 p-3 bg-red-50 border border-red-200 rounded text-red-700">
            <span class="font-bold">Alasan Penolakan:</span>
            <p class="mt-1">{{ $wor->reject_reason }}</p>
        </div>
        @endif
    </div>
    
    @if(in_array('DIREKTUR UTAMA', auth()->user()->roles()) && $wor->status == 'Pending')
    <div class="mt-6 pt-4 border-t flex space-x-3">
        <form action="{{ route('wor.approve', $wor->job_id) }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded" onclick="return confirm('Setujui Work Order ini?');">Approve WOR</button>
        </form>
        <button type="button" onclick="rejectWorShow('{{ $wor->job_id }}')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">Reject WOR</button>
    </div>
    @endif
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b">
        <h2 class="text-lg font-semibold">Work Scope</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Part Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($wor->details as $detail)
            <tr>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->desc_part ?: '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->part_qty ?: 0 }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $detail->satuan->nama_satuan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada detail item.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Form tersembunyi untuk reject -->
<form id="reject-form-show" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="reject_reason" id="reject_reason_input_show">
</form>

<script>
    function rejectWorShow(jobId) {
        let reason = prompt("Masukkan alasan penolakan:");
        if (reason != null && reason.trim() !== "") {
            let form = document.getElementById('reject-form-show');
            form.action = `/wor/${jobId}/reject`;
            document.getElementById('reject_reason_input_show').value = reason;
            form.submit();
        } else if (reason != null) {
            alert("Alasan penolakan tidak boleh kosong!");
        }
    }
</script>
@endsection

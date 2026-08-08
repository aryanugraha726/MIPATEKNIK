@extends('layout.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Daftar Work Order Release</h1>
    <a href="{{ route('wor.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition duration-150">
        <span class="mr-2">+</span> Tambah WOR
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Kirim</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($wor as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->job_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->customer }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tgl_order }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->jadwal_kirim }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $item->details->count() }} items
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($item->status == 'Approved')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($item->status == 'Rejected')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <a href="{{ route('wor.show', $item->job_id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                    @if(in_array('MARKETING', auth()->user()->roles()) || in_array('ADMIN', auth()->user()->roles()))
                        <a href="{{ route('wor.edit', $item->job_id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <form action="{{ route('wor.destroy', $item->job_id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Work Order ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 mr-3">Hapus</button>
                        </form>
                    @endif
                    
                    @if(in_array('DIREKTUR UTAMA', auth()->user()->roles()) && $item->status == 'Pending')
                        <form action="{{ route('wor.approve', $item->job_id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3" onclick="return confirm('Setujui Work Order ini?');">Approve</button>
                        </form>
                        <button type="button" onclick="rejectWor('{{ $item->job_id }}')" class="text-red-600 hover:text-red-900">Reject</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data Work Order Release.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Form tersembunyi untuk reject -->
<form id="reject-form" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="reject_reason" id="reject_reason_input">
</form>

<script>
    function rejectWor(jobId) {
        let reason = prompt("Masukkan alasan penolakan:");
        if (reason != null && reason.trim() !== "") {
            let form = document.getElementById('reject-form');
            form.action = `/wor/${jobId}/reject`;
            document.getElementById('reject_reason_input').value = reason;
            form.submit();
        } else if (reason != null) {
            alert("Alasan penolakan tidak boleh kosong!");
        }
    }
</script>
@endsection

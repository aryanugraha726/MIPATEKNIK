@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
        
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Barang Keluar</h1>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('transaksi.keluar') }}" class="mb-6 flex flex-wrap gap-4 items-end">
            <div>
                <label for="job_id" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select name="job_id" id="job_id" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->job_id }}" {{ request('job_id') == $project->job_id ? 'selected' : '' }}>
                            {{ $project->job_id }} - {{ $project->nama_project }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang" value="{{ request('nama_barang') }}" placeholder="Cari nama barang..." class="border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>

            <div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                    Filter
                </button>
                @if(request('job_id') || request('nama_barang'))
                    <a href="{{ route('transaksi.keluar') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-red-50 border-b border-red-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-sm">ID Keluar</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Tanggal</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Barang</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Project</th>
                        <th class="py-3 px-4 text-center font-semibold text-sm">Jumlah</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($keluar as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">OUT-{{ $item->id_keluar }}</td>
                            <td class="py-3 px-4 text-sm">{{ date('d M Y', strtotime($item->tgl_keluar)) }}</td>
                            <td class="py-3 px-4 text-sm">
                                <span class="font-bold text-red-600">{{ $item->id_barang }}</span><br>
                                {{ $item->barang->nama_barang ?? 'Barang tidak ditemukan' }}
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <span class="font-bold text-gray-700">{{ $item->job_id }}</span><br>
                                {{ $item->project->nama_project ?? 'Nama project tidak ditemukan' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-red-100 text-red-800 font-bold px-3 py-1 rounded-full text-xs">
                                    -{{ $item->jumlah_keluar }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $item->ket_keluar ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 italic">Belum ada riwayat barang keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
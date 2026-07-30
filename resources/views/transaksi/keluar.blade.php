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
                        <th class="py-3 px-4 text-left font-semibold text-sm">Barang</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Tanggal</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Project</th>
                        <th class="py-3 px-4 text-center font-semibold text-sm">Jumlah</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Satuan</th>
                        <th class="py-3 px-4 text-left font-semibold text-sm">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($keluar as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">OUT-{{ $item->id_keluar }}</td>
                            <td class="py-3 px-4 text-sm">
                                <button onclick="showBarangDetail('{{ $item->id_barang }}')" class="font-bold text-red-600 hover:text-red-800 underline focus:outline-none transition">{{ $item->id_barang }}</button><br>
                                {{ $item->barang->nama_barang ?? 'Barang tidak ditemukan' }}
                            </td>
                            <td class="py-3 px-4 text-sm">{{ date('d M Y', strtotime($item->tgl_keluar)) }}</td>
                            <td class="py-3 px-4 text-sm">
                                <button onclick="showProjectDetail('{{ $item->job_id }}')" class="font-bold text-gray-700 hover:text-gray-900 underline focus:outline-none transition">{{ $item->job_id }}</button><br>
                                {{ $item->project->nama_project ?? 'Nama project tidak ditemukan' }}
                            </td>
                            <td class="py-3 px-4 text-center text-sm font-bold text-red-600">
                                -{{ $item->jumlah_keluar }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $item->barang->satuan->nama_satuan ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $item->ket_keluar ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 italic">Belum ada riwayat barang keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2" id="modal-title">
                            Detail Riwayat
                        </h3>
                        <div class="mt-4" id="modal-content">
                            <!-- Content injected via JS -->
                            <div class="text-center py-4">
                                <svg class="animate-spin h-8 w-8 text-red-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Memuat data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150 ease-in-out">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('modal-content').innerHTML = `
            <div class="text-center py-4">
                <svg class="animate-spin h-8 w-8 text-red-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Memuat data...</p>
            </div>
        `;
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    function showBarangDetail(id_barang) {
        openModal();
        document.getElementById('modal-title').innerText = `Detail Pengeluaran Barang: ${id_barang}`;
        
        // Menggunakan absolute URL dari root laravel untuk mencegah masalah subfolder
        const baseUrl = window.location.origin + window.location.pathname.replace('/riwayat/keluar', '');
        
        fetch(`${baseUrl}/riwayat/keluar/barang/${id_barang}`)
            .then(response => response.json())
            .then(data => {
                let html = `
                    <div class="mb-4 bg-red-50 p-3 rounded text-sm text-gray-800">
                        <strong>Nama Barang:</strong> ${data.barang.nama_barang}
                    </div>
                `;

                if (data.summary.length === 0) {
                    html += `<p class="text-gray-500 italic text-sm text-center py-4">Belum ada riwayat pengeluaran ke project mana pun.</p>`;
                } else {
                    html += `<div class="space-y-4">`;
                    data.summary.forEach(proj => {
                        html += `
                            <div class="border border-gray-200 rounded p-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-gray-800">${proj.job_id} - ${proj.nama_project}</h4>
                                    <span class="bg-red-100 text-red-800 font-bold px-2 py-1 rounded text-xs">Total: ${proj.total_keluar}</span>
                                </div>
                                <ul class="text-sm text-gray-600 divide-y divide-gray-100 mt-2">`;
                        
                        proj.rincian.forEach(r => {
                            html += `<li class="py-1 flex justify-between">
                                        <span>${r.tanggal} <span class="text-gray-400">(${r.keterangan})</span></span>
                                        <span class="font-medium">-${r.jumlah}</span>
                                    </li>`;
                        });

                        html += `</ul></div>`;
                    });
                    html += `</div>`;
                }
                
                document.getElementById('modal-content').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('modal-content').innerHTML = '<p class="text-red-500 py-4">Gagal memuat data.</p>';
            });
    }

    function showProjectDetail(job_id) {
        openModal();
        document.getElementById('modal-title').innerText = `Detail Pengambilan Project: ${job_id}`;
        
        // Menggunakan absolute URL dari root laravel
        const baseUrl = window.location.origin + window.location.pathname.replace('/riwayat/keluar', '');

        fetch(`${baseUrl}/riwayat/keluar/project/${job_id}`)
            .then(response => response.json())
            .then(data => {
                let html = `
                    <div class="mb-4 bg-gray-100 p-3 rounded text-sm text-gray-800">
                        <strong>Nama Project:</strong> ${data.project.nama_project}
                    </div>
                `;

                if (data.summary.length === 0) {
                    html += `<p class="text-gray-500 italic text-sm text-center py-4">Project ini belum mengambil barang apapun.</p>`;
                } else {
                    html += `<div class="space-y-4">`;
                    data.summary.forEach(brg => {
                        html += `
                            <div class="border border-gray-200 rounded p-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-red-600">${brg.id_barang} <span class="text-gray-700 font-normal">- ${brg.nama_barang}</span></h4>
                                    <span class="bg-gray-200 text-gray-800 font-bold px-2 py-1 rounded text-xs">Total: ${brg.total_keluar}</span>
                                </div>
                                <ul class="text-sm text-gray-600 divide-y divide-gray-100 mt-2">`;
                        
                        brg.rincian.forEach(r => {
                            html += `<li class="py-1 flex justify-between">
                                        <span>${r.tanggal} <span class="text-gray-400">(${r.keterangan})</span></span>
                                        <span class="font-medium">-${r.jumlah}</span>
                                    </li>`;
                        });

                        html += `</ul></div>`;
                    });
                    html += `</div>`;
                }
                
                document.getElementById('modal-content').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('modal-content').innerHTML = '<p class="text-red-500 py-4">Gagal memuat data.</p>';
            });
    }
</script>
@endsection
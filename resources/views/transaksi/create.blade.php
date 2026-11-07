@extends('layout.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Input Transaksi Barang</h1>
            <p class="text-gray-500 text-sm mt-1">Catat mutasi barang masuk atau keluar gudang.</p>
        </div>
        <a href="{{ route('stock.index') }}" class="text-gray-600 bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm font-semibold transition">
            &larr; Kembali ke Stok
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg mb-6 shadow-sm">
            <div class="flex items-center mb-2">
                <strong class="font-bold">Gagal menyimpan transaksi!</strong>
            </div>
            <ul class="list-disc pl-8 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg mb-6 shadow-sm">
            <div class="flex items-center mb-2">
                <strong class="font-bold">Error!</strong>
            </div>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white p-8 rounded-lg shadow-md border border-gray-100">
        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Jenis Transaksi <span class="text-red-500">*</span></label>
                    <div class="relative flex w-full bg-gray-200 rounded-lg p-1">
                        <div class="absolute left-1 top-1 bottom-1 w-[calc(50%-4px)] bg-white rounded-md shadow transition-transform duration-300 ease-in-out" id="switch-bg"></div>
                        <label class="flex-1 text-center py-3 cursor-pointer z-10 relative">
                            <input type="radio" name="jenis_transaksi" value="masuk" class="hidden" onchange="updateSwitch(this)" checked>
                            <span class="font-semibold text-sm transition-colors text-blue-600" id="label-masuk">Barang Masuk</span>
                        </label>
                        <label class="flex-1 text-center py-3 cursor-pointer z-10 relative">
                            <input type="radio" name="jenis_transaksi" value="keluar" class="hidden" onchange="updateSwitch(this)">
                            <span class="font-semibold text-sm transition-colors text-gray-500" id="label-keluar">Barang Keluar</span>
                        </label>
                    </div>
                </div>

                <!-- SECTION BARANG MASUK (PO) -->
                <div id="section_masuk">
                    <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-lg mb-4">
                        <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Pilih PO (Purchase Order) <span class="text-red-500">*</span></label>
                        <select name="no_po" id="no_po" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" onchange="fetchPoDetails()">
                            <option value="">-- Pilih Nomor PO --</option>
                            @if(isset($pos))
                                @foreach($pos as $po)
                                    <option value="{{ $po->no_po }}">{{ $po->no_po }} - {{ $po->vendor->nama_vendor ?? '' }}</option>
                                @endforeach
                            @endif
                        </select>
                        <p class="text-xs text-indigo-600 mt-2">Hanya menampilkan PO dengan status APPROVED.</p>
                    </div>

                    <div id="po_details_container" style="display: none;">
                        <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">Daftar Barang</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse bg-white border border-gray-200 rounded">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-200 text-xs uppercase text-gray-600 tracking-wider">
                                        <th class="p-3 font-medium text-center w-12">Cek</th>
                                        <th class="p-3 font-medium">ID Barang</th>
                                        <th class="p-3 font-medium">Nama Barang</th>
                                        <th class="p-3 font-medium text-center">Qty PO</th>
                                        <th class="p-3 font-medium text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="po_details_tbody" class="divide-y divide-gray-200 text-sm text-gray-700">
                                    <!-- Items diisi via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="section_keluar" style="display: none;">
                    <!-- KELUAR MR -->
                    <div id="keluar_mr">
                        <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-lg mb-6">
                            <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Pilih Nota Material Request <span class="text-red-500">*</span></label>
                            <select name="no_nota" id="no_nota" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" onchange="fetchMrDetails()">
                                <option value="">-- Pilih Nomor Nota --</option>
                                @if(isset($mrs))
                                    @foreach($mrs as $mr)
                                        <option value="{{ $mr->no_nota }}">{{ $mr->no_nota }} - Job ID: {{ $mr->job_id ?? 'N/A' }} ({{ $mr->kategori }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div id="mr_details_container" style="display: none;" class="mb-6">
                            <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">Daftar Barang</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse bg-white border border-gray-200 rounded">
                                    <thead>
                                        <tr class="bg-gray-100 border-b border-gray-200 text-xs uppercase text-gray-600 tracking-wider">
                                            <th class="p-3 font-medium text-center w-12">Cek</th>
                                            <th class="p-3 font-medium">Nama Barang</th>
                                            <th class="p-3 font-medium text-center">Qty Diminta</th>
                                            <th class="p-3 font-medium text-center w-32">Qty Keluar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mr_details_tbody" class="divide-y divide-gray-200 text-sm text-gray-700">
                                        <!-- Items diisi via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TANGGAL (Shared for MR and Manual) -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50">
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-200 text-lg shadow-lg">
                        Simpan Transaksi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    function updateSwitch(radio) {
        const bg = document.getElementById('switch-bg');
        const labelMasuk = document.getElementById('label-masuk');
        const labelKeluar = document.getElementById('label-keluar');
        
        if (radio.value === 'keluar') {
            bg.style.transform = 'translateX(100%)';
            labelMasuk.classList.replace('text-blue-600', 'text-gray-500');
            labelKeluar.classList.replace('text-gray-500', 'text-blue-600');
        } else {
            bg.style.transform = 'translateX(0)';
            labelMasuk.classList.replace('text-gray-500', 'text-blue-600');
            labelKeluar.classList.replace('text-blue-600', 'text-gray-500');
        }
        
        toggleFormMode();
    }

    function toggleFormMode() {
        const jenisElement = document.querySelector('input[name="jenis_transaksi"]:checked');
        const jenis = jenisElement ? jenisElement.value : 'masuk';
        const sectionMasuk = document.getElementById('section_masuk');
        const sectionKeluar = document.getElementById('section_keluar');
        const noPo = document.getElementById('no_po');

        if (jenis === 'masuk') {
            sectionMasuk.style.display = 'block';
            sectionKeluar.style.display = 'none';
            noPo.required = true;
            noPo.disabled = false;
            if (noPo.tomselect) noPo.tomselect.enable();
            
            const inputs = sectionKeluar.querySelectorAll('input, select');
            inputs.forEach(el => {
                el.required = false;
                el.disabled = true;
                if (el.tomselect) el.tomselect.disable();
            });
        } else {
            sectionMasuk.style.display = 'none';
            sectionKeluar.style.display = 'block';
            noPo.required = false;
            noPo.disabled = true;
            if (noPo.tomselect) noPo.tomselect.disable();
            
            const inputs = sectionKeluar.querySelectorAll('input, select');
            inputs.forEach(el => {
                el.disabled = false;
                if (el.tomselect) el.tomselect.enable();
            });
            
            document.getElementById('no_nota').required = true;
            document.getElementById('tanggal').required = true;
        }
    }

    function fetchPoDetails() {
        const noPo = document.getElementById('no_po').value;
        const container = document.getElementById('po_details_container');
        const tbody = document.getElementById('po_details_tbody');
        
        if (!noPo) {
            container.style.display = 'none';
            return;
        }

        tbody.innerHTML = '<tr><td colspan="5" class="text-center p-4">Memuat data...</td></tr>';
        container.style.display = 'block';

        fetch(`/transaksi/po-details/${noPo}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-red-500">Tidak ada barang di PO ini.</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const tr = document.createElement('tr');
                    
                    let checkboxHtml = '';
                    let statusHtml = '';
                    
                    if (item.is_received) {
                        checkboxHtml = `<input type="checkbox" disabled checked class="h-5 w-5 text-gray-400 border-gray-300 rounded cursor-not-allowed">`;
                        statusHtml = `<span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Diterima</span>`;
                    } else {
                        checkboxHtml = `<input type="checkbox" name="items[]" value="${item.id_barang}" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">`;
                        statusHtml = `<span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Menunggu</span>`;
                    }

                    tr.className = item.is_received ? 'bg-gray-50' : 'hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="p-3 text-center">${checkboxHtml}</td>
                        <td class="p-3 font-medium text-gray-900">${item.id_barang}</td>
                        <td class="p-3">${item.nama_barang}</td>
                        <td class="p-3 text-center font-bold">${item.qty_po} ${item.satuan}</td>
                        <td class="p-3 text-center">${statusHtml}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-red-500">Gagal memuat data PO.</td></tr>';
                console.error('Error fetching PO details:', error);
            });
    }
    function fetchMrDetails() {
        const noNota = document.getElementById('no_nota').value;
        const container = document.getElementById('mr_details_container');
        const tbody = document.getElementById('mr_details_tbody');
        
        if (!noNota) {
            container.style.display = 'none';
            return;
        }

        tbody.innerHTML = '<tr><td colspan="4" class="text-center p-4">Memuat data...</td></tr>';
        container.style.display = 'block';

        fetch(`/transaksi/mr-details/${noNota}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-red-500">Tidak ada barang di Nota ini.</td></tr>';
                    return;
                }

                data.items.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    
                    let checkboxHtml = '';
                    let qtyInputHtml = '';
                    
                    if (item.is_unlisted) {
                        checkboxHtml = `<input type="checkbox" disabled class="h-5 w-5 text-gray-400 border-gray-300 rounded cursor-not-allowed">`;
                        qtyInputHtml = `<span class="text-xs text-red-500 font-semibold">Unlisted</span>`;
                    } else if (item.is_fulfilled) {
                        checkboxHtml = `<input type="checkbox" disabled checked class="h-5 w-5 text-gray-400 border-gray-300 rounded cursor-not-allowed">`;
                        qtyInputHtml = `<span class="text-xs text-green-600 font-semibold">Sudah Terpenuhi</span>`;
                    } else if (item.is_out_of_stock) {
                        checkboxHtml = `<input type="checkbox" disabled class="h-5 w-5 text-gray-400 border-gray-300 rounded cursor-not-allowed">`;
                        qtyInputHtml = `<span class="text-xs text-red-600 font-semibold">Stok Habis (0)</span>`;
                    } else {
                        checkboxHtml = `<input type="checkbox" name="items_mr[]" value="${item.id_barang}" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" onchange="toggleQtyInput(this, '${item.id_barang}')">`;
                        qtyInputHtml = `<input type="number" name="qty_mr[${item.id_barang}]" id="qty_mr_${item.id_barang}" min="1" max="${item.max_issue}" value="${item.max_issue}" class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-gray-100" disabled>`;
                    }

                    let stockInfo = '';
                    if (!item.is_unlisted) {
                        stockInfo = `<div class="text-xs ${item.sisa_stock > 0 ? 'text-green-600' : 'text-red-500'} font-medium mt-1">Sisa Stok: ${item.sisa_stock} ${item.satuan}</div>`;
                    }

                    tr.className = item.is_fulfilled || item.is_out_of_stock ? 'bg-gray-50' : 'hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="p-3 text-center">${checkboxHtml}</td>
                        <td class="p-3">
                            <div class="font-medium text-gray-900">${item.nama_barang}</div>
                            <div class="text-xs text-gray-500">${item.is_unlisted ? 'Belum terdaftar' : item.id_barang}</div>
                            ${stockInfo}
                        </td>
                        <td class="p-3 text-center font-bold">${item.qty_req} ${item.satuan}</td>
                        <td class="p-3 text-center">${qtyInputHtml}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-red-500">Gagal memuat data Material Request.</td></tr>';
                console.error('Error fetching MR details:', error);
            });
    }

    function toggleQtyInput(checkbox, idBarang) {
        const input = document.getElementById(`qty_mr_${idBarang}`);
        if (input) {
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                input.classList.add('bg-gray-100');
                input.classList.remove('bg-white');
            } else {
                input.classList.remove('bg-gray-100');
                input.classList.add('bg-white');
            }
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        toggleFormMode();
    });


</script>
@endsection
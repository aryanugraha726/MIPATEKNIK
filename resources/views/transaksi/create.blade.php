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
                    <select name="jenis_transaksi" id="jenis_transaksi" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" required onchange="toggleFormMode()">
                        <option value="masuk">Barang Masuk</option>
                        <option value="keluar">Barang Keluar</option>
                    </select>
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
                    <div class="mb-6 flex gap-4 border-b pb-4 flex-wrap">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sumber_keluar" value="manual" class="mr-2 h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300" checked onchange="toggleSumberKeluar()">
                            <span class="text-sm font-semibold uppercase tracking-wide text-gray-700">Project / Manual</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sumber_keluar" value="internal" class="mr-2 h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300" onchange="toggleSumberKeluar()">
                            <span class="text-sm font-semibold uppercase tracking-wide text-gray-700">Internal</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sumber_keluar" value="stok" class="mr-2 h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300" onchange="toggleSumberKeluar()">
                            <span class="text-sm font-semibold uppercase tracking-wide text-gray-700">Stok</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sumber_keluar" value="mr" class="mr-2 h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300" onchange="toggleSumberKeluar()">
                            <span class="text-sm font-semibold uppercase tracking-wide text-gray-700">Dari Nota MR</span>
                        </label>
                    </div>

                    <!-- KELUAR MANUAL -->
                    <div id="keluar_manual">
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-gray-700 font-semibold text-sm uppercase tracking-wide">Pilih Barang <span class="text-red-500">*</span></label>
                            </div>
                            <select name="id_barang" id="id_barang" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" onchange="updateHarga()">
                                <option value="" disabled selected data-harga="0">-- Ketik / Pilih Barang --</option>
                                @foreach($barangs as $brg)
                                    <option value="{{ $brg->id_barang }}" data-harga="{{ $brg->harga }}">{{ $brg->id_barang }} - {{ $brg->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="harga_container" class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-6" style="display:none;">
                            <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="harga_masuk" id="harga_masuk" min="0" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" oninput="checkHarga()">
                            <p id="harga_warning" class="text-xs text-yellow-700 mt-2 hidden font-medium">Harga berbeda dengan master. Barang ini akan masuk antrean FIFO otomatis (jika stok aktif masih ada).</p>
                            <input type="hidden" id="harga_master">
                        </div>

                        <div id="project_container" class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
                            <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Tujuan Project <span class="text-red-500">*</span></label>
                            <select name="job_id" id="job_id" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="" disabled selected>-- Pilih Project Tujuan --</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->job_id }}">{{ $proj->job_id }} - {{ $proj->nama_project }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-blue-600 mt-2">Wajib diisi jika barang dikeluarkan untuk kebutuhan project.</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Jumlah Barang <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah" id="jumlah" min="1" placeholder="Misal: 10" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50">
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Keterangan (Opsional)</label>
                            <input type="text" name="keterangan" id="keterangan" maxlength="30" placeholder="Catatan tambahan (Maks 30 karakter)..." class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50">
                        </div>
                    </div>

                    <!-- KELUAR MR -->
                    <div id="keluar_mr" style="display: none;">
                        <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-lg mb-6">
                            <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Pilih Nota Permintaan Barang <span class="text-red-500">*</span></label>
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
    function toggleFormMode() {
        const jenis = document.getElementById('jenis_transaksi').value;
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
            
            toggleSumberKeluar();
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
                    } else {
                        checkboxHtml = `<input type="checkbox" name="items_mr[]" value="${item.id_barang}" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" onchange="toggleQtyInput(this, '${item.id_barang}')">`;
                        qtyInputHtml = `<input type="number" name="qty_mr[${item.id_barang}]" id="qty_mr_${item.id_barang}" min="1" max="${item.qty_req}" value="${item.qty_req}" class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-gray-100" disabled>`;
                    }

                    tr.className = 'hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="p-3 text-center">${checkboxHtml}</td>
                        <td class="p-3">
                            <div class="font-medium text-gray-900">${item.nama_barang}</div>
                            <div class="text-xs text-gray-500">${item.is_unlisted ? 'Belum terdaftar' : item.id_barang}</div>
                        </td>
                        <td class="p-3 text-center font-bold">${item.qty_req} ${item.satuan}</td>
                        <td class="p-3 text-center">${qtyInputHtml}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-red-500">Gagal memuat data Permintaan Barang.</td></tr>';
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

    function toggleSumberKeluar() {
        const sumber = document.querySelector('input[name="sumber_keluar"]:checked').value;
        const keluarManual = document.getElementById('keluar_manual');
        const keluarMr = document.getElementById('keluar_mr');
        
        const manualInputs = keluarManual.querySelectorAll('input, select');
        const mrInputs = keluarMr.querySelectorAll('input, select');
        
        if (sumber === 'manual' || sumber === 'internal' || sumber === 'stok') {
            keluarManual.style.display = 'block';
            keluarMr.style.display = 'none';
            
            manualInputs.forEach(el => {
                el.disabled = false;
                if (el.tomselect) el.tomselect.enable();
            });
            mrInputs.forEach(el => {
                el.disabled = true;
                if (el.tomselect) el.tomselect.disable();
            });
            
            document.getElementById('id_barang').required = true;
            document.getElementById('jumlah').required = true;
            
            // Set default keterangan for Internal and Stok
            const ketInput = document.getElementById('keterangan');
            if (sumber === 'internal') ketInput.value = 'Penggunaan Internal';
            else if (sumber === 'stok') ketInput.value = 'Penyesuaian Stok';
            else if (ketInput.value === 'Penggunaan Internal' || ketInput.value === 'Penyesuaian Stok') ketInput.value = '';
            
            toggleProject();
        } else {
            keluarManual.style.display = 'none';
            keluarMr.style.display = 'block';
            
            manualInputs.forEach(el => {
                el.required = false;
                el.disabled = true;
                if (el.tomselect) el.tomselect.disable();
            });
            mrInputs.forEach(el => {
                el.disabled = false;
                if (el.tomselect) el.tomselect.enable();
            });
            
            document.getElementById('no_nota').required = true;
        }
    }
    function toggleProject() {
        var jenis = document.getElementById('jenis_transaksi').value;
        var projectContainer = document.getElementById('project_container');
        var projectInput = document.getElementById('job_id');
        var hargaContainer = document.getElementById('harga_container');
        var hargaInput = document.getElementById('harga_masuk');

        if (jenis === 'keluar') {
            const sumberEl = document.querySelector('input[name="sumber_keluar"]:checked');
            const sumber = sumberEl ? sumberEl.value : 'manual';
            
            if (sumber === 'internal' || sumber === 'stok' || sumber === 'mr') {
                projectContainer.style.display = 'none';
                projectInput.removeAttribute('required');
                projectInput.value = '';
            } else {
                projectContainer.style.display = 'block';
                projectInput.setAttribute('required', 'required');
            }
            
            hargaContainer.style.display = 'none';
            hargaInput.removeAttribute('required');
        } else {
            projectContainer.style.display = 'none';
            projectInput.removeAttribute('required');
            projectInput.value = '';

            hargaContainer.style.display = 'block';
            hargaInput.setAttribute('required', 'required');
        }
    }

    function updateHarga() {
        var select = document.getElementById('id_barang');
        var selectedOption = select.options[select.selectedIndex];
        var harga = selectedOption.getAttribute('data-harga');
        
        document.getElementById('harga_master').value = harga;
        document.getElementById('harga_masuk').value = harga;
        
        checkHarga(); // Cek di awal jika form edit (meskipun ini form create)
    }

    document.addEventListener("DOMContentLoaded", () => {
        toggleFormMode();
    });

    function checkHarga() {
        var hargaMaster = document.getElementById('harga_master').value;
        var hargaMasuk = document.getElementById('harga_masuk').value;
        var warning = document.getElementById('harga_warning');
        var jenis = document.getElementById('jenis_transaksi').value;

        if (jenis === 'masuk' && hargaMasuk !== '' && hargaMaster !== '' && hargaMasuk !== hargaMaster) {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    }

    // Run on load to set initial state
    window.onload = function() {
        toggleProject();
    };


</script>
@endsection
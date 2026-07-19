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

    <div class="bg-white p-8 rounded-lg shadow-md border border-gray-100">
        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Jenis Transaksi <span class="text-red-500">*</span></label>
                    <select name="jenis_transaksi" id="jenis_transaksi" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" required onchange="toggleProject()">
                        <option value="masuk">Barang Masuk (In)</option>
                        <option value="keluar">Barang Keluar (Out)</option>
                    </select>
                </div>

                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-gray-700 font-semibold text-sm uppercase tracking-wide">Pilih Barang <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openBarangModal()" class="text-xs bg-indigo-100 text-indigo-700 font-bold px-3 py-1 rounded hover:bg-indigo-200 transition">
                            + Tambah Barang Baru
                        </button>
                    </div>
                    <select name="id_barang" id="id_barang" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" required onchange="updateHarga()">
                        <option value="" disabled selected data-harga="0">-- Ketik / Pilih Barang --</option>
                        @foreach($barangs as $brg)
                            <option value="{{ $brg->id_barang }}" data-harga="{{ $brg->harga }}">{{ $brg->id_barang }} - {{ $brg->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="harga_container" class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-2">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_masuk" id="harga_masuk" min="0" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" oninput="checkHarga()">
                    <p id="harga_warning" class="text-xs text-yellow-700 mt-2 hidden font-medium">Harga berbeda dengan master. Barang ini akan masuk antrean FIFO otomatis (jika stok aktif masih ada).</p>
                    <input type="hidden" id="harga_master">
                </div>

                <div id="project_container" style="display: none;" class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Tujuan Project <span class="text-red-500">*</span></label>
                    <select name="project_id" id="project_id" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="" disabled selected>-- Pilih Project Tujuan --</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->project_id }}">{{ $proj->project_id }} - {{ $proj->nama_project }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-blue-600 mt-2">Wajib diisi jika barang dikeluarkan untuk kebutuhan project.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Jumlah Barang <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" min="1" placeholder="Misal: 10" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm uppercase tracking-wide">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" maxlength="30" placeholder="Catatan tambahan (Maks 30 karakter)..." class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50">
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

<!-- Modal Tambah Master Barang -->
<div id="barangModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 overflow-y-auto flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
        <div class="flex justify-between items-center bg-indigo-600 text-white p-4 rounded-t-xl">
            <h3 class="text-lg font-bold">Tambah Master Barang Baru</h3>
            <button type="button" onclick="closeBarangModal()" class="text-white hover:text-gray-200 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6">
            <div id="ajax_error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm"></div>
            
            <form id="formBarangAjax">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">ID Barang (Kodifikasi)</label>
                        <input type="text" id="ajax_id_barang" name="id_barang" placeholder="Otomatis terisi jika kosong" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" id="ajax_nama_barang" name="nama_barang" placeholder="Misal: Semen Tiga Roda" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">Kategori <span class="text-red-500">*</span></label>
                            <select id="ajax_id_kategori" name="id_kategori" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required onchange="generateAjaxId(this.value)">
                                <option value="" disabled selected>-- Pilih --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id_kategori }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">Satuan <span class="text-red-500">*</span></label>
                            <select id="ajax_id_satuan" name="id_satuan" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                @foreach($satuans as $satuan)
                                    <option value="{{ $satuan->id_satuan }}">{{ $satuan->nama_satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Harga Master (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="ajax_harga" name="harga" min="0" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                    <button type="button" onclick="closeBarangModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Batal</button>
                    <button type="button" onclick="submitBarangAjax()" id="btnSubmitAjax" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 flex items-center">
                        <span id="btnTextAjax">Simpan & Pilih</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleProject() {
        var jenis = document.getElementById('jenis_transaksi').value;
        var projectContainer = document.getElementById('project_container');
        var projectInput = document.getElementById('project_id');
        var hargaContainer = document.getElementById('harga_container');
        var hargaInput = document.getElementById('harga_masuk');

        if (jenis === 'keluar') {
            projectContainer.style.display = 'block';
            projectInput.setAttribute('required', 'required');
            
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
        
        checkHarga();
    }

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

    // --- Modal & AJAX Logic ---
    function openBarangModal() {
        document.getElementById('barangModal').classList.remove('hidden');
        document.getElementById('ajax_error').classList.add('hidden');
        document.getElementById('formBarangAjax').reset();
    }

    function closeBarangModal() {
        document.getElementById('barangModal').classList.add('hidden');
    }

    function generateAjaxId(kategoriId) {
        if(!kategoriId) return;
        
        var inputId = document.getElementById('ajax_id_barang');
        var lastAuto = inputId.getAttribute('data-last-auto') || '';

        // Hanya isi jika kosong ATAU jika nilainya sama dengan auto-generate terakhir
        if(inputId.value !== '' && inputId.value !== lastAuto) return;

        fetch('/barang/next-id/' + kategoriId)
            .then(response => response.json())
            .then(data => {
                if(data.next_id) {
                    inputId.value = data.next_id;
                    inputId.placeholder = data.next_id;
                    inputId.setAttribute('data-last-auto', data.next_id);
                }
            })
            .catch(error => console.error('Error fetching next ID:', error));
    }

    function submitBarangAjax() {
        var btn = document.getElementById('btnSubmitAjax');
        var btnText = document.getElementById('btnTextAjax');
        var errorBox = document.getElementById('ajax_error');
        
        var id_barang = document.getElementById('ajax_id_barang').value;
        var nama_barang = document.getElementById('ajax_nama_barang').value;
        var id_kategori = document.getElementById('ajax_id_kategori').value;
        var id_satuan = document.getElementById('ajax_id_satuan').value;
        var harga = document.getElementById('ajax_harga').value;
        var _token = document.querySelector('input[name="_token"]').value;

        if(!nama_barang || !id_kategori || !id_satuan || !harga) {
            errorBox.textContent = 'Harap isi semua kolom bertanda *';
            errorBox.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btnText.textContent = 'Menyimpan...';
        errorBox.classList.add('hidden');

        fetch("{{ route('barang.storeAjax') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': _token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                id_barang: id_barang,
                nama_barang: nama_barang,
                id_kategori: id_kategori,
                id_satuan: id_satuan,
                harga: harga
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Add new option to select
                var select = document.getElementById('id_barang');
                var option = document.createElement("option");
                option.text = data.barang.id_barang + " - " + data.barang.nama_barang;
                option.value = data.barang.id_barang;
                option.setAttribute('data-harga', data.barang.harga);
                
                select.add(option);
                select.value = data.barang.id_barang;
                
                closeBarangModal();
                updateHarga(); // Trigger price update
            } else {
                errorBox.textContent = data.message || 'Terjadi kesalahan validasi.';
                errorBox.classList.remove('hidden');
            }
        })
        .catch(error => {
            errorBox.textContent = 'Terjadi kesalahan sistem.';
            errorBox.classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            btnText.textContent = 'Simpan & Pilih';
        });
    }
</script>
@endsection
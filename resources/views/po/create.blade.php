@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Purchase Order (PO)</h1>
            <p class="text-gray-500 text-sm mt-1">Isi formulir di bawah ini untuk membuat PO baru</p>
        </div>
        <a href="{{ route('po.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm flex items-center gap-1">
            &larr; Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc pl-5 text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="po-form" action="{{ route('po.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Informasi PO -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Informasi PO</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor PO <span class="text-red-500">*</span></label>
                        <input type="text" name="no_po" value="{{ old('no_po', '000/PO-MIPATEKNIK/' . date('my')) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('no_po') border-red-500 @enderror" required placeholder="Contoh: 000/PO-MIPATEKNIK/0000">
                        @error('no_po')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal PO</label>
                        <input type="text" value="{{ \Carbon\Carbon::now()->format('d M Y') }}" class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendor <span class="text-red-500">*</span></label>
                        <select name="id_vendor" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id_vendor }}" {{ old('id_vendor') == $v->id_vendor ? 'selected' : '' }}>
                                    {{ $v->id_vendor }} - {{ $v->nama_vendor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Pengiriman <span class="text-red-500">*</span></label>
                        <select name="id_lokasi" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($shippingAddresses as $s)
                                <option value="{{ $s->id_lokasi }}" {{ old('id_lokasi') == $s->id_lokasi ? 'selected' : '' }}>
                                    {{ $s->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Referensi Project (Job ID)</label>
                    <select name="job_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Stock (Tanpa Referensi Project) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->job_id }}" {{ old('job_id') == $p->job_id ? 'selected' : '' }}>
                                JOB-{{ $p->job_id }} | {{ $p->nama_project }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_ppn" id="is_ppn" value="1" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="calculateGrandTotal()" {{ old('is_ppn') ? 'checked' : '' }}>
                        <label for="is_ppn" class="ml-2 block text-sm font-medium text-gray-700">
                            Kenakan PPN (11%)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_pph" id="is_pph" value="1" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="calculateGrandTotal()" {{ old('is_pph') ? 'checked' : '' }}>
                        <label for="is_pph" class="ml-2 block text-sm font-medium text-gray-700">
                            Potong PPh 23 (2%)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Biaya -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-center">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Ringkasan Total</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Total Harga Barang</span>
                        <span class="font-medium" id="lbl_total_barang">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600" id="row_ppn" style="display: none;">
                        <span>PPN (11%)</span>
                        <span class="font-medium text-red-500" id="lbl_ppn">+ Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600" id="row_pph" style="display: none;">
                        <span>PPh 23 (2%)</span>
                        <span class="font-medium text-emerald-600" id="lbl_pph">- Rp 0</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <span class="font-bold text-gray-800">Grand Total</span>
                        <span class="font-bold text-emerald-600 text-lg" id="lbl_grand_total">Rp 0</span>
                    </div>
                </div>
                
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <button type="submit" id="btn_submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                        <span>Simpan PO</span>
                    </button>
                    <p class="text-xs text-gray-500 mt-2 text-center">Pastikan minimal 1 barang telah ditambahkan</p>
                </div>
            </div>
        </div>

        <!-- Daftar Barang -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">Daftar Barang (Item Details)</h2>
            </div>
            <div class="p-6 border-b border-gray-200 bg-blue-50/50">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Barang</label>
                        <select id="sel_barang" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="updateHargaBeli()">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $b)
                                <option value="{{ $b->id_barang }}" data-name="{{ $b->nama_barang }}" data-price="{{ $b->harga }}" data-satuan="{{ $b->satuan->nama_satuan ?? $b->id_satuan }}">
                                    {{ $b->id_barang }} - {{ $b->nama_barang }} (Master: Rp {{ number_format($b->harga,0,',','.') }}/{{ $b->satuan->nama_satuan ?? $b->id_satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-40">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Harga Beli (Rp)</label>
                        <input type="number" id="inp_harga" min="0" value="0" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                        <input type="number" id="inp_qty" min="1" value="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <button type="button" onclick="addItem()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm transition-colors h-10">
                        + Tambah
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200 text-xs uppercase text-gray-600 tracking-wider">
                            <th class="p-4 font-medium">ID Barang</th>
                            <th class="p-4 font-medium">Nama Barang</th>
                            <th class="p-4 font-medium text-right">Harga Satuan</th>
                            <th class="p-4 font-medium text-center">Qty</th>
                            <th class="p-4 font-medium text-right">Subtotal</th>
                            <th class="p-4 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table_items" class="divide-y divide-gray-200 text-sm text-gray-700">
                        <!-- Items will be appended here -->
                        <tr id="empty_row">
                            <td colspan="6" class="p-6 text-center text-gray-500">Belum ada barang yang ditambahkan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div id="hidden_inputs_container"></div>
        </div>
    </form>
</div>

<script>
    let items = [];

    // Pre-populate old items if validation
    @if(old('items'))
        @foreach(old('items') as $index => $oldItem)
            @php
                $oldB = \App\Models\Barang::find($oldItem['id_barang']);
                $oldHarga = isset($oldItem['harga']) ? $oldItem['harga'] : ($oldB ? $oldB->harga : 0);
            @endphp
            @if($oldB)
                items.push({
                    id: '{{ $oldB->id_barang }}',
                    name: '{{ $oldB->nama_barang }}',
                    price: parseFloat('{{ $oldHarga }}'),
                    satuan: '{{ $oldB->satuan->nama_satuan ?? $oldB->id_satuan }}',
                    qty: parseInt('{{ $oldItem['qty_po'] }}')
                });
            @endif
        @endforeach
    @endif

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function calculateGrandTotal() {
        let total_barang = 0;
        items.forEach(item => {
            total_barang += (item.price * item.qty);
        });

        const isPpn = document.getElementById('is_ppn').checked;
        const isPph = document.getElementById('is_pph').checked;

        let nilai_ppn = isPpn ? (total_barang * 0.11) : 0;
        let nilai_pph = isPph ? (total_barang * 0.02) : 0;
        
        let grand_total = total_barang + nilai_ppn - nilai_pph;

        // Update UI
        document.getElementById('lbl_total_barang').textContent = formatRupiah(total_barang);
        
        const rowPpn = document.getElementById('row_ppn');
        if(isPpn) {
            rowPpn.style.display = 'flex';
            document.getElementById('lbl_ppn').textContent = '+ ' + formatRupiah(nilai_ppn);
        } else {
            rowPpn.style.display = 'none';
        }

        const rowPph = document.getElementById('row_pph');
        if(isPph) {
            rowPph.style.display = 'flex';
            document.getElementById('lbl_pph').textContent = '- ' + formatRupiah(nilai_pph);
        } else {
            rowPph.style.display = 'none';
        }

        document.getElementById('lbl_grand_total').textContent = formatRupiah(grand_total);
        
        renderHiddenInputs();
    }

    function renderTable() {
        const tbody = document.getElementById('table_items');
        tbody.innerHTML = '';
        
        if(items.length === 0) {
            tbody.innerHTML = '<tr id="empty_row"><td colspan="6" class="p-6 text-center text-gray-500">Belum ada barang yang ditambahkan</td></tr>';
            calculateGrandTotal();
            return;
        }

        items.forEach((item, index) => {
            const subtotal = item.price * item.qty;
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';
            tr.innerHTML = `
                <td class="p-4 font-medium text-gray-900">${item.id}</td>
                <td class="p-4">${item.name}</td>
                <td class="p-4 text-right">${formatRupiah(item.price)}</td>
                <td class="p-4 text-center font-semibold">${item.qty} ${item.satuan}</td>
                <td class="p-4 text-right font-medium">${formatRupiah(subtotal)}</td>
                <td class="p-4 text-center">
                    <button type="button" onclick="removeItem(${index})" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-xs font-medium transition">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        calculateGrandTotal();
    }

    function renderHiddenInputs() {
        const container = document.getElementById('hidden_inputs_container');
        container.innerHTML = '';
        
        items.forEach((item, index) => {
            const inpId = document.createElement('input');
            inpId.type = 'hidden';
            inpId.name = `items[${index}][id_barang]`;
            inpId.value = item.id;
            
            const inpQty = document.createElement('input');
            inpQty.type = 'hidden';
            inpQty.name = `items[${index}][qty_po]`;
            inpQty.value = item.qty;
            const inpHarga = document.createElement('input');
            inpHarga.type = 'hidden';
            inpHarga.name = `items[${index}][harga]`;
            inpHarga.value = item.price;
            
            container.appendChild(inpId);
            container.appendChild(inpQty);
            container.appendChild(inpHarga);
        });
    }

    function updateHargaBeli() {
        const sel = document.getElementById('sel_barang');
        if(sel.value) {
            const option = sel.options[sel.selectedIndex];
            document.getElementById('inp_harga').value = option.getAttribute('data-price');
        } else {
            document.getElementById('inp_harga').value = 0;
        }
    }

    function addItem() {
        const sel = document.getElementById('sel_barang');
        const qtyInp = document.getElementById('inp_qty');
        const hargaInp = document.getElementById('inp_harga');
        
        if(!sel.value) {
            alert('Pilih barang terlebih dahulu!');
            return;
        }
        
        const qty = parseInt(qtyInp.value);
        if(isNaN(qty) || qty < 1) {
            alert('Qty minimal 1');
            return;
        }

        const hargaBeli = parseFloat(hargaInp.value);
        if(isNaN(hargaBeli) || hargaBeli < 0) {
            alert('Harga beli tidak valid');
            return;
        }

        const option = sel.options[sel.selectedIndex];
        const id = option.value;
        
        // Check if item already exists
        const existingIndex = items.findIndex(i => i.id === id);
        if(existingIndex >= 0) {
            items[existingIndex].qty += qty;
            items[existingIndex].price = hargaBeli; // Update harga jika berbeda
        } else {
            items.push({
                id: id,
                name: option.getAttribute('data-name'),
                price: hargaBeli,
                satuan: option.getAttribute('data-satuan'),
                qty: qty
            });
        }
        
        // Reset inputs
        sel.value = '';
        qtyInp.value = 1;
        hargaInp.value = 0;
        
        renderTable();
    }

    function removeItem(index) {
        items.splice(index, 1);
        renderTable();
    }

    // Form validation
    document.getElementById('po-form').addEventListener('submit', function(e) {
        if(items.length === 0) {
            e.preventDefault();
            alert('Silakan tambahkan minimal 1 barang sebelum menyimpan PO.');
        }
    });

    // Initial render
    renderTable();
</script>
@endsection

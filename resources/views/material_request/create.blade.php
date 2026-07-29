@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Buat Nota Permintaan Barang</h1>
        <p class="text-sm text-gray-500">Isi form di bawah ini untuk mengajukan kebutuhan material {{ $isPurchasing ? '(Bypass Approval)' : '' }}</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 text-red-500 p-4 mb-6 rounded-lg">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('material-requests.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Project (Job ID)</label>
                    <select name="job_id" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Job ID --</option>
                        @foreach($jobIds as $jobId)
                            <option value="{{ $jobId }}">{{ $jobId }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Request</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <hr class="my-6 border-gray-100">

            <div class="mb-4 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Detail Material</h3>
                <button type="button" id="addRowBtn" class="text-sm bg-gray-900 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left" id="itemsTable">
                    <thead>
                        <tr class="text-xs uppercase text-gray-500 border-b border-gray-200">
                            <th class="pb-2 font-medium">Barang</th>
                            <th class="pb-2 font-medium w-48 hidden" id="thBarangBaru">Nama Barang Baru</th>
                            <th class="pb-2 font-medium w-24 hidden" id="thSatuanBaru">Satuan</th>
                            <th class="pb-2 font-medium w-32">Qty</th>
                            <th class="pb-2 font-medium w-16 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody" class="divide-y divide-gray-100">
                        <!-- Rows will be added here via JS -->
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('material-requests.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    let rowIndex = 0;
    const barangs = @json($barangs);
    
    function addRow() {
        const tr = document.createElement('tr');
        
        let options = '<option value="">-- Pilih Barang --</option>';
        options += '<option value="NEW_ITEM" class="font-bold text-blue-600">++ BARANG BARU (Belum Terdaftar) ++</option>';
        barangs.forEach(b => {
            options += `<option value="${b.id_barang}">${b.nama_barang}</option>`;
        });

        tr.innerHTML = `
            <td class="py-3 pr-2">
                <select name="items[${rowIndex}][id_barang]" class="item-select w-full p-2 border border-gray-300 rounded focus:ring-blue-500" required>
                    ${options}
                </select>
            </td>
            <td class="py-3 px-2 hidden new-item-col">
                <input type="text" name="items[${rowIndex}][nama_barang_baru]" placeholder="Nama Barang..." class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500">
            </td>
            <td class="py-3 px-2 hidden new-item-col">
                <input type="text" name="items[${rowIndex}][satuan_baru]" placeholder="Pcs/Ltr" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500">
            </td>
            <td class="py-3 px-2">
                <input type="number" name="items[${rowIndex}][req_qty]" min="1" required class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500">
            </td>
            <td class="py-3 pl-2 text-right">
                <button type="button" class="text-red-500 hover:bg-red-50 p-1.5 rounded remove-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        `;
        
        document.getElementById('itemsBody').appendChild(tr);
        
        const selectEl = tr.querySelector('.item-select');
        
        // Initialize TomSelect for the new select dropdown
        const ts = new TomSelect(selectEl, {
            create: false,
            // Custom render for the options to highlight NEW_ITEM
            render: {
                option: function(data, escape) {
                    if (data.value === 'NEW_ITEM') {
                        return '<div class="font-bold text-blue-600">' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                }
            }
        });

        // Use Tom Select's 'change' event
        ts.on('change', function(val) {
            const isNew = val === 'NEW_ITEM';
            const newCols = tr.querySelectorAll('.new-item-col');
            const thBarang = document.getElementById('thBarangBaru');
            const thSatuan = document.getElementById('thSatuanBaru');
            
            if (isNew) {
                // selectEl.removeAttribute('required'); // TomSelect handles its own required state based on input
                newCols.forEach(col => {
                    col.classList.remove('hidden');
                    col.querySelector('input').setAttribute('required', 'required');
                });
                thBarang.classList.remove('hidden');
                thSatuan.classList.remove('hidden');
            } else {
                // selectEl.setAttribute('required', 'required');
                newCols.forEach(col => {
                    col.classList.add('hidden');
                    col.querySelector('input').removeAttribute('required');
                    col.querySelector('input').value = '';
                });
            }
        });

        tr.querySelector('.remove-btn').addEventListener('click', function() {
            ts.destroy(); // Cleanup TomSelect instance
            tr.remove();
        });
        
        rowIndex++;
    }

    document.getElementById('addRowBtn').addEventListener('click', addRow);
    
    // Add first row default
    addRow();
</script>
@endsection

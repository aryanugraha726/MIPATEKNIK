@extends('layout.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Work Order Release</h1>
    <a href="{{ route('wor.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
</div>

@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
    <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('wor.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
    @csrf
    
    <!-- Section 1: Job, Customer, Desc -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-4 border-b-2 border-gray-300">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Job ID <span class="text-red-500">*</span></label>
            <input type="text" maxlength="7" name="job_id" value="{{ old('job_id', $nextId) }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
            <input type="text" name="customer" value="{{ old('customer') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Description <span class="text-red-500">*</span></label>
            <textarea name="jobdesc" rows="2" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>{{ old('jobdesc') }}</textarea>
        </div>
    </div>

    <!-- Section 2: Order Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-4 border-b-2 border-gray-300">
        <!-- Left Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order No. <span class="text-red-500">*</span></label>
                <input type="text" name="po_proyek" value="{{ old('po_proyek') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Order <span class="text-red-500">*</span></label>
                <input type="date" name="tgl_order" value="{{ old('tgl_order') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Schedule <span class="text-red-500">*</span></label>
                <input type="date" name="jadwal_kirim" value="{{ old('jadwal_kirim') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address <span class="text-red-500">*</span></label>
                <textarea name="alamat_kirim" rows="2" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>{{ old('alamat_kirim') }}</textarea>
            </div>
        </div>
        <!-- Right Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person <span class="text-red-500">*</span></label>
                <input type="text" name="cp_customer" value="{{ old('cp_customer') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sales</label>
                <input type="text" name="sales" value="{{ old('sales') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mail Address</label>
                <input type="text" name="mail_address" value="{{ old('mail_address') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estimate Man Hour</label>
                <input type="number" step="0.01" name="estimate_man_hour" value="{{ old('estimate_man_hour') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
        </div>
    </div>

    <!-- Section 3: Equipment Detail & Priority Status -->
    <div class="mb-6 pb-4 border-b-2 border-gray-300">
        <h3 class="text-md font-semibold text-gray-700 mb-3">Equipment Detail</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Material</label>
                <input type="text" name="material" value="{{ old('material') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                <input type="number" name="equip_qty" value="{{ old('equip_qty') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                <input type="text" name="model" value="{{ old('model') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Power</label>
                <input type="text" name="power" value="{{ old('power') }}" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority Status</label>
                    <select name="priority" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Pilih --</option>
                        <option value="High Priority" {{ old('priority') == 'High Priority' ? 'selected' : '' }}>High Priority</option>
                        <option value="Standard Time" {{ old('priority') == 'Standard Time' ? 'selected' : '' }}>Standard Time</option>
                        <option value="Low Priority" {{ old('priority') == 'Low Priority' ? 'selected' : '' }}>Low Priority</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Hari Kerja <span class="text-gray-400 text-xs">(Opsional)</span></label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="number" name="priority_days" value="{{ old('priority_days') }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white">
                        <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            hari
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-lg font-semibold border-b pb-2 mb-4 flex justify-between items-center">
        Work Scope <span class="text-red-500">*</span>
        <button type="button" id="add-detail" class="text-sm bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Tambah Item</button>
    </h2>
    <div id="details-container">
        <!-- Template row -->
        <div class="detail-row border border-gray-300 rounded-lg p-4 mb-4 bg-gray-100 relative shadow-inner">
            <button type="button" class="remove-detail absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold">&times;</button>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Part Description</label>
                    <input type="text" name="details[0][desc_part]" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                    <input type="number" name="details[0][part_qty]" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Satuan</label>
                    <select name="details[0][id_satuan]" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($satuans as $sat)
                            <option value="{{ $sat->id_satuan }}">{{ $sat->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
            Simpan Work Order
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let detailIndex = 1;
        const container = document.getElementById('details-container');
        const addButton = document.getElementById('add-detail');

        addButton.addEventListener('click', function() {
            const template = `
            <div class="detail-row bg-gray-100 p-4 rounded-md shadow-inner border border-gray-300 relative">
                <button type="button" class="remove-detail absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold px-2">&times;</button>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Part Description</label>
                        <input type="text" name="details[${detailIndex}][desc_part]" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                        <input type="number" name="details[${detailIndex}][part_qty]" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Satuan</label>
                        <select name="details[${detailIndex}][id_satuan]" class="mt-1 block w-full bg-white rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach($satuans as $sat)
                                <option value="{{ $sat->id_satuan }}">{{ $sat->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', template);
            detailIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-detail')) {
                e.target.closest('.detail-row').remove();
            }
        });
    });
</script>
@endsection

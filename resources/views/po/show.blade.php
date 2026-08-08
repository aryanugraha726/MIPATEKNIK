@extends('layout.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Detail Purchase Order</h1>
        <p class="text-gray-500 text-sm mt-1">No. PO: <span class="font-bold text-gray-700">{{ $po->no_po }}</span></p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('po.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            &larr; Kembali
        </a>
        @if($po->status === 'APPROVED')
            <a href="{{ route('po.exportPdf') . '?no_po=' . urlencode($po->no_po) }}" target="_blank"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors flex items-center gap-1">
                Cetak PDF
            </a>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm">
        <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
        <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Informasi Utama -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-lg font-bold text-gray-800">Informasi PO</h2>
            @if($po->status === 'DRAFT')
                <span class="bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs font-semibold">DRAFT</span>
            @elseif($po->status === 'PENDING_APPROVAL')
                <span class="bg-yellow-100 text-yellow-700 py-1 px-3 rounded-full text-xs font-semibold animate-pulse">MENUNGGU PERSETUJUAN</span>
            @elseif($po->status === 'APPROVED')
                <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">DISETUJUI</span>
            @elseif($po->status === 'REJECTED')
                <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">DITOLAK</span>
            @endif
        </div>
        
        <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm">
            <div>
                <p class="text-gray-500 mb-1">Vendor</p>
                <p class="font-semibold text-gray-900">{{ $po->vendor->nama_vendor ?? '-' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $po->vendor->alamat_vendor ?? '' }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Tanggal PO</p>
                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($po->tgl_po)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Alamat Pengiriman</p>
                <p class="font-semibold text-gray-900">{{ $po->shippingAddress->nama_lokasi ?? '-' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $po->shippingAddress->alamat_mipa ?? '' }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Job ID</p>
                <p class="font-medium text-gray-900">
                    @if($po->job_id)
                        JOB-{{ $po->job_id }} 
                        @if(isset($po->workOrderRelease->project))
                            - {{ $po->workOrderRelease->project->nama_project }}
                        @endif
                    @else
                        Stock
                    @endif
                </p>
            </div>
        </div>

        @if($po->status === 'REJECTED')
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800 font-semibold mb-1">Alasan Penolakan:</p>
                <p class="text-sm text-red-600">{{ $po->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <!-- Ringkasan Biaya -->
    @php
        $total_barang = 0;
        foreach($po->details as $d) {
            $b = \App\Models\Barang::find($d->id_barang);
            if($b) $total_barang += ($b->harga * $d->qty_po);
        }
        $nilai_ppn = ($po->ppn / 100) * $total_barang;
        $nilai_pph = ($po->pph / 100) * $total_barang;
    @endphp
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-center">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Ringkasan Total</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Total Harga Barang</span>
                <span class="font-medium">Rp {{ number_format($total_barang, 0, ',', '.') }}</span>
            </div>
            @if($po->ppn > 0)
            <div class="flex justify-between text-gray-600">
                <span>PPN ({{ $po->ppn }}%)</span>
                <span class="font-medium text-red-500">+ Rp {{ number_format($nilai_ppn, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($po->pph > 0)
            <div class="flex justify-between text-gray-600">
                <span>PPh 23 ({{ $po->pph }}%)</span>
                <span class="font-medium text-emerald-600">- Rp {{ number_format($nilai_pph, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="pt-3 border-t border-gray-200 flex justify-between">
                <span class="font-bold text-gray-800">Grand Total</span>
                <span class="font-bold text-emerald-600 text-lg">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Barang -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <h2 class="text-lg font-bold text-gray-800">Daftar Barang (Item Details)</h2>
    </div>

    @if($po->status === 'DRAFT' && (in_array('ADMIN', Auth::user()->roles()) || in_array('PURCHASING', Auth::user()->roles())))
        <div class="p-6 border-b border-gray-200 bg-blue-50/50">
            <form action="{{ route('po.add-detail', $po->no_po) }}" method="POST" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Barang</label>
                    <select name="id_barang" id="id_barang" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required onchange="updateHargaBeliShow()">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $b)
                            <option value="{{ $b->id_barang }}" data-price="{{ $b->harga }}">{{ $b->id_barang }} - {{ $b->nama_barang }} (Master: Rp {{ number_format($b->harga,0,',','.') }}/{{ $b->satuan->nama_satuan ?? $b->id_satuan }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Beli (Rp)</label>
                    <input type="number" name="harga" id="inp_harga" min="0" value="0" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                    <input type="number" name="qty_po" min="1" value="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm transition-colors h-10">
                    + Tambah
                </button>
            </form>
            <script>
                function updateHargaBeliShow() {
                    const sel = document.getElementById('id_barang');
                    if(sel.value) {
                        const option = sel.options[sel.selectedIndex];
                        document.getElementById('inp_harga').value = option.getAttribute('data-price');
                    } else {
                        document.getElementById('inp_harga').value = 0;
                    }
                }
            </script>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200 text-xs uppercase text-gray-600 tracking-wider">
                    <th class="p-4 font-medium">ID Barang</th>
                    <th class="p-4 font-medium">Nama Barang</th>
                    <th class="p-4 font-medium text-right">Harga Satuan</th>
                    <th class="p-4 font-medium text-center">Qty</th>
                    <th class="p-4 font-medium text-right">Subtotal</th>
                    @if($po->status === 'DRAFT')
                    <th class="p-4 font-medium text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                @forelse ($po->details as $detail)
                    @php
                        $barang = \App\Models\Barang::find($detail->id_barang);
                        $harga = $detail->harga ?? ($barang ? $barang->harga : 0);
                        $subtotal = $harga * $detail->qty_po;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-medium text-gray-900">{{ $detail->id_barang }}</td>
                        <td class="p-4">{{ $barang->nama_barang ?? '-' }}</td>
                        <td class="p-4 text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                        <td class="p-4 text-center font-semibold">{{ $detail->qty_po }} {{ $barang->satuan->nama_satuan ?? $detail->id_satuan }}</td>
                        <td class="p-4 text-right font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        @if($po->status === 'DRAFT')
                        <td class="p-4 text-center">
                            <form action="{{ route('po.remove-detail', $detail->id_po_detail) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus Item">&times;</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $po->status === 'DRAFT' ? '6' : '5' }}" class="p-8 text-center text-gray-500">
                            Belum ada barang yang ditambahkan ke PO ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Aksi / Action Panel -->
@if($po->status === 'DRAFT' && count($po->details) > 0 && (in_array('ADMIN', Auth::user()->roles()) || in_array('PURCHASING', Auth::user()->roles())))
    <div class="bg-blue-50 border border-blue-200 p-6 rounded-lg shadow-sm text-center">
        <h3 class="text-lg font-bold text-blue-800 mb-2">Ajukan PO ke Direktur Utama</h3>
        <p class="text-sm text-blue-600 mb-4">Pastikan semua barang dan harga sudah benar sebelum diajukan untuk persetujuan.</p>
        <form action="{{ route('po.submit', $po->no_po) }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-sm transition-colors text-lg">
                Ajukan Persetujuan
            </button>
        </form>
    </div>
@endif

@if($po->status === 'PENDING_APPROVAL' && in_array('DIREKTUR UTAMA', Auth::user()->roles()))
    <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg shadow-sm text-center">
        <h3 class="text-lg font-bold text-yellow-800 mb-2">Persetujuan Direktur Utama</h3>
        <p class="text-sm text-yellow-700 mb-6">Silakan tinjau daftar pesanan di atas. Anda dapat menyetujui atau menolak PO ini.</p>
        
        <div class="flex justify-center gap-4">
            <form action="{{ route('po.approve', $po->no_po) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-sm transition-colors text-lg" onclick="return confirm('Apakah Anda yakin menyetujui PO ini?');">
                    ✓ Setujui PO
                </button>
            </form>

            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="bg-red-100 hover:bg-red-200 text-red-700 font-bold py-3 px-8 rounded-lg shadow-sm transition-colors text-lg">
                ✗ Tolak PO
            </button>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tolak Purchase Order</h3>
            <form action="{{ route('po.reject', $po->no_po) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500" required placeholder="Tuliskan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md shadow-sm">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #content-area, #content-area * {
            visibility: visible;
        }
        .mb-6.flex.justify-between, .bg-blue-50, .bg-yellow-50, form, button {
            display: none !important;
        }
        #content-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .shadow-sm {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endsection

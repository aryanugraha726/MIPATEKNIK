@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Gudang</h1>
        <span class="text-gray-500">{{ date('d M Y') }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
            <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Total Jenis Barang</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalJenisBarang, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-teal-500">
            <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Transaksi Barang Masuk</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalTransaksiMasuk, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Transaksi Barang Keluar</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalTransaksiKeluar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Total Stok Tersedia</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalStokKeseluruhan, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Total Nilai Barang Masuk</h3>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($nilaiMasuk, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Total Nilai Barang Keluar</h3>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($nilaiKeluar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Total Nilai Aset Saat Ini</h3>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalAset, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2 flex flex-col">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-2 sm:mb-0">Grafik Transaksi Barang<span class="text-blue-600 font-medium text-base ml-1">({{ $chartTitle ?? '6 Bulan Terakhir' }})</span></h2>
                <form action="{{ route('dashboard.index') }}" method="GET" class="flex items-center space-x-2" id="chart-filter-form">
                    <input type="hidden" name="offset" id="time-offset" value="{{ request('offset', 0) }}">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider hidden sm:inline">Periode:</span>
                    
                    <button type="button" onclick="shiftPeriod(1)" class="p-1 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 focus:outline-none transition-colors" title="Mundur 1 Periode">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <div class="relative">
                        <select name="filter" id="period-filter" onchange="resetOffsetAndSubmit()" class="no-search block appearance-none w-40 sm:w-48 bg-white border border-gray-200 text-gray-700 py-1.5 pl-3 pr-8 rounded-lg leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium shadow-sm transition-all duration-200 cursor-pointer hover:border-gray-300 hover:shadow">
                            <option value="mingguan" {{ request('filter') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan" {{ request('filter') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="quarter" {{ request('filter') == 'quarter' ? 'selected' : '' }}>Quarter</option>
                            <option value="6_bulan" {{ request('filter') == '6_bulan' || !request()->has('filter') ? 'selected' : '' }}>6 Bulan</option>
                            <option value="tahunan" {{ request('filter') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <button type="button" onclick="shiftPeriod(-1)" class="p-1 rounded-full {{ request('offset', 0) > 0 ? 'text-gray-400 hover:text-blue-600 hover:bg-blue-50 cursor-pointer' : 'text-gray-200 cursor-not-allowed' }} focus:outline-none transition-colors" title="Maju 1 Periode" {{ request('offset', 0) > 0 ? '' : 'disabled' }}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </form>
            </div>
            <div class="w-full h-80 flex-1">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-1 flex flex-col">
            <h2 class="text-lg font-bold text-gray-800 mb-6 text-center">Stok Berdasarkan Kategori</h2>
            
            <div class="relative w-full h-56 flex justify-center mb-6">
                <canvas id="categoryChart"></canvas>
                <!-- Teks di Tengah Donut Chart -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xs text-gray-500 font-semibold uppercase">Total Stok</span>
                    <span class="text-2xl font-bold text-gray-800">{{ number_format($totalStokKeseluruhan, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="mt-auto max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                <ul class="divide-y divide-gray-100">
                    @forelse($stokPerKategori as $index => $item)
                    <li class="py-2 flex justify-between items-center text-sm">
                        <div class="flex items-center">
                            <!-- Indikator warna dinamis berdasarkan urutan data -->
                            <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#f97316'][$index % 8] }}"></span>
                            <span class="text-gray-700 font-medium">{{ $item->nama_kategori }}</span>
                        </div>
                        <span class="font-bold text-gray-900">{{ number_format($item->total_stok, 0, ',', '.') }}</span>
                    </li>
                    @empty
                    <li class="py-2 text-center text-gray-500 italic text-sm">Tidak ada data stok.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-base font-bold text-gray-800">Riwayat Barang Masuk Terakhir</h2>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($mutasiMasuk as $masuk)
                <li class="px-4 py-2 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs font-medium text-blue-600">{{ $masuk->barang->nama_barang ?? 'Barang tidak ditemukan' }} ({{ $masuk->id_barang }})</p>
                            <p class="text-[10px] text-gray-500">{{ date('d M Y', strtotime($masuk->tgl_masuk)) }} - {{ $masuk->ket_masuk }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800">
                            +{{ $masuk->jml_masuk }} Masuk
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-4 py-2 text-center text-gray-500 text-xs">Belum ada riwayat masuk.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-base font-bold text-gray-800">Riwayat Barang Keluar Terakhir</h2>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($mutasiKeluar as $keluar)
                <li class="px-4 py-2 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs font-medium text-red-600">{{ $keluar->barang->nama_barang ?? 'Barang tidak ditemukan' }} ({{ $keluar->id_barang }})</p>
                            <p class="text-[10px] text-gray-500">Job ID: {{ $keluar->job_id }} | {{ date('d M Y', strtotime($keluar->tgl_keluar)) }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-800">
                            -{{ $keluar->jumlah_keluar }} Keluar
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-4 py-2 text-center text-gray-500 text-xs">Belum ada riwayat keluar.</li>
                @endforelse
            </ul>
        </div>

    </div>

    @if($lowStocks->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg shadow-sm mb-8">
        <div class="px-4 py-3 border-b border-red-200 bg-red-100 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h2 class="text-sm font-bold text-red-800">Peringatan: Stok Menipis (<= 3)</h2>
            </div>
            @if($lowStocks->count() > 5)
            <button type="button" onclick="document.getElementById('lowStockModal').classList.remove('hidden')" class="text-xs font-bold text-red-600 hover:text-red-800 bg-white px-3 py-1 rounded-full border border-red-200 shadow-sm transition-colors">
                Lihat Selengkapnya ({{ $lowStocks->count() }})
            </button>
            @endif
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                @foreach($lowStocks->take(5) as $stock)
                <div class="bg-white border border-red-200 p-2 rounded flex justify-between items-center shadow-sm hover:shadow transition-shadow">
                    <div class="flex flex-col flex-1 min-w-0 text-left pr-2">
                        <span class="text-[10px] font-bold text-gray-500 truncate">{{ $stock->id_barang }}</span>
                        <span class="text-xs font-semibold text-gray-800 leading-tight truncate">{{ $stock->nama_barang }}</span>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">Sisa: {{ $stock->sisa_stock }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Low Stock -->
    <div id="lowStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('lowStockModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Daftar Seluruh Barang Menipis
                            </h3>
                            <div class="mt-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-96 overflow-y-auto p-1 custom-scrollbar">
                                    @foreach($lowStocks as $stock)
                                    <div class="bg-gray-50 border border-red-200 p-2 rounded flex justify-between items-center">
                                        <div class="flex flex-col flex-1 min-w-0 text-left pr-2">
                                            <span class="text-[10px] font-bold text-gray-500 truncate">{{ $stock->id_barang }}</span>
                                            <span class="text-xs font-semibold text-gray-800 leading-tight truncate">{{ $stock->nama_barang }}</span>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="text-red-600 text-xs font-bold">Sisa: {{ $stock->sisa_stock }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('lowStockModal').classList.add('hidden')">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('inventoryChart').getContext('2d');
        const inventoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: {!! json_encode($chartMasuk) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Barang Keluar',
                        data: {!! json_encode($chartKeluar) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // Kategori Donut Chart
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        const bgColors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#f97316'];
        
        const categoryChart = new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($kategoriLabels) !!},
                datasets: [{
                    data: {!! json_encode($kategoriData) !!},
                    backgroundColor: bgColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Membuat bagian tengah bolong/kopong lebih besar
                plugins: {
                    legend: {
                        display: false // Sembunyikan legenda default karena kita sudah buat daftarnya
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('id-ID').format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });

    // Fungsi untuk menggeser waktu (offset periode)
    function shiftPeriod(direction) {
        const offsetInput = document.getElementById('time-offset');
        let currentOffset = parseInt(offsetInput.value) || 0;
        
        let newOffset = currentOffset + direction;
        if (newOffset < 0) newOffset = 0; // Tidak bisa maju melebihi waktu sekarang
        
        offsetInput.value = newOffset;
        document.getElementById('chart-filter-form').submit();
    }

    // Fungsi reset offset saat ganti filter
    function resetOffsetAndSubmit() {
        document.getElementById('time-offset').value = 0;
        document.getElementById('chart-filter-form').submit();
    }
</script>
@endsection
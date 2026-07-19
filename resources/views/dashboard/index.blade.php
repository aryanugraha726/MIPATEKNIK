@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Gudang</h1>
        <span class="text-gray-500">{{ date('d M Y') }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

    <div class="bg-white rounded-lg shadow mb-8 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Analitik Pergerakan Barang (6 Bulan Terakhir)</h2>
        <div class="w-full h-80">
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-bold text-gray-800">Riwayat Barang Masuk Terakhir</h2>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($mutasiMasuk as $masuk)
                <li class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-blue-600">{{ $masuk->barang->nama_barang ?? 'Barang tidak ditemukan' }} ({{ $masuk->id_barang }})</p>
                            <p class="text-xs text-gray-500">{{ date('d M Y', strtotime($masuk->tgl_masuk)) }} - {{ $masuk->ket_masuk }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            +{{ $masuk->jml_masuk }} Masuk
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-6 py-4 text-center text-gray-500 text-sm">Belum ada riwayat masuk.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-bold text-gray-800">Riwayat Barang Keluar Terakhir</h2>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($mutasiKeluar as $keluar)
                <li class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-red-600">{{ $keluar->barang->nama_barang ?? 'Barang tidak ditemukan' }} ({{ $keluar->id_barang }})</p>
                            <p class="text-xs text-gray-500">Project ID: {{ $keluar->project_id }} | {{ date('d M Y', strtotime($keluar->tgl_keluar)) }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            -{{ $keluar->jumlah_keluar }} Keluar
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-6 py-4 text-center text-gray-500 text-sm">Belum ada riwayat keluar.</li>
                @endforelse
            </ul>
        </div>

    </div>

    @if($lowStocks->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg shadow-sm mb-8">
        <div class="px-6 py-4 border-b border-red-200 bg-red-100 rounded-t-lg flex items-center">
            <span class="text-red-600 text-xl mr-2"></span>
            <h2 class="text-lg font-bold text-red-800">Peringatan: Stok Menipis (<= 3)</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($lowStocks as $stock)
                <div class="bg-white border border-red-300 p-4 rounded-lg shadow-sm flex flex-col items-center text-center">
                    <span class="font-bold text-gray-800">{{ $stock->id_barang }}</span>
                    <span class="text-sm text-gray-600 mb-2">{{ $stock->nama_barang }}</span>
                    <span class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">Sisa: {{ $stock->sisa_stock }}</span>
                </div>
                @endforeach
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
    });
</script>
@endsection
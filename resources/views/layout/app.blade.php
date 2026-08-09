<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIPA TEKNIK - Enterprise System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Customizing Tom Select to better match Tailwind forms */
        .ts-control { border-radius: 0.5rem; padding: 0.625rem; border-color: #d1d5db; box-shadow: none; }
        .ts-control.focus { box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); border-color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-50 font-sans flex h-screen overflow-hidden">

    <!-- Sidebar Navigation -->
    <div class="w-64 bg-gray-900 text-white flex flex-col shadow-xl">
        <div class="p-6 border-b border-gray-800 bg-gray-950">
            <h2 class="text-xl font-black tracking-wider text-blue-500">MIPA TEKNIK</h2>
            <p class="text-xs text-gray-500 mt-1 font-semibold uppercase tracking-widest">Enterprise System</p>
        </div>
        @php $roles = auth()->user()->roles(); @endphp
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            {{-- ============================================= --}}
            {{-- MENU PPIC (ADMIN, PPIC) --}}
            {{-- ============================================= --}}
            @if(array_intersect(['ADMIN', 'PPIC'], $roles))
            @php $isPpicActive = request()->routeIs('ppic.*') || request()->routeIs('projects.*') || request()->routeIs('subprojects.*') || request()->routeIs('tugas.*'); @endphp
            <div class="pt-2">
                <button onclick="document.getElementById('ppic-dropdown').classList.toggle('hidden'); document.getElementById('ppic-arrow').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded-lg focus:outline-none transition-colors">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isPpicActive ? 'text-gray-300' : 'text-gray-500' }}">PPIC</span>
                    <svg id="ppic-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isPpicActive ? 'rotate-180 text-gray-300' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="ppic-dropdown" class="mt-1 space-y-1 {{ $isPpicActive ? '' : 'hidden' }}">
                    <a href="{{ route('ppic.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('ppic.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Dashboard PPIC</span>
                    </a>
                    <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('projects.*') || request()->routeIs('subprojects.*') || request()->routeIs('tugas.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Daftar Project</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- MENU MARKETING (ADMIN, MARKETING) --}}
            {{-- ============================================= --}}
            @if(array_intersect(['ADMIN', 'MARKETING'], $roles))
            @php $isMktActive = request()->routeIs('wor.*'); @endphp
            <div class="pt-2">
                <button onclick="document.getElementById('mkt-dropdown').classList.toggle('hidden'); document.getElementById('mkt-arrow').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded-lg focus:outline-none transition-colors">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isMktActive ? 'text-gray-300' : 'text-gray-500' }}">Marketing</span>
                    <svg id="mkt-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isMktActive ? 'rotate-180 text-gray-300' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="mkt-dropdown" class="mt-1 space-y-1 {{ $isMktActive ? '' : 'hidden' }}">
                    <a href="{{ route('wor.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('wor.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Work Order Release</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- MENU PURCHASING (ADMIN, PURCHASING) --}}
            {{-- ============================================= --}}
            @if(array_intersect(['ADMIN', 'PURCHASING'], $roles))
            @php $isPurchActive = request()->routeIs('dashboard.*') || request()->routeIs('stock.*') || request()->routeIs('transaksi.*') || request()->routeIs('material-requests.*') || request()->routeIs('po.*') || request()->routeIs('shipping-address.*'); @endphp
            <div class="pt-2">
                <button onclick="document.getElementById('purchasing-dropdown').classList.toggle('hidden'); document.getElementById('purchasing-arrow').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded-lg focus:outline-none transition-colors">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isPurchActive ? 'text-gray-300' : 'text-gray-500' }}">Purchasing</span>
                    <svg id="purchasing-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isPurchActive ? 'rotate-180 text-gray-300' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="purchasing-dropdown" class="mt-1 space-y-1 {{ $isPurchActive ? '' : 'hidden' }}">
                    <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Dashboard Gudang</span>
                    </a>
                    <a href="{{ route('stock.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('stock.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Stock Opname</span>
                    </a>
                    <a href="{{ route('transaksi.antrean') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('transaksi.antrean') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Antrean Barang</span>
                    </a>
                    <a href="{{ route('transaksi.create') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('transaksi.create') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Input Transaksi</span>
                    </a>
                    <a href="{{ route('transaksi.masuk') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('transaksi.masuk') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Riwayat Masuk</span>
                    </a>
                    <a href="{{ route('transaksi.keluar') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('transaksi.keluar') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Riwayat Keluar</span>
                    </a>
                    <a href="{{ route('material-requests.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('material-requests.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Material Request</span>
                    </a>
                    <a href="{{ route('po.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('po.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Purchase Order</span>
                    </a>
                    <a href="{{ route('shipping-address.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('shipping-address.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Alamat Pengiriman</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- MENU KARYAWAN (KARYAWAN) --}}
            {{-- ============================================= --}}
            @if(array_intersect(['ADMIN', 'KARYAWAN'], $roles))
            @php $isKaryawanActive = request()->routeIs('karyawan.projects') || request()->routeIs('material-requests.*'); @endphp
            <div class="pt-2">
                <button onclick="document.getElementById('karyawan-dropdown').classList.toggle('hidden'); document.getElementById('karyawan-arrow').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded-lg focus:outline-none transition-colors">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isKaryawanActive ? 'text-gray-300' : 'text-gray-500' }}">Karyawan</span>
                    <svg id="karyawan-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isKaryawanActive ? 'rotate-180 text-gray-300' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="karyawan-dropdown" class="mt-1 space-y-1 {{ $isKaryawanActive ? '' : 'hidden' }}">
                    <a href="{{ route('karyawan.projects') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('karyawan.projects') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Project Saya</span>
                    </a>
                    <a href="{{ route('material-requests.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('material-requests.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Material Request</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- MENU MANAGEMENT --}}
            {{-- ============================================= --}}
            @if(array_intersect(['ADMIN', 'MANAGEMENT'], $roles))
            @php $isManagerActive = request()->routeIs('manager_approval.*'); @endphp
            <div class="pt-2">
                <button onclick="document.getElementById('manager-dropdown').classList.toggle('hidden'); document.getElementById('manager-arrow').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded-lg focus:outline-none transition-colors">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isManagerActive ? 'text-gray-300' : 'text-gray-500' }}">Management</span>
                    <svg id="manager-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isManagerActive ? 'rotate-180 text-gray-300' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="manager-dropdown" class="mt-1 space-y-1 {{ $isManagerActive ? '' : 'hidden' }}">
                    <a href="{{ route('manager_approval.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('manager_approval.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Persetujuan Manager</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- MENU DIREKTUR UTAMA (DIREKTUR UTAMA) --}}
            {{-- ============================================= --}}
            @if(array_intersect(['ADMIN', 'DIREKTUR UTAMA'], $roles))
            @php $isDirActive = request()->routeIs('direktur.stock') || request()->routeIs('dashboard.*') || request()->routeIs('ppic.index') || request()->routeIs('po.*') || request()->routeIs('wor.*'); @endphp
            <div class="pt-2">
                <button onclick="document.getElementById('dir-dropdown').classList.toggle('hidden'); document.getElementById('dir-arrow').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded-lg focus:outline-none transition-colors">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isDirActive ? 'text-gray-300' : 'text-gray-500' }}">Direktur Utama</span>
                    <svg id="dir-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isDirActive ? 'rotate-180 text-gray-300' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="dir-dropdown" class="mt-1 space-y-1 {{ $isDirActive ? '' : 'hidden' }}">
                    <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Dashboard Gudang</span>
                    </a>
                    <a href="{{ route('direktur.stock') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('direktur.stock') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Stock Opname</span>
                    </a>
                    <a href="{{ route('ppic.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('ppic.index') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Jadwal Project (Gantt)</span>
                    </a>
                    <a href="{{ route('po.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('po.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Persetujuan PO</span>
                    </a>
                    <a href="{{ route('wor.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('wor.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Persetujuan WOR</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- MENU ADMINISTRATOR / HRD / PURCHASING / PPIC --}}
            {{-- ============================================= --}}
            @if(array_intersect(['ADMIN', 'HRD', 'PURCHASING', 'PPIC'], $roles))
            @php
                $isMasterDataActive = request()->is('master-data*') || request()->is('divisi*') || request()->is('karyawan*') || request()->is('vendor*') || request()->is('satuan*') || request()->is('kategori*') || request()->is('management*') || request()->is('barang*') || request()->is('users*');
            @endphp
            <div class="pt-2">
                <button onclick="document.getElementById('master-data-dropdown').classList.toggle('hidden'); document.getElementById('master-data-arrow').classList.toggle('rotate-180');" class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-800 rounded-lg focus:outline-none transition-colors">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isMasterDataActive ? 'text-gray-300' : 'text-gray-500' }}">
                        {{ in_array('ADMIN', $roles) ? 'Administrator' : (in_array('HRD', $roles) ? 'HRD' : 'Master Data') }}
                    </span>
                    <svg id="master-data-arrow" class="w-4 h-4 transition-transform duration-200 {{ $isMasterDataActive ? 'rotate-180 text-gray-300' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div id="master-data-dropdown" class="mt-1 space-y-1 {{ $isMasterDataActive ? '' : 'hidden' }}">
                    @if(array_intersect(['ADMIN', 'HRD', 'PURCHASING'], $roles))
                    <a href="{{ route('master-data.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('master-data.index') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Beranda Master Data</span>
                    </a>
                    @endif
                    @if(array_intersect(['ADMIN', 'HRD'], $roles))
                    <a href="{{ route('divisi.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('divisi.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Divisi</span>
                    </a>
                    <a href="{{ route('karyawan.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('karyawan.*') && !request()->routeIs('karyawan.projects') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Karyawan</span>
                    </a>
                    <a href="{{ route('management.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('management.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Management</span>
                    </a>
                    @endif
                    @if(array_intersect(['ADMIN', 'PURCHASING', 'PPIC'], $roles))
                    <a href="{{ route('vendor.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('vendor.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Vendor</span>
                    </a>
                    @endif
                    @if(array_intersect(['ADMIN', 'PURCHASING'], $roles))
                    <a href="{{ route('barang.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('barang.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Master Barang</span>
                    </a>
                    <a href="{{ route('kategori.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('kategori.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Kategori Barang</span>
                    </a>
                    <a href="{{ route('satuan.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('satuan.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Satuan</span>
                    </a>
                    @endif
                    @if(in_array('ADMIN', $roles))
                    <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="font-medium text-sm pl-2">Manajemen Akun</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </nav>
        
        <div class="p-4 border-t border-gray-800 bg-gray-950">
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-200">{{ auth()->user()->karyawan->nm_karyawan ?? auth()->user()->username }}</span>
                    <span class="text-xs text-blue-400 font-semibold uppercase tracking-wider">{{ implode(', ', $roles) }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
        @yield('content')
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize TomSelect on all select elements except those with .no-search
            document.querySelectorAll('select:not(.no-search)').forEach((el) => {
                if (!el.tomselect) {
                    new TomSelect(el, {
                        create: false
                    });
                }
            });

            // Script untuk sinkronisasi Tanggal Mulai, Durasi (Hari), dan Target Selesai
            const startInputs = document.querySelectorAll('.sync-start');
            const durasiInputs = document.querySelectorAll('.sync-durasi');
            const targetInputs = document.querySelectorAll('.sync-target');

            for(let i = 0; i < startInputs.length; i++){
                const startInput = startInputs[i];
                const durasiInput = durasiInputs[i];
                const targetInput = targetInputs[i];

                function updateTarget() {
                    if (startInput.value && durasiInput.value) {
                        const startDate = new Date(startInput.value);
                        startDate.setDate(startDate.getDate() + parseInt(durasiInput.value));
                        targetInput.value = startDate.toISOString().split('T')[0];
                    }
                }

                function updateDurasi() {
                    if (startInput.value && targetInput.value) {
                        const startDate = new Date(startInput.value);
                        const targetDate = new Date(targetInput.value);
                        const diffTime = targetDate - startDate;
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        
                        if (diffDays >= 0) {
                            durasiInput.value = diffDays;
                        } else {
                            durasiInput.value = 0;
                            updateTarget();
                        }
                    }
                }

                startInput.addEventListener('change', updateTarget);
                durasiInput.addEventListener('input', updateTarget);
                targetInput.addEventListener('change', updateDurasi);
                
                // Inisialisasi awal
                if(startInput.value && durasiInput.value && !targetInput.value) {
                    updateTarget();
                }
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIPA TEKNIK - Enterprise System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans flex h-screen overflow-hidden">

    <!-- Sidebar Navigation -->
    <div class="w-64 bg-gray-900 text-white flex flex-col shadow-xl">
        <div class="p-6 border-b border-gray-800 bg-gray-950">
            <h2 class="text-xl font-black tracking-wider text-blue-500">MIPA TEKNIK</h2>
            <p class="text-xs text-gray-500 mt-1 font-semibold uppercase tracking-widest">Enterprise System</p>
        </div>
        @php $role = auth()->user()->role->nama_role ?? ''; @endphp
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            {{-- ============================================= --}}
            {{-- MENU PPIC (ADMIN, PPIC) --}}
            {{-- ============================================= --}}
            @if(in_array($role, ['ADMIN', 'PPIC']))
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">PPIC</p>
            </div>
            <a href="{{ route('ppic.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('ppic.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Dashboard PPIC</span>
            </a>
            <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('projects.*') || request()->routeIs('subprojects.*') || request()->routeIs('tugas.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Daftar Project</span>
            </a>
            @endif

            {{-- ============================================= --}}
            {{-- MENU PURCHASING (ADMIN, PURCHASING) --}}
            {{-- ============================================= --}}
            @if(in_array($role, ['ADMIN', 'PURCHASING']))
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Purchasing</p>
            </div>
            <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('dashboard.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Dashboard Gudang</span>
            </a>
            <a href="{{ route('barang.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('barang.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Daftar Barang</span>
            </a>
            <a href="{{ route('stock.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('stock.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Stock Opname</span>
            </a>
            <a href="{{ route('transaksi.antrean') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('transaksi.antrean') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Antrean Barang</span>
            </a>
            <a href="{{ route('transaksi.create') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('transaksi.create') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Input Transaksi</span>
            </a>
            <a href="{{ route('transaksi.masuk') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('transaksi.masuk') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Riwayat Masuk</span>
            </a>
            <a href="{{ route('transaksi.keluar') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('transaksi.keluar') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Riwayat Keluar</span>
            </a>
            @endif

            {{-- ============================================= --}}
            {{-- MENU KARYAWAN (KARYAWAN) --}}
            {{-- ============================================= --}}
            @if(in_array($role, ['ADMIN', 'KARYAWAN']))
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Karyawan</p>
            </div>
            <a href="{{ route('karyawan.projects') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('karyawan.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Project Saya</span>
            </a>
            @endif

            {{-- ============================================= --}}
            {{-- MENU DIREKTUR UTAMA (DIREKTUR UTAMA) --}}
            {{-- ============================================= --}}
            @if(in_array($role, ['ADMIN', 'DIREKTUR UTAMA']))
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Direktur Utama</p>
            </div>
            <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('dashboard.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Dashboard Gudang</span>
            </a>
            <a href="{{ route('direktur.stock') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('direktur.stock') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Stock Opname</span>
            </a>
            <a href="{{ route('ppic.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('ppic.index') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Jadwal Project (Gantt)</span>
            </a>
            @endif

            {{-- ============================================= --}}
            {{-- MENU ADMINISTRATOR (ADMIN saja) --}}
            {{-- ============================================= --}}
            @if($role == 'ADMIN')
            <div class="pt-6 pb-2">
                <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Administrator</p>
            </div>
            <a href="{{ route('master-data.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->is('master-data*') || request()->is('divisi*') || request()->is('karyawan*') || request()->is('vendor*') || request()->is('satuan*') || request()->is('kategori*') || request()->is('role*') || request()->is('management*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Master Data</span>
            </a>
            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-sm' : '' }}">
                <span class="font-medium text-sm">Manajemen Akun</span>
            </a>
            @endif
        </nav>
        
        <div class="p-4 border-t border-gray-800 bg-gray-950">
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-200">{{ auth()->user()->karyawan->nm_karyawan ?? auth()->user()->username }}</span>
                    <span class="text-xs text-blue-400 font-semibold uppercase tracking-wider">{{ $role }}</span>
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

</body>
</html>
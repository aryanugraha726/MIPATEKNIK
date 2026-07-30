@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 border-l-4 border-yellow-500 pl-3">Dashboard PPIC</h1>
        <p class="text-gray-500 text-sm">Pilih proyek untuk melihat visualisasi jadwal proyek</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $p)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $p->prioritas == 'TINGGI' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                    {{ $p->prioritas }}
                </span>
                <span class="text-xs text-gray-500 font-mono">#{{ $p->job_id }}</span>
            </div>
            
            <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">{{ $p->nama_project }}</h3>
            
            <div class="text-sm text-gray-600 mb-4 space-y-1">
                <p><strong>Mulai:</strong> {{ date('d M Y', strtotime($p->start_project)) }}</p>
                <p><strong>Target Selesai:</strong> {{ date('d M Y', strtotime($p->target_project)) }}</p>
                <p><strong>Penanggung Jawab:</strong> {{ $p->management->karyawan->nm_karyawan ?? '-' }}</p>
            </div>

            <a href="{{ route('ppic.gantt', $p->job_id) }}" class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition">
                Lihat Jadwal Proyek
            </a>
        </div>
        @empty
        <div class="col-span-full bg-white p-8 text-center rounded border border-gray-200">
            <p class="text-gray-500">Belum ada proyek yang terdaftar.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

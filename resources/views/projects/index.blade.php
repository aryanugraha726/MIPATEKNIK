@extends('layout.app')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Project</h1>
        <a href="{{ route('projects.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">Tambah Project</a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Job</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Project</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Waktu</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penanggung Jawab</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $p)
                <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="toggleRow('sub-{{ $p->job_id }}', 'icon-p-{{ $p->job_id }}')">
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900 flex items-center">
                        <svg id="icon-p-{{ $p->job_id }}" class="w-4 h-4 mr-2 text-gray-400 transition-transform transform rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        {{ $p->job_id }}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-800">{{ $p->nama_project }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $p->prioritas == 'TINGGI' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $p->prioritas }}
                        </span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                        {{ date('d M Y', strtotime($p->start_project)) }} - {{ date('d M Y', strtotime($p->target_project)) }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                        @php
                            $diff = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($p->target_project)->startOfDay(), false);
                            $color = $diff < 0 ? 'text-red-600 font-bold' : ($diff <= 7 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold');
                            $text = $diff < 0 ? 'Terlambat ' . abs(intval($diff)) . ' hari' : ($diff == 0 ? 'Hari Ini' : intval($diff) . ' Hari');
                        @endphp
                        <span class="{{ $color }}">{{ $text }}</span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                        {{ $p->management->karyawan->nm_karyawan ?? 'N/A' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-400">-</td>
                    <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('projects.show', $p->job_id) }}" onclick="event.stopPropagation()" class="text-indigo-600 hover:text-indigo-900 mx-1 border border-indigo-200 px-2 py-1 rounded bg-indigo-50">Detail</a>
                        <a href="{{ route('ppic.gantt', $p->job_id) }}" onclick="event.stopPropagation()" class="text-yellow-600 hover:text-yellow-900 mx-1 border border-yellow-200 px-2 py-1 rounded bg-yellow-50">Jadwal</a>
                        <a href="{{ route('projects.edit', $p->job_id) }}" onclick="event.stopPropagation()" class="text-blue-600 hover:text-blue-900 mx-1">Edit</a>
                        <form action="{{ route('projects.destroy', $p->job_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="event.stopPropagation()" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                        </form>
                    </td>
                </tr>

                {{-- SUBPROJECT ROWS --}}
                @foreach($p->subprojects as $sub)
                <tr class="sub-{{ $p->job_id }} hidden bg-blue-50/30 hover:bg-blue-50 transition {{ count($sub->tugas) > 0 ? 'cursor-pointer' : '' }}" {!! count($sub->tugas) > 0 ? "onclick=\"toggleRow('tugas-{$sub->subproject_id}', 'icon-s-{$sub->subproject_id}')\"" : "" !!}>
                    <td class="px-3 py-2 pl-6 whitespace-nowrap text-sm font-bold text-gray-700 flex items-center border-l-4 border-blue-200">
                        @if(count($sub->tugas) > 0)
                        <svg id="icon-s-{{ $sub->subproject_id }}" class="w-4 h-4 mr-2 text-blue-400 transition-transform transform rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        @else
                        <span class="w-4 h-4 mr-2 inline-block"></span>
                        @endif
                        └ {{ substr($sub->subproject_id, -2) }}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-700 font-medium">{{ $sub->nama_subproject }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-400">-</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                        {{ date('d M Y', strtotime($sub->start_subproject)) }} - {{ date('d M Y', strtotime($sub->target_subproject)) }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                        @php
                            $diffSub = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($sub->target_subproject)->startOfDay(), false);
                            $colorSub = $diffSub < 0 ? 'text-red-600 font-bold' : ($diffSub <= 7 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold');
                            $textSub = $diffSub < 0 ? 'Terlambat ' . abs(intval($diffSub)) . ' hari' : ($diffSub == 0 ? 'Hari Ini' : intval($diffSub) . ' Hari');
                        @endphp
                        <span class="{{ $colorSub }}">{{ $textSub }}</span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                        {{ $sub->management->karyawan->nm_karyawan ?? 'N/A' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                        {{ $sub->karyawan->nm_karyawan ?? 'N/A' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                        @if(count($sub->tugas) == 0)
                        <form action="{{ route('subprojects.toggle-status', $sub->subproject_id) }}" method="POST" class="inline-block mr-2">
                            @csrf
                            <input type="checkbox" onchange="this.form.submit()" {{ $sub->is_completed ? 'checked' : '' }} class="w-5 h-5 text-blue-600 bg-white rounded border-gray-400 focus:ring-blue-500 cursor-pointer align-middle">
                            @if($sub->is_completed && $sub->tanggal_selesai)
                            <span class="block text-xs mt-1 text-gray-500 italic">
                                {{ date('d M Y', strtotime($sub->tanggal_selesai)) }}
                            </span>
                            @endif
                        </form>
                        @endif
                        <a href="{{ route('subprojects.show', $sub->subproject_id) }}" onclick="event.stopPropagation()" class="text-indigo-600 hover:text-indigo-900 mx-1 border border-indigo-200 px-2 py-1 rounded bg-indigo-50 inline-block align-middle">Detail</a>
                        <a href="{{ route('subprojects.edit', $sub->subproject_id) }}" onclick="event.stopPropagation()" class="text-blue-600 hover:text-blue-900 mx-1">Edit</a>
                        <form action="{{ route('subprojects.destroy', $sub->subproject_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus subproject ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="event.stopPropagation()" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                        </form>
                    </td>
                </tr>

                    {{-- TUGAS ROWS --}}
                    @foreach($sub->tugas as $t)
                    <tr class="tugas-{{ $sub->subproject_id }} hidden bg-gray-100 hover:bg-gray-200 transition">
                        <td class="px-3 py-2 pl-12 whitespace-nowrap text-sm font-bold text-gray-600 border-l-4 border-gray-300">
                            └ {{ substr($t->tugas_id, -2) }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $t->tugas }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-400">-</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                            {{ date('d M Y', strtotime($t->start_tugas)) }} - {{ date('d M Y', strtotime($t->target_tugas)) }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                            @php
                                $diffTugas = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($t->target_tugas)->startOfDay(), false);
                                $colorTugas = $diffTugas < 0 ? 'text-red-600 font-bold' : ($diffTugas <= 7 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold');
                                $textTugas = $diffTugas < 0 ? 'Terlambat ' . abs(intval($diffTugas)) . ' hari' : ($diffTugas == 0 ? 'Hari Ini' : intval($diffTugas) . ' Hari');
                            @endphp
                            <span class="{{ $colorTugas }}">{{ $textTugas }}</span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-400">-</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">
                            @if($t->id_karyawan)
                                {{ $t->karyawan->nm_karyawan }}
                            @elseif($t->id_vendor)
                                <span class="font-semibold">VENDOR :</span> {{ $t->vendor->nama_vendor }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center justify-center space-x-3">
                                <form action="{{ route('tugas.toggle-status', $t->tugas_id) }}" method="POST" class="flex flex-col items-center">
                                    @csrf
                                    <input type="checkbox" onchange="this.form.submit()" {{ $t->is_completed ? 'checked' : '' }} class="w-5 h-5 text-blue-600 bg-white rounded border-gray-400 focus:ring-blue-500 cursor-pointer">
                                    @if($t->is_completed && $t->tanggal_selesai)
                                    <span class="text-xs mt-1 text-gray-500 italic">
                                        {{ date('d M Y', strtotime($t->tanggal_selesai)) }}
                                    </span>
                                    @endif
                                </form>
                                <a href="{{ route('tugas.edit', $t->tugas_id) }}" onclick="event.stopPropagation()" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                                <form action="{{ route('tugas.destroy', $t->tugas_id) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="event.stopPropagation()" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                @endforeach

                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data project</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleRow(className, iconId) {
        const rows = document.querySelectorAll('.' + className);
        let isHidden = true;
        rows.forEach(row => {
            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                isHidden = false;
            } else {
                row.classList.add('hidden');
                
                // Jika baris yang ditutup adalah subproject, kita juga harus menutup tugas-tugas di dalamnya
                if (className.startsWith('sub-')) {
                    const onclickAttr = row.getAttribute('onclick');
                    if (onclickAttr) {
                        const match = onclickAttr.match(/toggleRow\('([^']+)'/);
                        if (match && match[1]) {
                            const taskRows = document.querySelectorAll('.' + match[1]);
                            taskRows.forEach(tRow => tRow.classList.add('hidden'));
                            
                            // Reset icon subproject jika ada
                            const iconMatch = onclickAttr.match(/,\s*'([^']+)'/);
                            if (iconMatch && iconMatch[1]) {
                                const subIcon = document.getElementById(iconMatch[1]);
                                if (subIcon) {
                                    subIcon.classList.remove('rotate-90');
                                }
                            }
                        }
                    }
                }
            }
        });

        const icon = document.getElementById(iconId);
        if (icon) {
            if (!isHidden) {
                icon.classList.add('rotate-90');
            } else {
                icon.classList.remove('rotate-90');
            }
        }
    }
</script>
@endsection

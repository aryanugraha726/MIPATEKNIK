@extends('layout.app')

@section('content')
<!-- Include Frappe Gantt CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.css" />

<style>
    /* Custom styling for gantt chart to make it look premium */
    .gantt .bar-wrapper.bar-subproject .bar { fill: #4F46E5 !important; }
    .gantt .bar-wrapper.bar-subproject .bar-progress { fill: #3730A3 !important; }
    
    .gantt .bar-wrapper.bar-tugas .bar { fill: #10B981 !important; }
    .gantt .bar-wrapper.bar-tugas .bar-progress { fill: #059669 !important; }

    /* Sembunyikan label di dalam bar karena sekarang kita pakai sidebar */
    .gantt .bar-label { 
        display: none !important;
    }
    
    /* Disable drag and resize visually */
    .gantt .handle-group { display: none !important; }
    .gantt .bar-wrapper { cursor: default !important; }

    /* Print Styles for flawless SVG Export */
    @media print {
        body * {
            visibility: hidden;
        }
        #pdf-container, #pdf-container * {
            visibility: visible;
        }
        #pdf-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @page {
            size: landscape;
            margin: 10mm;
        }
    }
</style>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold flex items-center border-l-4 border-yellow-500 pl-3">
                <a href="{{ route('ppic.index') }}" class="text-gray-500 hover:text-gray-900 mr-3">&larr;</a>
                Gantt Chart: {{ $project->nama_project }}
            </h1>
        </div>
        
        <div class="flex items-center space-x-4">
            <button onclick="exportPDF()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded shadow-sm text-sm flex items-center transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Cetak PDF
            </button>
            <!-- View Mode Switcher -->
            <div class="bg-white border border-gray-300 rounded overflow-hidden shadow-sm flex text-sm">
                <button type="button" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium" onclick="changeViewMode('Quarter Day')">Q-Day</button>
                <button type="button" class="px-3 py-1 border-l border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium" onclick="changeViewMode('Half Day')">H-Day</button>
                <button type="button" class="px-3 py-1 border-l border-gray-300 bg-blue-50 text-blue-700 font-bold" id="btn-day" onclick="changeViewMode('Day')">Day</button>
                <button type="button" class="px-3 py-1 border-l border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium" onclick="changeViewMode('Week')">Week</button>
                <button type="button" class="px-3 py-1 border-l border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium" onclick="changeViewMode('Month')">Month</button>
            </div>
        </div>
    </div>

    <div id="pdf-container">
        <!-- Info Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4 flex items-center justify-between">
        <div class="flex space-x-6">
            <div>
                <span class="block text-xs text-gray-500 uppercase tracking-wider">Mulai Proyek</span>
                <span class="font-bold text-gray-800">{{ date('d M Y', strtotime($project->start_project)) }}</span>
            </div>
            <div>
                <span class="block text-xs text-gray-500 uppercase tracking-wider">Target Selesai</span>
                <span class="font-bold text-gray-800">{{ date('d M Y', strtotime($project->target_project)) }}</span>
            </div>
        </div>
        <div class="flex items-center space-x-4 text-sm">
            <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-indigo-600 mr-2"></span> Subproject</div>
            <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span> Tugas</div>
        </div>
    </div>

    <!-- Gantt Chart Container -->
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mb-8">
        @if(count($ganttTasks) > 0)
            <div class="flex">
                <!-- Sidebar Kiri untuk Nama Task -->
                <div class="w-64 flex-shrink-0 border-r border-gray-200 bg-gray-50 z-10">
                    <!-- Header Sidebar -->
                    <div class="px-4 font-bold text-gray-700 text-sm flex items-center border-b border-gray-200 bg-gray-100" style="height: 50px;">
                        Nama Tugas / Subproject
                    </div>
                    <!-- Daftar Task (Tinggi 48px = bar_height 30 + padding 18) -->
                    @foreach($ganttTasks as $task)
                        <div class="px-4 text-sm font-medium text-gray-800 truncate flex items-center" style="height: 48px; border-bottom: 1px solid #ebeff2;" title="{{ $task['name'] }}">
                            @if(str_starts_with($task['id'], 'SUB-'))
                                <span class="w-2 h-2 rounded-full bg-indigo-600 mr-2 flex-shrink-0"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 flex-shrink-0 ml-4"></span>
                            @endif
                            <span class="truncate">{{ $task['name'] }}</span>
                        </div>
                    @endforeach
                </div>
                
                <!-- Chart SVG -->
                <div class="flex-1 overflow-x-auto">
                    <svg id="gantt"></svg>
                </div>
            </div>
        @else
            <div class="py-12 text-center text-gray-500">
                <p class="mb-2">Proyek ini belum memiliki Subproject atau Tugas.</p>
                <a href="{{ route('projects.show', $project->job_id) }}" class="text-blue-600 hover:underline">Tambahkan Subproject terlebih dahulu.</a>
            </div>
        @endif
    </div>
    </div>
</div>

<!-- Include Frappe Gantt JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tasks = @json($ganttTasks);
        
        if(tasks.length > 0) {
            // Inisialisasi Gantt
            window.gantt = new Gantt("#gantt", tasks, {
                header_height: 50,
                column_width: 30,
                step: 24,
                view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
                bar_height: 30,
                bar_corner_radius: 4,
                arrow_curve: 5,
                padding: 18,
                view_mode: 'Day',   
                date_format: 'YYYY-MM-DD',
                readonly: true,
                on_click: function (task) {
                    // Do nothing
                }
            });

            // Matikan fungsi drag pada Frappe Gantt secara paksa & jalankan highlight hari libur
            setTimeout(() => {
                const bars = document.querySelectorAll('.gantt .bar-wrapper');
                bars.forEach(bar => {
                    bar.addEventListener('mousedown', e => e.stopPropagation(), true);
                    bar.addEventListener('touchstart', e => e.stopPropagation(), true);
                    bar.addEventListener('pointerdown', e => e.stopPropagation(), true);
                });
                highlightWeekends();
            }, 500);
        }
    });

    // Fungsi untuk menandai (highlight) hari libur Sabtu & Minggu
    function highlightWeekends() {
        if(!window.gantt) return;
        
        // Hapus highlight lama jika ada
        document.querySelectorAll('.weekend-highlight').forEach(el => el.remove());
        
        // Hanya gambar highlight di mode Day agar selaras dengan kolom hari
        const viewMode = window.gantt.options.view_mode;
        if(viewMode !== 'Day') return;
        
        const dates = window.gantt.dates;
        const colWidth = window.gantt.options.column_width;
        
        const svg = document.getElementById('gantt');
        const gridGroup = svg.querySelector('.grid');
        if(!gridGroup || !dates) return;
        
        const header = svg.querySelector('.grid-header');
        const ns = 'http://www.w3.org/2000/svg';
        const svgHeight = svg.getAttribute('height') || svg.getBoundingClientRect().height;
        
        dates.forEach((date, i) => {
            let day = date.getDay();
            // 0 = Minggu, 6 = Sabtu
            if(day === 0 || day === 6) { 
                let rect = document.createElementNS(ns, 'rect');
                rect.setAttribute('x', i * colWidth);
                rect.setAttribute('y', 50); // 50px adalah header height
                rect.setAttribute('width', colWidth);
                rect.setAttribute('height', svgHeight - 50);
                rect.setAttribute('fill', 'rgba(239, 68, 68, 0.1)'); // Warna merah transparan
                rect.classList.add('weekend-highlight');
                // Masukkan di bawah grid-header agar tidak menutupi teks header
                gridGroup.insertBefore(rect, header);
            }
        });
    }

    // Fungsi untuk mengubah view mode (Hari, Minggu, Bulan)
    function changeViewMode(mode) {
        if(window.gantt) {
            window.gantt.change_view_mode(mode);
            
            // Re-render highlight hari libur setelah mengubah view mode
            setTimeout(highlightWeekends, 200);
            
            // Update styling button aktif
            const btns = document.querySelectorAll('button[type="button"]');
            btns.forEach(b => {
                if(b.innerText === mode || (b.innerText==='Q-Day' && mode==='Quarter Day') || (b.innerText==='H-Day' && mode==='Half Day')) {
                    b.className = b.className.replace('bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium', 'bg-blue-50 text-blue-700 font-bold');
                } else {
                    if(b.innerText === 'Q-Day' || b.innerText === 'H-Day' || b.innerText === 'Day' || b.innerText === 'Week' || b.innerText === 'Month') {
                        b.className = b.className.replace('bg-blue-50 text-blue-700 font-bold', 'bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium');
                    }
                }
            });
        }
    }

    function exportPDF() {
        window.print();
    }
</script>
@endsection

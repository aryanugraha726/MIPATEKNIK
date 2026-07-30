@extends('layout.app')

@section('content')
<!-- Include Frappe Gantt CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.css" />

<style>
    /* Custom styling for gantt chart to make it look premium */
    .gantt .bar-wrapper.bar-project .bar { fill: #F59E0B !important; }
    .gantt .bar-wrapper.bar-project .bar-progress { fill: #D97706 !important; }

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
            width: max-content; /* Membiarkan container selebar SVG agar browser me-resize kertas */
            min-width: 100%;
            margin: 0;
            padding: 10mm;
        }
        /* Hapus overflow agar browser bisa mendeteksi lebar asli dan me-shrink halaman */
        .overflow-x-auto, .overflow-hidden {
            overflow: visible !important;
        }
        .flex-1 {
            flex: none !important;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @page {
            size: landscape;
            margin: 0;
        }
    }
</style>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between print:hidden">
        <div>
            <h1 class="text-2xl font-bold flex items-center border-l-4 border-yellow-500 pl-3">
                <a href="{{ route('ppic.index') }}" class="text-gray-500 hover:text-gray-900 mr-3">&larr;</a>
                Gantt Chart: {{ $project->nama_project }}
            </h1>
        </div>
        
        <div class="flex items-center space-x-4">
            <button onclick="window.print()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded shadow-sm text-sm flex items-center transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Cetak PDF
            </button>
        </div>
    </div>

    <div id="pdf-container">
        <!-- Print Only Title -->
        <div class="hidden print:block text-center mb-4">
            <h1 class="text-base font-bold text-gray-900 uppercase">JADWAL PROYEK: {{ $project->nama_project }}</h1>
        </div>

        <!-- Info Card (Hidden during print to save space) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4 flex items-center justify-between print:hidden">
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
                    <!-- Header Sidebar (Kosong / Label) -->
                    <div class="h-[40px] border-b border-gray-200 bg-gray-100 flex items-center px-4 font-bold text-gray-700 text-sm">
                        Description
                    </div>
                    <!-- Daftar Task -->
                    <div class="py-[5px]"> <!-- Padding menyesuaikan offset Frappe Gantt (padding/2 = 5px) -->
                        @foreach($ganttTasks as $task)
                            @php
                                $isProject = str_starts_with($task['id'], 'Project-');
                                $isSub = str_starts_with($task['id'], 'SUB-') || str_starts_with($task['id'], 'Subproject');
                                $padding = $isProject ? '' : ($isSub ? '<span class="inline-block w-4"></span>' : '<span class="inline-block w-8"></span>');
                            @endphp
                            <div class="sidebar-row flex items-center px-4 border-b border-gray-100 hover:bg-gray-100 transition-colors" style="height: 30px;">
                            <div class="truncate">
                                {!! $padding !!}
                                @if($isProject)
                                    <span class="inline-block w-2 h-2 rounded-full bg-orange-500 mr-2"></span>
                                    <span class="font-bold text-gray-800 text-xs">{{ $task['name'] }}</span>
                                @elseif($isSub)
                                    <span class="inline-block w-2 h-2 rounded-full bg-indigo-500 mr-2"></span>
                                    <span class="font-semibold text-gray-700 text-xs">{{ $task['name'] }}</span>
                                @else
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                                    <span class="text-gray-600 text-xs">{{ $task['name'] }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div> <!-- Missing closing div for w-64 added back -->
            
            <!-- Chart SVG -->
            <div class="flex-1 overflow-x-auto">
                <svg id="gantt"></svg>
            </div>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                Belum ada subproject atau tugas untuk ditampilkan dalam Gantt Chart.
            </div>
        @endif
    </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tasks = @json($ganttTasks);
        
        if(tasks.length > 0) {
            // 1. Calculate exact date bounds
            let minStart = null;
            let maxEnd = null;
            for (let task of tasks) {
                let s = new Date(task.start);
                let e = new Date(task.end);
                if (!minStart || s < minStart) minStart = new Date(s);
                if (!maxEnd || e > maxEnd) maxEnd = new Date(e);
            }
            
            // Add padding to prevent horizontal slider/scrollbar (-1 day start, +1 day end)
            minStart.setDate(minStart.getDate() - 1);
            maxEnd.setDate(maxEnd.getDate() + 1);
            let totalDays = Math.round((maxEnd - minStart) / (1000 * 60 * 60 * 24));

            // 2. Calculate exact available width to perfectly stretch the chart
            // The container is inside max-w-7xl, minus the 256px sidebar
            const wrapper = document.querySelector('.max-w-7xl');
            let availableWidth = (wrapper ? wrapper.clientWidth : window.innerWidth) - 256 - 40; // 40px for safety padding
            
            let adaptiveColumnWidth = 30; // default
            if (totalDays > 0 && availableWidth > 100) {
                let calcWidth = Math.floor(availableWidth / totalDays);
                adaptiveColumnWidth = Math.max(30, calcWidth); // Minimum 30px, no max limit so it always stretches
            }

            // 3. Patch Frappe Gantt prototype BEFORE initialization!
            // This guarantees that the chart obeys our sizes during the very first render.
            if (typeof Gantt !== 'undefined') {
                const originalUpdateViewScale = Gantt.prototype.update_view_scale;
                Gantt.prototype.update_view_scale = function(mode) {
                    originalUpdateViewScale.call(this, mode);
                    if (mode === 'Day') {
                        // Force our stretched column width
                        this.options.column_width = adaptiveColumnWidth;
                    }
                };

                Gantt.prototype.setup_gantt_dates = function() {
                    // Force our precise project bounds
                    this.gantt_start = new Date(minStart);
                    this.gantt_end = new Date(maxEnd);
                };
            }

            // 4. Initialize Gantt Chart
            window.gantt = new Gantt("#gantt", tasks, {
                header_height: 40,
                column_width: adaptiveColumnWidth,
                step: 24,
                view_modes: ['Day'],
                bar_height: 20, // Slim height
                bar_corner_radius: 4,
                arrow_curve: 5,
                padding: 10,
                view_mode: 'Day',   
                date_format: 'YYYY-MM-DD',
                readonly: true,
                on_click: function (task) {}
            });

            // FIX: Frappe Gantt arbitrarily adds +100px to the SVG height for legacy tooltips.
            // We forcefully revert the SVG height to the exact mathematical grid height.
            let exactGridHeight = 40 + 10 + (30 * tasks.length); 
            document.querySelector('#gantt').setAttribute('height', exactGridHeight);

            // 5. Matikan fungsi drag pada Frappe Gantt secara paksa & jalankan highlight hari libur
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
            // 0 = Minggu
            if(day === 0) { 
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

    function exportPDF() {
        window.print();
    }
</script>
@endsection

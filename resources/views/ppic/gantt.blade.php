@extends('layout.app')

@section('content')
<style>
    .day-cell {
        cursor: pointer;
        transition: all 0.1s ease;
        border-right: 1px solid #f3f4f6;
        text-align: center;
        min-width: 24px;
        max-width: 24px;
        height: 24px;
        padding: 0;
    }
    .day-cell:hover { background-color: #dbeafe; }
    
    /* Subproject colors */
    .subproject-row .day-cell.selected-range { background-color: #6366f1; } /* indigo-500 */
    .subproject-row .day-cell.selected-start, .subproject-row .day-cell.selected-end { background-color: #4338ca; } /* indigo-700 */
    
    /* Tugas colors */
    .tugas-row .day-cell.selected-range { background-color: #10b981; } /* emerald-500 */
    .tugas-row .day-cell.selected-start, .tugas-row .day-cell.selected-end { background-color: #047857; } /* emerald-700 */

    /* Existing task colors (readonly) */
    .existing-subproject { background-color: #818cf8 !important; cursor: not-allowed; border-radius:2px;}
    .existing-tugas { background-color: #34d399 !important; cursor: not-allowed; border-radius:2px;}
    
    .sticky-col-1 { position: sticky; left: 0px; z-index: 20; background-color: inherit; border-right: 2px solid #e5e7eb;}
    .sticky-col-2 { position: sticky; left: 200px; z-index: 20; background-color: inherit; }
    .sticky-col-3 { position: sticky; left: 320px; z-index: 20; background-color: inherit; border-right: 2px solid #e5e7eb;}
    .sticky-col-4 { position: sticky; left: 440px; z-index: 20; background-color: inherit; }
    .sticky-col-5 { position: sticky; left: 490px; z-index: 20; background-color: inherit; border-right: 2px solid #e5e7eb;}
    
</style>

<div class="max-w-[95%] mx-auto bg-white p-4 rounded-xl shadow border border-gray-100 text-sm">
    <!-- Header -->
    <div class="mb-4 flex justify-between items-end border-b pb-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800 flex items-center border-l-4 border-yellow-500 pl-3">
                <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-gray-900 mr-3">&larr;</a>
                Gantt & Timeline: {{ $project->nama_project }}
            </h1>
            <p class="text-gray-500 text-xs mt-1 ml-5">Tentukan Start & Target. Tambahkan Subproyek & Tugas.</p>
        </div>
        <div class="flex gap-3 items-center">
            @if(session('success'))
                <span class="text-green-600 font-bold text-xs bg-green-100 px-2 py-1 rounded">{{ session('success') }}</span>
            @endif
            @if(session('error'))
                <span class="text-red-600 font-bold text-xs bg-red-100 px-2 py-1 rounded">{{ session('error') }}</span>
            @endif
            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Mulai</label>
                <input type="date" id="project_start" value="{{ date('Y-m-d', strtotime($project->start_project)) }}" readonly class="border border-gray-200 px-2 py-1 rounded text-xs bg-gray-50 text-gray-500 font-bold">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Target</label>
                <input type="date" id="project_end" value="{{ date('Y-m-d', strtotime($project->target_project)) }}" readonly class="border border-gray-200 px-2 py-1 rounded text-xs bg-gray-50 text-gray-500 font-bold">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <button type="button" onclick="openSubproyekModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-3 rounded shadow-sm text-xs transition">
            + Tambah Subproyek
        </button>
    </div>

    <form action="{{ route('subprojects.bulkStore', $project->job_id) }}" method="POST" id="submit_form">
        @csrf
        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-inner bg-gray-50 pb-2 relative" style="max-height: 65vh;">
            <table class="w-full text-left border-collapse min-w-max text-xs" id="timeline_table">
                <thead class="sticky top-0 z-30" id="table_thead">
                    <!-- Header akan diisi oleh JS -->
                </thead>
                <tbody id="table_body" class="divide-y divide-gray-200 bg-white">
                    <!-- Data Existing (Interactive) -->
                    @foreach($project->subprojects as $i => $sub)
                        <tr class="bg-indigo-50/30 subproject-row new-row text-xs" data-sub-idx="exist_s_{{ $i }}" data-row-id="exist_s_{{ $i }}">
                            <td class="p-0 border-r border-gray-200 sticky-col-1 w-[200px]">
                                <div class="flex items-center w-full h-6">
                                    <span class="text-indigo-600 font-bold mx-1">■</span>
                                    <input type="hidden" name="subproyek[exist_s_{{ $i }}][id]" value="{{ $sub->subproject_id }}">
                                    <input type="text" name="subproyek[exist_s_{{ $i }}][nama]" required value="{{ $sub->nama_subproject }}" placeholder="Nama Subproyek..." class="w-full h-full px-1 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs font-bold text-indigo-800">
                                </div>
                                <input type="hidden" name="subproyek[exist_s_{{ $i }}][mulai]" class="start-date-input" value="{{ date('Y-m-d', strtotime($sub->start_subproject)) }}">
                                <input type="hidden" name="subproyek[exist_s_{{ $i }}][selesai]" class="end-date-input" value="{{ date('Y-m-d', strtotime($sub->target_subproject)) }}">
                            </td>
                            <td class="p-0 border-r border-gray-200 sticky-col-2 w-[120px]">
                                <select name="subproyek[exist_s_{{ $i }}][management_id]" required class="no-search gantt-select w-full h-6 py-0 px-0.5 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs text-gray-700">
                                    <option value="">- P.Jawab -</option>
                                    @foreach($managements as $m)
                                        <option value="{{ $m->management_id }}" data-short="{{ $m->karyawan->nm_karyawan ?? 'Unknown' }}" data-full="{{ $m->karyawan->nm_karyawan ?? 'Unknown' }} ({{ $subprojectCounts[$m->management_id] ?? 0 }} sub)" {{ $sub->management_id == $m->management_id ? 'selected' : '' }}>{{ $m->karyawan->nm_karyawan ?? 'Unknown' }} ({{ $subprojectCounts[$m->management_id] ?? 0 }} sub)</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-0 border-r border-gray-200 sticky-col-3 w-[120px]">
                                <select name="subproyek[exist_s_{{ $i }}][id_karyawan]" class="no-search gantt-select w-full h-6 py-0 px-0.5 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs text-gray-700">
                                    <option value="">- Operator -</option>
                                    @foreach($karyawans as $k)
                                        <option value="{{ $k->id_karyawan }}" data-short="{{ $k->nm_karyawan }}" data-full="{{ $k->nm_karyawan }} ({{ $tugasCounts[$k->id_karyawan] ?? 0 }} tugas)" {{ $sub->id_karyawan == $k->id_karyawan ? 'selected' : '' }}>{{ $k->nm_karyawan }} ({{ $tugasCounts[$k->id_karyawan] ?? 0 }} tugas)</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-0 border-r border-gray-200 sticky-col-4 w-[50px] text-center text-xs">-</td>
                            <td class="p-0 border-r border-gray-200 sticky-col-5 w-[50px] text-center text-xs">-</td>
                            
                            <td class="existing-timeline-placeholder" data-start="{{ date('Y-m-d', strtotime($sub->start_subproject)) }}" data-end="{{ date('Y-m-d', strtotime($sub->target_subproject)) }}" data-type="subproject" data-row-id="exist_s_{{ $i }}"></td>
                        </tr>
                        
                        @foreach($sub->tugas as $j => $tugas)
                            <tr class="bg-emerald-50/20 tugas-row new-row child-of-exist_s_{{ $i }} text-xs" data-row-id="exist_t_{{ $i }}_{{ $j }}">
                                <td class="p-0 pl-4 border-r border-gray-200 sticky-col-1 w-[200px]">
                                    <div class="flex items-center w-full h-6">
                                        <span class="text-emerald-500 font-bold mr-1">↳</span>
                                        <input type="hidden" name="subproyek[exist_s_{{ $i }}][tugas][exist_t_{{ $j }}][id]" value="{{ $tugas->tugas_id }}">
                                        <input type="text" name="subproyek[exist_s_{{ $i }}][tugas][exist_t_{{ $j }}][nama]" required value="{{ $tugas->tugas }}" placeholder="Tugas..." class="w-full h-full px-1 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs text-gray-700">
                                    </div>
                                    <input type="hidden" name="subproyek[exist_s_{{ $i }}][tugas][exist_t_{{ $j }}][mulai]" class="start-date-input" value="{{ date('Y-m-d', strtotime($tugas->start_tugas)) }}">
                                    <input type="hidden" name="subproyek[exist_s_{{ $i }}][tugas][exist_t_{{ $j }}][selesai]" class="end-date-input" value="{{ date('Y-m-d', strtotime($tugas->target_tugas)) }}">
                                </td>
                                <td class="p-0 border-r border-gray-200 sticky-col-2 w-[120px] text-center text-gray-400 text-xs">-</td>
                                <td class="p-0 border-r border-gray-200 sticky-col-3 w-[120px]">
                                    <select name="subproyek[exist_s_{{ $i }}][tugas][exist_t_{{ $j }}][id_karyawan]" required class="no-search gantt-select w-full h-6 py-0 px-0.5 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs text-gray-700">
                                        <option value="">- Operator -</option>
                                        @foreach($karyawans as $k)
                                            <option value="{{ $k->id_karyawan }}" data-short="{{ $k->nm_karyawan }}" data-full="{{ $k->nm_karyawan }} ({{ $tugasCounts[$k->id_karyawan] ?? 0 }} tugas)" {{ $tugas->id_karyawan == $k->id_karyawan ? 'selected' : '' }}>{{ $k->nm_karyawan }} ({{ $tugasCounts[$k->id_karyawan] ?? 0 }} tugas)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-0 border-r border-gray-200 sticky-col-4 w-[50px]">
                                    <input type="number" name="subproyek[exist_s_{{ $i }}][tugas][exist_t_{{ $j }}][qty]" value="{{ $tugas->qty }}" placeholder="Qty" class="w-full h-6 py-0 px-1 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs text-center">
                                </td>
                                <td class="p-0 border-r border-gray-200 sticky-col-5 w-[50px]">
                                    <input type="text" name="subproyek[exist_s_{{ $i }}][tugas][exist_t_{{ $j }}][unit]" value="{{ $tugas->unit }}" placeholder="Unit" class="w-full h-6 py-0 px-1 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs text-center">
                                </td>
                                
                                <td class="existing-timeline-placeholder" data-start="{{ date('Y-m-d', strtotime($tugas->start_tugas)) }}" data-end="{{ date('Y-m-d', strtotime($tugas->target_tugas)) }}" data-type="tugas" data-row-id="exist_t_{{ $i }}_{{ $j }}"></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition text-sm">
                Simpan Subproyek & Tugas Baru
            </button>
        </div>
    </form>
</div>

<!-- ========== MODAL TAMBAH SUBPROYEK ========== -->
<div id="modal-subproyek" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSubproyekModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <!-- Header -->
        <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
            <h2 class="text-white font-bold text-base flex items-center gap-2">
                <span class="text-lg">■</span> Tambah Subproyek Baru
            </h2>
            <button onclick="closeSubproyekModal()" class="text-white/70 hover:text-white text-xl font-bold">&times;</button>
        </div>
        <!-- Body -->
        <div class="px-6 py-5 space-y-4 overflow-y-auto max-h-[70vh]">

            @if(count($workScope) > 0)
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ambil dari Work Scope WOR</label>
                <select id="modal_sub_wor" class="no-search block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none bg-white">
                    <option value="">-- Pilih Work Scope (opsional) --</option>
                    @foreach($workScope as $idx => $ws)
                        <option value="{{ $idx }}" data-part="{{ $ws['part_description'] ?? '' }}">{{ $ws['part_description'] ?? 'Item '.($idx+1) }} – {{ $ws['qty'] ?? '' }} {{ $ws['unit'] ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Subproyek <span class="text-red-500">*</span></label>
                <input type="text" id="modal_sub_nama" placeholder="Nama subproyek..." class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mulai</label>
                    <input type="date" id="modal_sub_mulai" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Target</label>
                    <input type="date" id="modal_sub_selesai" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Durasi (Hari)</label>
                    <input type="number" id="modal_sub_durasi" min="1" value="1" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Penanggung Jawab <span class="text-red-500">*</span></label>
                <select id="modal_sub_management" class="no-search block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none bg-white">
                    <option value="">-- Pilih Penanggung Jawab --</option>
                    @foreach($managements as $m)
                        <option value="{{ $m->management_id }}">{{ $m->karyawan->nm_karyawan ?? 'Unknown' }} ({{ $subprojectCounts[$m->management_id] ?? 0 }} sub aktif)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Operator (Opsional)</label>
                <select id="modal_sub_operator" class="no-search block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none bg-white">
                    <option value="">-- Pilih Operator --</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id_karyawan }}">{{ $k->nm_karyawan }} ({{ ($k->subproject_count ?? 0) + ($k->tugas_count ?? 0) }} tugas aktif)</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeSubproyekModal()" class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 font-medium transition">Batal</button>
            <button onclick="submitSubproyekModal()" class="px-5 py-2 rounded-lg text-sm text-white bg-indigo-600 hover:bg-indigo-700 font-bold shadow transition">+ Tambahkan ke Tabel</button>
        </div>
    </div>
</div>

<!-- ========== MODAL TAMBAH TUGAS ========== -->
<div id="modal-tugas" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeTugasModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <!-- Header -->
        <div class="bg-emerald-600 px-6 py-4 flex items-center justify-between">
            <h2 class="text-white font-bold text-base flex items-center gap-2">
                <span class="text-lg">↳</span> Tambah Tugas Baru
            </h2>
            <button onclick="closeTugasModal()" class="text-white/70 hover:text-white text-xl font-bold">&times;</button>
        </div>
        <!-- Body -->
        <div class="px-6 py-5 space-y-4 overflow-y-auto max-h-[70vh]">

            @if(count($workScope) > 0)
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ambil dari Work Scope WOR</label>
                <select id="modal_tgs_wor" class="no-search block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white">
                    <option value="">-- Pilih Work Scope (opsional) --</option>
                    @foreach($workScope as $idx => $ws)
                        <option value="{{ $idx }}" data-part="{{ $ws['part_description'] ?? '' }}" data-qty="{{ $ws['qty'] ?? '' }}" data-unit="{{ $ws['unit'] ?? '' }}">{{ $ws['part_description'] ?? 'Item '.($idx+1) }} – {{ $ws['qty'] ?? '' }} {{ $ws['unit'] ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Tugas <span class="text-red-500">*</span></label>
                <input type="text" id="modal_tgs_nama" placeholder="Rincian tugas..." class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Qty</label>
                    <input type="text" id="modal_tgs_qty" placeholder="misal: 10" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Unit</label>
                    <input type="text" id="modal_tgs_unit" placeholder="misal: pcs" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mulai</label>
                    <input type="date" id="modal_tgs_mulai" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Target</label>
                    <input type="date" id="modal_tgs_selesai" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Durasi (Hari)</label>
                    <input type="number" id="modal_tgs_durasi" min="1" value="1" class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-xs text-gray-500 mb-3">Pilih <strong>Operator</strong> jika dikerjakan internal, atau <strong>Vendor</strong> jika pihak ketiga. Bisa dikosongkan.</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Operator (Internal)</label>
                        <select id="modal_tgs_operator" class="no-search block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white">
                            <option value="">-- Pilih Operator --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id_karyawan }}">{{ $k->nm_karyawan }} ({{ ($k->subproject_count ?? 0) + ($k->tugas_count ?? 0) }} aktif)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Vendor (Eksternal)</label>
                        <select id="modal_tgs_vendor" class="no-search block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id_vendor }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeTugasModal()" class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 font-medium transition">Batal</button>
            <button onclick="submitTugasModal()" class="px-5 py-2 rounded-lg text-sm text-white bg-emerald-600 hover:bg-emerald-700 font-bold shadow transition">+ Tambahkan ke Tabel</button>
        </div>
    </div>
</div>

<script>
    let subIdx = 0;
    
    document.addEventListener("DOMContentLoaded", () => {
        renderHeaderAndExisting();
    });

    function getDatesInRange(startDate, endDate) {
        const date = new Date(startDate.getTime());
        const dates = [];
        while (date <= endDate) {
            dates.push(new Date(date));
            date.setDate(date.getDate() + 1);
        }
        return dates;
    }

    function renderHeaderAndExisting() {
        const startDate = new Date(document.getElementById('project_start').value);
        const endDate = new Date(document.getElementById('project_end').value);
        const datesArray = getDatesInRange(startDate, endDate);
        
        let monthGroups = [];
        let currentMonth = datesArray[0].getMonth();
        let currentYear = datesArray[0].getFullYear();
        let currentCount = 0;
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        
        datesArray.forEach(date => {
            if (date.getMonth() === currentMonth) {
                currentCount++;
            } else {
                monthGroups.push({ name: `${monthNames[currentMonth]} ${currentYear}`, count: currentCount });
                currentMonth = date.getMonth();
                currentYear = date.getFullYear();
                currentCount = 1;
            }
        });
        if (currentCount > 0) {
            monthGroups.push({ name: `${monthNames[currentMonth]} ${currentYear}`, count: currentCount });
        }

        const thead = document.getElementById('table_thead');
        
        // Baris Pertama (Bulan)
        let monthHeaderHTML = `<tr class="bg-gray-100 border-b border-gray-300 text-xs text-gray-700 font-bold">`;
        // Bagian lengket yang kosong untuk kolom 1-5 (digabung jadi 1 sel besar jika mungkin, atau per sel tapi kosong)
        monthHeaderHTML += `
            <th class="p-1 border-r border-gray-300 sticky-col-1 bg-gray-200 z-30" style="left:0" colspan="5"></th>
        `;
        monthGroups.forEach(m => {
            monthHeaderHTML += `<th class="p-1 border-r border-gray-300 bg-gray-200 text-center font-bold text-gray-800 z-10" colspan="${m.count}">${m.name}</th>`;
        });
        monthHeaderHTML += `<th class="p-1 bg-gray-200 border-gray-300 z-10"></th></tr>`;

        // Baris Kedua (Hari)
        let dayHeaderHTML = `<tr class="bg-gray-100 border-b border-gray-300 text-[10px] uppercase text-gray-700">`;
        dayHeaderHTML += `
            <th class="p-2 font-bold w-[200px] border-r border-gray-300 sticky-col-1 bg-gray-200 z-30" style="left:0;">Keterangan</th>
            <th class="p-2 font-bold w-[120px] border-r border-gray-300 sticky-col-2 bg-gray-200 z-30" style="left:200px;">Penangung Jawab</th>
            <th class="p-2 font-bold w-[120px] border-r border-gray-300 sticky-col-3 bg-gray-200 z-30" style="left:320px;">Operator</th>
            <th class="p-2 font-bold w-[50px] border-r border-gray-300 sticky-col-4 bg-gray-200 text-center z-30" style="left:440px;">Qty</th>
            <th class="p-2 font-bold w-[50px] border-r border-gray-400 sticky-col-5 bg-gray-200 text-center z-30" style="left:490px;">Unit</th>
        `;
        
        datesArray.forEach(date => {
            const dayNum = date.getDate();
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            const textCol = isWeekend ? 'text-red-500' : 'text-gray-700';
            dayHeaderHTML += `<th class="font-medium text-center border-r border-gray-200 ${textCol}" style="min-width:24px; max-width:24px; height:24px; padding:0;" title="${date.toDateString()}">${dayNum}</th>`;
        });
        dayHeaderHTML += `<th class="p-2 font-bold text-center w-16">Aksi</th></tr>`;
        
        thead.innerHTML = monthHeaderHTML + dayHeaderHTML;

        // 2. Render Existing Timeline Cells
        document.querySelectorAll('.existing-timeline-placeholder').forEach(td => {
            const row = td.parentElement;
            const startStr = td.dataset.start;
            const endStr = td.dataset.end;
            const type = td.dataset.type;
            const rowId = td.dataset.rowId;
            
            const startD = new Date(startStr);
            const endD = new Date(endStr);
            
            rowStates[rowId] = { start: startD, end: endD, type: type };
            
            td.remove();
            
            let cellsHTML = '';
            datesArray.forEach(date => {
                const isoDate = date.toISOString().split('T')[0];
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;
                const bgClass = isWeekend ? 'bg-red-100' : '';
                
                cellsHTML += `<td class="p-0 day-cell ${bgClass}" data-date="${isoDate}" onclick="handleCellClick(this, '${rowId}')"><div class="h-[24px] w-full"></div></td>`;
            });
            
            if (type === 'subproject') {
                cellsHTML += `
                    <td class="p-1 text-center border-l border-gray-200 whitespace-nowrap">
                        <button type="button" onclick="addTugas('${rowId}')" class="text-[10px] bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold px-1.5 py-0.5 rounded mr-1">+ Tugas</button>
                        <button type="button" onclick="hapusRow(this, '${rowId}')" class="text-red-500 hover:text-red-700 font-bold px-1">&times;</button>
                    </td>
                `;
            } else {
                cellsHTML += `
                    <td class="p-1 text-center border-l border-gray-200">
                        <button type="button" onclick="hapusRow(this, '${rowId}')" class="text-red-400 hover:text-red-700 font-bold px-2">&times;</button>
                    </td>
                `;
            }
            
            row.insertAdjacentHTML('beforeend', cellsHTML);
            updateRowVisuals(rowId);
        });
    }

    const rowStates = {};

    function getCurrentDatesArray() {
        const startDate = new Date(document.getElementById('project_start').value);
        const endDate = new Date(document.getElementById('project_end').value);
        return getDatesInRange(startDate, endDate);
    }

    // ============================================================
    // MODAL SUBPROYEK — event listeners
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        // WOR autocomplete subproyek
        const worSub = document.getElementById('modal_sub_wor');
        if (worSub) {
            worSub.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (opt.value !== '') {
                    document.getElementById('modal_sub_nama').value = opt.getAttribute('data-part') || '';
                }
            });
        }
        // Durasi sync subproyek (mulai + durasi → selesai, atau selesai → durasi)
        const subMulai   = document.getElementById('modal_sub_mulai');
        const subSelesai = document.getElementById('modal_sub_selesai');
        const subDurasi  = document.getElementById('modal_sub_durasi');
        if (subMulai && subDurasi && subSelesai) {
            subDurasi.addEventListener('change', function() {
                if (subMulai.value && this.value) {
                    const d = new Date(subMulai.value);
                    d.setDate(d.getDate() + parseInt(this.value) - 1);
                    subSelesai.value = d.toISOString().split('T')[0];
                }
            });
            subMulai.addEventListener('change', function() {
                if (this.value && subDurasi.value) {
                    const d = new Date(this.value);
                    d.setDate(d.getDate() + parseInt(subDurasi.value) - 1);
                    subSelesai.value = d.toISOString().split('T')[0];
                }
            });
            subSelesai.addEventListener('change', function() {
                if (subMulai.value && this.value) {
                    const diff = Math.round((new Date(this.value) - new Date(subMulai.value)) / 86400000) + 1;
                    if (diff > 0) subDurasi.value = diff;
                }
            });
        }
        // WOR autocomplete tugas
        const worTgs = document.getElementById('modal_tgs_wor');
        if (worTgs) {
            worTgs.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (opt.value !== '') {
                    document.getElementById('modal_tgs_nama').value = opt.getAttribute('data-part') || '';
                    document.getElementById('modal_tgs_qty').value  = opt.getAttribute('data-qty')  || '';
                    document.getElementById('modal_tgs_unit').value = opt.getAttribute('data-unit') || '';
                }
            });
        }
        // Durasi sync tugas
        const tgsMulai   = document.getElementById('modal_tgs_mulai');
        const tgsSelesai = document.getElementById('modal_tgs_selesai');
        const tgsDurasi  = document.getElementById('modal_tgs_durasi');
        if (tgsMulai && tgsDurasi && tgsSelesai) {
            tgsDurasi.addEventListener('change', function() {
                if (tgsMulai.value && this.value) {
                    const d = new Date(tgsMulai.value);
                    d.setDate(d.getDate() + parseInt(this.value) - 1);
                    tgsSelesai.value = d.toISOString().split('T')[0];
                }
            });
            tgsMulai.addEventListener('change', function() {
                if (this.value && tgsDurasi.value) {
                    const d = new Date(this.value);
                    d.setDate(d.getDate() + parseInt(tgsDurasi.value) - 1);
                    tgsSelesai.value = d.toISOString().split('T')[0];
                }
            });
            tgsSelesai.addEventListener('change', function() {
                if (tgsMulai.value && this.value) {
                    const diff = Math.round((new Date(this.value) - new Date(tgsMulai.value)) / 86400000) + 1;
                    if (diff > 0) tgsDurasi.value = diff;
                }
            });
        }
    });

    // ============================================================
    // MODAL SUBPROYEK — open / close / submit
    // ============================================================
    function openSubproyekModal() {
        document.getElementById('modal_sub_nama').value = '';
        document.getElementById('modal_sub_mulai').value = '';
        document.getElementById('modal_sub_selesai').value = '';
        document.getElementById('modal_sub_durasi').value = 1;
        document.getElementById('modal_sub_management').value = '';
        document.getElementById('modal_sub_operator').value = '';
        const worEl = document.getElementById('modal_sub_wor');
        if (worEl) worEl.value = '';
        document.getElementById('modal-subproyek').classList.remove('hidden');
        setTimeout(() => document.getElementById('modal_sub_nama').focus(), 100);
    }
    function closeSubproyekModal() {
        document.getElementById('modal-subproyek').classList.add('hidden');
    }
    function submitSubproyekModal() {
        const nama       = document.getElementById('modal_sub_nama').value.trim();
        const mulai      = document.getElementById('modal_sub_mulai').value;
        const selesai    = document.getElementById('modal_sub_selesai').value;
        const management = document.getElementById('modal_sub_management').value;
        const operator   = document.getElementById('modal_sub_operator').value;

        if (!nama)       { alert('Nama Subproyek wajib diisi.'); return; }
        if (!management) { alert('Penanggung Jawab wajib dipilih.'); return; }

        subIdx++;
        const tbody = document.getElementById('table_body');
        const datesArray = getCurrentDatesArray();
        const row = document.createElement('tr');
        row.className = 'bg-indigo-50/30 subproject-row new-row text-xs';
        row.dataset.subIdx = 's_' + subIdx;
        row.dataset.rowId  = 's_' + subIdx;
        rowStates['s_' + subIdx] = { start: mulai ? new Date(mulai) : null, end: selesai ? new Date(selesai) : null, type: 'subproject' };

        const mgmtSel  = document.getElementById('modal_sub_management');
        const mgmtText = mgmtSel.options[mgmtSel.selectedIndex]?.text || '';
        const opSel    = document.getElementById('modal_sub_operator');
        const opText   = opSel.options[opSel.selectedIndex]?.text || '';

        let html = `
            <td class="p-0 border-r border-gray-200 sticky-col-1 w-[200px]">
                <div class="flex items-center w-full h-6">
                    <span class="text-indigo-600 font-bold mx-1">&#9632;</span>
                    <input type="hidden" name="subproyek[s_${subIdx}][management_id]" value="${management}">
                    <input type="hidden" name="subproyek[s_${subIdx}][id_karyawan]" value="${operator}">
                    <input type="hidden" name="subproyek[s_${subIdx}][mulai]" class="start-date-input" value="${mulai}">
                    <input type="hidden" name="subproyek[s_${subIdx}][selesai]" class="end-date-input" value="${selesai}">
                    <input type="text" name="subproyek[s_${subIdx}][nama]" required value="${nama}" placeholder="Nama Subproyek..." class="w-full h-full px-1 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs font-bold text-indigo-800">
                </div>
            </td>
            <td class="p-0 border-r border-gray-200 sticky-col-2 w-[120px] text-xs px-1 text-gray-700 truncate" title="${mgmtText}">${mgmtText.split('(')[0].trim()}</td>
            <td class="p-0 border-r border-gray-200 sticky-col-3 w-[120px] text-xs px-1 text-gray-500 truncate" title="${opText}">${operator ? opText.split('(')[0].trim() : '-'}</td>
            <td class="p-0 border-r border-gray-200 sticky-col-4 w-[50px] text-center text-xs">-</td>
            <td class="p-0 border-r border-gray-200 sticky-col-5 w-[50px] text-center text-xs">-</td>
        `;

        datesArray.forEach(date => {
            const isoDate = date.toISOString().split('T')[0];
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            html += `<td class="p-0 day-cell ${isWeekend ? 'bg-red-100' : ''}" data-date="${isoDate}" onclick="handleCellClick(this, 's_${subIdx}')"><div class="h-[24px] w-full"></div></td>`;
        });

        html += `
            <td class="p-1 text-center border-l border-gray-200 whitespace-nowrap">
                <button type="button" onclick="openTugasModal('s_${subIdx}')" class="text-[10px] bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold px-1.5 py-0.5 rounded mr-1">+ Tugas</button>
                <button type="button" onclick="hapusRow(this, 's_${subIdx}')" class="text-red-500 hover:text-red-700 font-bold px-1">&times;</button>
            </td>
        `;

        row.innerHTML = html;
        tbody.appendChild(row);
        if (mulai && selesai) updateRowVisuals('s_' + subIdx);
        closeSubproyekModal();
    }

    // ============================================================
    // MODAL TUGAS — open / close / submit
    // ============================================================
    let tugasGlobalIdx = 0;
    let _modalTugasParentIdx = null;

    function addTugas(parentSubIdx) { openTugasModal(parentSubIdx); }

    function openTugasModal(parentSubIdx) {
        _modalTugasParentIdx = parentSubIdx;
        document.getElementById('modal_tgs_nama').value    = '';
        document.getElementById('modal_tgs_qty').value     = '';
        document.getElementById('modal_tgs_unit').value    = '';
        document.getElementById('modal_tgs_mulai').value   = '';
        document.getElementById('modal_tgs_selesai').value = '';
        document.getElementById('modal_tgs_durasi').value  = 1;
        document.getElementById('modal_tgs_operator').value = '';
        document.getElementById('modal_tgs_vendor').value   = '';
        const worEl = document.getElementById('modal_tgs_wor');
        if (worEl) worEl.value = '';
        document.getElementById('modal-tugas').classList.remove('hidden');
        setTimeout(() => document.getElementById('modal_tgs_nama').focus(), 100);
    }
    function closeTugasModal() {
        document.getElementById('modal-tugas').classList.add('hidden');
    }
    function submitTugasModal() {
        const parentSubIdx = _modalTugasParentIdx;
        const nama     = document.getElementById('modal_tgs_nama').value.trim();
        const qty      = document.getElementById('modal_tgs_qty').value.trim();
        const unit     = document.getElementById('modal_tgs_unit').value.trim();
        const mulai    = document.getElementById('modal_tgs_mulai').value;
        const selesai  = document.getElementById('modal_tgs_selesai').value;
        const operator = document.getElementById('modal_tgs_operator').value;
        const vendor   = document.getElementById('modal_tgs_vendor').value;

        if (!nama) { alert('Nama Tugas wajib diisi.'); return; }

        const parentRow = document.querySelector(`.subproject-row[data-sub-idx="${parentSubIdx}"]`);
        if (!parentRow) return;

        tugasGlobalIdx++;
        const datesArray = getCurrentDatesArray();
        const tr = document.createElement('tr');
        tr.className = 'bg-emerald-50/20 tugas-row new-row child-of-' + parentSubIdx + ' text-xs';
        tr.dataset.rowId = 't_' + tugasGlobalIdx;
        rowStates['t_' + tugasGlobalIdx] = { start: mulai ? new Date(mulai) : null, end: selesai ? new Date(selesai) : null, type: 'tugas' };

        const opSel   = document.getElementById('modal_tgs_operator');
        const opText  = opSel.options[opSel.selectedIndex]?.text || '';
        const vndSel  = document.getElementById('modal_tgs_vendor');
        const vndText = vndSel.options[vndSel.selectedIndex]?.text || '';
        const pelaksana = operator ? opText.split('(')[0].trim() : (vendor ? vndText : '-');

        let html = `
            <td class="p-0 pl-4 border-r border-gray-200 sticky-col-1 w-[200px]">
                <div class="flex items-center w-full h-6">
                    <span class="text-emerald-500 font-bold mr-1">&#8627;</span>
                    <input type="hidden" name="subproyek[${parentSubIdx}][tugas][t_${tugasGlobalIdx}][mulai]" class="start-date-input" value="${mulai}">
                    <input type="hidden" name="subproyek[${parentSubIdx}][tugas][t_${tugasGlobalIdx}][selesai]" class="end-date-input" value="${selesai}">
                    <input type="hidden" name="subproyek[${parentSubIdx}][tugas][t_${tugasGlobalIdx}][id_karyawan]" value="${operator}">
                    <input type="hidden" name="subproyek[${parentSubIdx}][tugas][t_${tugasGlobalIdx}][id_vendor]" value="${vendor}">
                    <input type="hidden" name="subproyek[${parentSubIdx}][tugas][t_${tugasGlobalIdx}][qty]" value="${qty}">
                    <input type="hidden" name="subproyek[${parentSubIdx}][tugas][t_${tugasGlobalIdx}][unit]" value="${unit}">
                    <input type="text" name="subproyek[${parentSubIdx}][tugas][t_${tugasGlobalIdx}][nama]" required value="${nama}" placeholder="Tugas..." class="w-full h-full px-1 border-0 focus:outline-none focus:bg-white/50 bg-transparent text-xs text-gray-700">
                </div>
            </td>
            <td class="p-0 border-r border-gray-200 sticky-col-2 w-[120px] text-xs px-1 text-gray-400 text-center">-</td>
            <td class="p-0 border-r border-gray-200 sticky-col-3 w-[120px] text-xs px-1 text-gray-600 truncate" title="${pelaksana}">${pelaksana}</td>
            <td class="p-0 border-r border-gray-200 sticky-col-4 w-[50px] text-xs text-center text-gray-600">${qty || '-'}</td>
            <td class="p-0 border-r border-gray-200 sticky-col-5 w-[50px] text-xs text-center text-gray-600">${unit || '-'}</td>
        `;

        datesArray.forEach(date => {
            const isoDate = date.toISOString().split('T')[0];
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            html += `<td class="p-0 day-cell ${isWeekend ? 'bg-red-100' : ''}" data-date="${isoDate}" onclick="handleCellClick(this, 't_${tugasGlobalIdx}')"><div class="h-[24px] w-full"></div></td>`;
        });

        html += `
            <td class="p-1 text-center border-l border-gray-200">
                <button type="button" onclick="hapusRow(this, 't_${tugasGlobalIdx}')" class="text-red-400 hover:text-red-700 font-bold px-2">&times;</button>
            </td>
        `;

        tr.innerHTML = html;

        let lastChild = parentRow;
        let nextSibling = parentRow.nextElementSibling;
        while (nextSibling && nextSibling.classList.contains(`child-of-${parentSubIdx}`)) {
            lastChild = nextSibling;
            nextSibling = nextSibling.nextElementSibling;
        }
        lastChild.insertAdjacentElement('afterend', tr);
        if (mulai && selesai) updateRowVisuals('t_' + tugasGlobalIdx);
        closeTugasModal();
    }

    function handleCellClick(cell, rowId) {
        const dateStr = cell.dataset.date;
        const clickedDate = new Date(dateStr);
        let state = rowStates[rowId];

        if (!state.start || (state.start && state.end)) {
            state.start = clickedDate;
            state.end = null;
        } else if (state.start && !state.end) {
            if (clickedDate < state.start) {
                state.end = state.start;
                state.start = clickedDate;
            } else {
                state.end = clickedDate;
            }
        }
        updateRowVisuals(rowId);
    }

    function updateRowVisuals(rowId) {
        const state = rowStates[rowId];
        const row = document.querySelector(`tr[data-row-id="${rowId}"]`);
        if(!row) return;

        const cells = row.querySelectorAll('.day-cell');
        cells.forEach(cell => {
            cell.classList.remove('selected-range', 'selected-start', 'selected-end');
        });

        if (!state.start) return;

        const startInput = row.querySelector('.start-date-input');
        const endInput = row.querySelector('.end-date-input');
        startInput.value = state.start.toISOString().split('T')[0];
        endInput.value = state.end ? state.end.toISOString().split('T')[0] : state.start.toISOString().split('T')[0];

        cells.forEach(cell => {
            const cellDate = new Date(cell.dataset.date);
            const isStart = cellDate.getTime() === state.start.getTime();
            const isEnd = state.end && cellDate.getTime() === state.end.getTime();
            const isBetween = state.end && cellDate > state.start && cellDate < state.end;

            if (isStart) cell.classList.add('selected-start');
            if (isEnd) cell.classList.add('selected-end');
            if (isBetween || (isStart && !isEnd) || (isStart && isEnd)) cell.classList.add('selected-range');
        });
    }

    function hapusRow(btn, rowId) {
        const row = btn.closest('tr');
        if(row.classList.contains('subproject-row')) {
            const subIdx = row.dataset.subIdx;
            document.querySelectorAll(`.child-of-${subIdx}`).forEach(el => el.remove());
        }
        delete rowStates[rowId];
        row.remove();
    }
    
    document.getElementById('submit_form').addEventListener('submit', function(e) {
        let hasError = false;
        const newRows = document.querySelectorAll('.new-row');
        
        if (newRows.length === 0) {
            e.preventDefault();
            alert("Harap tambahkan setidaknya satu Subproyek sebelum menyimpan.");
            return;
        }

        newRows.forEach(row => {
            const startInput = row.querySelector('.start-date-input').value;
            const endInput = row.querySelector('.end-date-input').value;
            const type = row.classList.contains('subproject-row') ? 'Subproyek' : 'Tugas';
            
            if (!startInput || !endInput) {
                hasError = true;
                const name = row.querySelector('input[type="text"]').value || "Tanpa Nama";
                alert(`Harap tentukan tanggal mulai dan selesai untuk ${type}: "${name}" dengan mengklik pada kotak tabel.`);
            }
        });

        if (hasError) {
            e.preventDefault();
        }
    });

    // === Gantt Select: tampilkan info jumlah hanya saat dropdown terbuka ===
    function showFullText(select) {
        Array.from(select.options).forEach(opt => {
            if (opt.dataset.full) opt.textContent = opt.dataset.full;
        });
    }

    function showShortText(select) {
        const selected = select.options[select.selectedIndex];
        if (selected && selected.dataset.short) {
            selected.textContent = selected.dataset.short;
        }
    }

    function initGanttSelect(select) {
        // Saat fokus (dropdown dibuka): tampilkan teks lengkap dengan jumlah
        select.addEventListener('focus', function() { showFullText(this); });
        select.addEventListener('mousedown', function() { showFullText(this); });
        
        // Saat dipilih / ditutup: kembalikan ke nama saja
        select.addEventListener('change', function() { showShortText(this); });
        select.addEventListener('blur', function() { showShortText(this); });

        // Inisialisasi: jika sudah ada yang terpilih, tampilkan nama saja
        showShortText(select);
    }

    // Inisialisasi semua gantt-select yang sudah ada di halaman
    document.querySelectorAll('.gantt-select').forEach(initGanttSelect);

    // Observer untuk menangani select yang ditambahkan secara dinamis (addSubproyek/addTugas)
    const ganttObserver = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    if (node.classList && node.classList.contains('gantt-select')) {
                        initGanttSelect(node);
                    }
                    node.querySelectorAll && node.querySelectorAll('.gantt-select').forEach(initGanttSelect);
                }
            });
        });
    });
    ganttObserver.observe(document.getElementById('table_body'), { childList: true, subtree: true });

</script>
@endsection

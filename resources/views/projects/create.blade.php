@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Tambah Project Baru</h1>
        <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded shadow-sm border border-gray-100 p-6">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Job ID (Work Order Release)</label>
                <select name="job_id" id="job_id" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Work Order Release --</option>
                    @foreach($workOrders as $wor)
                        <option value="{{ $wor->job_id }}" 
                            data-jobdesc="{{ $wor->jobdesc }}"
                            data-tgl="{{ $wor->tgl_order }}"
                            data-jadwal="{{ $wor->jadwal_kirim }}"
                            data-prioritas="{{ $wor->priority }}"
                            {{ old('job_id') == $wor->job_id ? 'selected' : '' }}>
                            {{ $wor->job_id }} - {{ $wor->customer }} ({{ \Illuminate\Support\Str::limit($wor->jobdesc, 50) }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Hanya menampilkan WOR yang belum memiliki Project.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Project</label>
                <input type="text" name="nama_project" id="nama_project" value="{{ old('nama_project') }}" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Prioritas</label>
                <select name="prioritas" id="prioritas" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="TINGGI" {{ old('prioritas') == 'TINGGI' ? 'selected' : '' }}>TINGGI</option>
                    <option value="SEDANG" {{ old('prioritas') == 'SEDANG' ? 'selected' : '' }}>SEDANG</option>
                    <option value="RENDAH" {{ old('prioritas') == 'RENDAH' ? 'selected' : '' }}>RENDAH</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_project" id="start_project" value="{{ old('start_project') }}" class="sync-start shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Target Selesai</label>
                    <input type="date" name="target_project" id="target_project" value="{{ old('target_project') }}" class="sync-target shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Durasi Pengerjaan</label>
                    <div class="flex items-center">
                        <input type="number" name="durasi_hari" id="durasi_hari" min="1" value="{{ old('durasi_hari', 1) }}" class="sync-durasi shadow-sm appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300" required>
                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r py-2 px-4 text-gray-600">Hari</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Penanggung Jawab</label>
                <select name="management_id" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring focus:border-blue-300" required>
                    <option value="">-- Pilih Penanggung Jawab --</option>
                    @foreach($managements as $m)
                        <option value="{{ $m->management_id }}" {{ old('management_id') == $m->management_id ? 'selected' : '' }}>
                            {{ $m->karyawan->nm_karyawan ?? 'Unknown' }} ({{ $m->karyawan ? ($m->karyawan->divisi->pluck('nama_divisi')->join(', ') ?: 'Unknown Divisi') : 'Unknown Divisi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Project
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jobIdSelect = document.getElementById('job_id');
        
        jobIdSelect.addEventListener('change', function() {
            let option = this.options[this.selectedIndex];
            if(option && option.value) {
                // Auto-fill Nama Project
                let jobdesc = option.getAttribute('data-jobdesc');
                if (jobdesc && !document.getElementById('nama_project').value) {
                    document.getElementById('nama_project').value = jobdesc.substring(0, 50); // limit 50 chars as in validation
                }
                
                // Auto-fill Tanggal Mulai dan Target Selesai
                let tglOrder = option.getAttribute('data-tgl');
                let tglKirim = option.getAttribute('data-jadwal');
                
                let startInput = document.getElementById('start_project');
                let targetInput = document.getElementById('target_project');
                let durasiInput = document.getElementById('durasi_hari');
                
                if (tglOrder) {
                    startInput.value = tglOrder;
                }
                
                if (tglKirim) {
                    targetInput.value = tglKirim;
                }
                
                // Hitung durasi jika tgl order dan tgl kirim ada
                if (tglOrder && tglKirim) {
                    let start = new Date(tglOrder);
                    let end = new Date(tglKirim);
                    let diffTime = end - start;
                    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    if (diffDays > 0) {
                        durasiInput.value = diffDays;
                    }
                }
                
                // Auto-fill Prioritas
                let worPrio = option.getAttribute('data-prioritas');
                let prioSelect = document.getElementById('prioritas');
                
                if (worPrio === 'High Priority') {
                    prioSelect.value = 'TINGGI';
                } else if (worPrio === 'Low Priority') {
                    prioSelect.value = 'RENDAH';
                } else {
                    prioSelect.value = 'SEDANG';
                }
                
                // Update TomSelect if it exists
                if (prioSelect.tomselect) {
                    prioSelect.tomselect.setValue(prioSelect.value);
                }
            }
        });
    });
</script>
@endsection

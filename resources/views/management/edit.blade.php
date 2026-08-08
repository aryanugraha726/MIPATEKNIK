@extends('layout.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold border-l-4 border-purple-600 pl-3">Edit Management (PIC) #{{ $management->management_id }}</h1>
        <a href="{{ route('management.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
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
        <form action="{{ route('management.update', $management->management_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Karyawan</label>
                <select name="id_karyawan" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-indigo-300" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan', $management->id_karyawan) == $k->id_karyawan ? 'selected' : '' }}>
                            {{ $k->nm_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update PIC
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

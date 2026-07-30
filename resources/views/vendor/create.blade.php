@extends('layout.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold border-l-4 border-orange-600 pl-3">Tambah Vendor</h1>
        <a href="{{ route('vendor.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Kembali</a>
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
        <form action="{{ route('vendor.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Vendor <span class="text-red-500">*</span></label>
                <input type="text" name="nama_vendor" value="{{ old('nama_vendor') }}" placeholder="Contoh: PT. Sumber Makmur" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-indigo-300" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Contact Person (CP)</label>
                    <input type="text" name="cp_vendor" value="{{ old('cp_vendor') }}" placeholder="Contoh: Pak Budi" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-indigo-300">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">No. Telepon / HP</label>
                    <input type="text" name="phone_vendor" value="{{ old('phone_vendor') }}" placeholder="Contoh: 081234567890" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-indigo-300">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Vendor</label>
                <textarea name="alamat_vendor" rows="3" placeholder="Alamat lengkap vendor..." class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-indigo-300">{{ old('alamat_vendor') }}</textarea>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Vendor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

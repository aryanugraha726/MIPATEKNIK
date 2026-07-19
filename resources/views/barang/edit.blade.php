@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center space-x-3">
        <a href="{{ route('barang.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Edit Barang: {{ $barang->id_barang }}</h1>
    </div>

    <div class="bg-white p-8 rounded-lg shadow-md border border-gray-100">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="id_barang_display" class="block text-sm font-semibold text-gray-700 mb-2">ID Barang</label>
                    <input type="text" id="id_barang_display" value="{{ $barang->id_barang }}" disabled
                        class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">ID Barang tidak dapat diubah.</p>
                </div>

                <div>
                    <label for="nama_barang" class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <div>
                    <label for="id_kategori" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select name="id_kategori" id="id_kategori" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="" disabled>-- Pilih Kategori --</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori', $barang->id_kategori) == $kategori->id_kategori ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="id_satuan" class="block text-sm font-semibold text-gray-700 mb-2">Satuan</label>
                    <select name="id_satuan" id="id_satuan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="" disabled>-- Pilih Satuan --</option>
                        @foreach($satuans as $satuan)
                            <option value="{{ $satuan->id_satuan }}" {{ old('id_satuan', $barang->id_satuan) == $satuan->id_satuan ? 'selected' : '' }}>
                                {{ $satuan->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="harga" class="block text-sm font-semibold text-gray-700 mb-2">Harga Satuan (Rp)</label>
                    <input type="number" name="harga" id="harga" value="{{ old('harga', $barang->harga) }}" required min="0" step="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg transition duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

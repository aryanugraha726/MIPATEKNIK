@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Alamat Pengiriman</h1>
            <p class="text-gray-500 text-sm mt-1">Perbarui data lokasi {{ $shippingAddress->id_lokasi }}</p>
        </div>
        <a href="{{ route('shipping-address.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm">
            &larr; Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc pl-5 text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('shipping-address.update', $shippingAddress->id_lokasi) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ID Lokasi</label>
                <input type="text" value="{{ $shippingAddress->id_lokasi }}" class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                <p class="text-xs text-gray-500 mt-1">ID Lokasi tidak dapat diubah.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lokasi" value="{{ old('nama_lokasi', $shippingAddress->nama_lokasi) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="alamat_mipa" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('alamat_mipa', $shippingAddress->alamat_mipa) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                    <input type="text" name="cp_mipa" value="{{ old('cp_mipa', $shippingAddress->cp_mipa) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" name="phone_mipa" value="{{ old('phone_mipa', $shippingAddress->phone_mipa) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email_mipa" value="{{ old('email_mipa', $shippingAddress->email_mipa) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Yeni Tedarikçi Ekle')
@section('page-title', 'Yeni Tedarikçi Ekle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tedarikçi Adı *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kod</label>
                <input type="text" name="code" value="{{ old('code') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">XML URL</label>
                <input type="url" name="xml_url" value="{{ old('xml_url') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('xml_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">XML Tipi *</label>
                <select name="xml_type" required
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="standard" {{ old('xml_type') == 'standard' ? 'selected' : '' }}>Standart</option>
                    <option value="custom" {{ old('xml_type') == 'custom' ? 'selected' : '' }}>Özel</option>
                </select>
                @error('xml_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">XML Kullanıcı Adı</label>
                <input type="text" name="xml_username" value="{{ old('xml_username') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('xml_username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">XML Şifre</label>
                <input type="password" name="xml_password" value="{{ old('xml_password') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('xml_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Güncelleme Sıklığı (Gün) *</label>
                <input type="number" name="update_frequency" value="{{ old('update_frequency', 1) }}" min="1" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('update_frequency')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="flex items-center mt-6">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                <textarea name="notes" rows="3"
                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                İptal
            </a>
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                Kaydet
            </button>
        </div>
    </form>
</div>
@endsection


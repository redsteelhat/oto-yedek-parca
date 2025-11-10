@extends('layouts.admin')

@section('title', 'Yeni Kargo Firması Ekle')
@section('page-title', 'Yeni Kargo Firması Ekle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.shipping-companies.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Firma Adı *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kod *</label>
                <input type="text" name="code" value="{{ old('code') }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="Örn: yurtici, aras, mng">
                <p class="text-xs text-gray-500 mt-1">Benzersiz kod (küçük harf, boşluksuz)</p>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">API Tipi</label>
                <select name="api_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="manual" {{ old('api_type') == 'manual' ? 'selected' : '' }}>Manuel (API Yok)</option>
                    <option value="yurtici_api" {{ old('api_type') == 'yurtici_api' ? 'selected' : '' }}>Yurtiçi Kargo API</option>
                    <option value="aras_api" {{ old('api_type') == 'aras_api' ? 'selected' : '' }}>Aras Kargo API</option>
                    <option value="mng_api" {{ old('api_type') == 'mng_api' ? 'selected' : '' }}>MNG Kargo API</option>
                    <option value="surat_api" {{ old('api_type') == 'surat_api' ? 'selected' : '' }}>Sürat Kargo API</option>
                </select>
            </div>
            
            <div id="apiSettings" class="md:col-span-2 hidden">
                <div class="border rounded-lg p-4 bg-gray-50">
                    <h4 class="font-semibold mb-3">API Ayarları</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API URL</label>
                            <input type="url" name="api_url" value="{{ old('api_url') }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                            <input type="text" name="api_key" value="{{ old('api_key') }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Secret</label>
                            <input type="text" name="api_secret" value="{{ old('api_secret') }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Username</label>
                            <input type="text" name="api_username" value="{{ old('api_username') }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Password</label>
                            <input type="password" name="api_password" value="{{ old('api_password') }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Temel Fiyat (₺)</label>
                <input type="number" name="base_price" step="0.01" value="{{ old('base_price', 0) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kilo Başı Ücret (₺/kg)</label>
                <input type="number" name="price_per_kg" step="0.01" value="{{ old('price_per_kg', 0) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Desi Başı Ücret (₺/cm³)</label>
                <input type="number" name="price_per_cm3" step="0.01" value="{{ old('price_per_cm3', 0) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ücretsiz Kargo Limiti (₺)</label>
                <input type="number" name="free_shipping_threshold" step="0.01" value="{{ old('free_shipping_threshold') }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="Boş bırakılırsa ücretsiz kargo yok">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahmini Teslimat (Gün)</label>
                <input type="number" name="estimated_days" value="{{ old('estimated_days', 3) }}" min="1"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sıralama</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                <textarea name="notes" rows="3"
                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.shipping-companies.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                İptal
            </a>
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                Kaydet
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiTypeSelect = document.querySelector('select[name="api_type"]');
    const apiSettings = document.getElementById('apiSettings');
    
    function toggleApiSettings() {
        if (apiTypeSelect.value && apiTypeSelect.value !== 'manual') {
            apiSettings.classList.remove('hidden');
        } else {
            apiSettings.classList.add('hidden');
        }
    }
    
    apiTypeSelect.addEventListener('change', toggleApiSettings);
    toggleApiSettings(); // Initial call
});
</script>
@endpush
@endsection


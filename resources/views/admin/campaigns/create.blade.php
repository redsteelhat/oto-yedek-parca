@extends('layouts.admin')

@section('title', 'Yeni Kampanya Ekle')
@section('page-title', 'Yeni Kampanya Ekle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kampanya Adı *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kampanya Görseli</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kampanya Tipi *</label>
                <select name="type" required class="w-full border rounded px-3 py-2">
                    <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Genel</option>
                    <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Ürün Bazlı</option>
                    <option value="category" {{ old('type') == 'category' ? 'selected' : '' }}>Kategori Bazlı</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İndirim Tipi *</label>
                <select name="discount_type" required class="w-full border rounded px-3 py-2">
                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Yüzde (%)</option>
                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Sabit Tutar (₺)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İndirim Değeri *</label>
                <input type="number" name="discount_value" step="0.01" value="{{ old('discount_value') }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('discount_value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Alışveriş Tutarı</label>
                <input type="number" name="min_purchase_amount" step="0.01" value="{{ old('min_purchase_amount') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi *</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi *</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sıralama</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Düşük sayı önceliklidir</p>
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
            
            <div class="md:col-span-2" id="categorySection" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Uygulanabilir Kategoriler</label>
                <div class="max-h-48 overflow-y-auto border rounded p-3">
                    @foreach($categories as $category)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="applicable_categories[]" value="{{ $category->id }}" class="mr-2">
                            <span class="text-sm text-gray-700">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div class="md:col-span-2" id="productSection" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Uygulanabilir Ürünler</label>
                <div class="max-h-48 overflow-y-auto border rounded p-3">
                    @foreach($products as $product)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="applicable_products[]" value="{{ $product->id }}" class="mr-2">
                            <span class="text-sm text-gray-700">{{ $product->name }} ({{ $product->sku }})</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.campaigns.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
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
    const typeSelect = document.querySelector('select[name="type"]');
    const categorySection = document.getElementById('categorySection');
    const productSection = document.getElementById('productSection');
    
    function toggleSections() {
        const type = typeSelect.value;
        if (type === 'category') {
            categorySection.style.display = 'block';
            productSection.style.display = 'none';
        } else if (type === 'product') {
            categorySection.style.display = 'none';
            productSection.style.display = 'block';
        } else {
            categorySection.style.display = 'none';
            productSection.style.display = 'none';
        }
    }
    
    typeSelect.addEventListener('change', toggleSections);
    toggleSections(); // Initial call
});
</script>
@endpush
@endsection


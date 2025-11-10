@extends('layouts.admin')

@section('title', 'Kampanya Düzenle: ' . $campaign->name)
@section('page-title', 'Kampanya Düzenle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kampanya Adı *</label>
                <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description', $campaign->description) }}</textarea>
            </div>
            
            @if($campaign->image)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Görsel</label>
                    <img src="{{ asset('storage/' . $campaign->image) }}" alt="{{ $campaign->name }}" class="h-32 w-32 object-cover rounded">
                </div>
            @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kampanya Görseli</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kampanya Tipi *</label>
                <select name="type" required class="w-full border rounded px-3 py-2" id="campaignType">
                    <option value="general" {{ old('type', $campaign->type) == 'general' ? 'selected' : '' }}>Genel</option>
                    <option value="product" {{ old('type', $campaign->type) == 'product' ? 'selected' : '' }}>Ürün Bazlı</option>
                    <option value="category" {{ old('type', $campaign->type) == 'category' ? 'selected' : '' }}>Kategori Bazlı</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İndirim Tipi *</label>
                <select name="discount_type" required class="w-full border rounded px-3 py-2">
                    <option value="percentage" {{ old('discount_type', $campaign->discount_type) == 'percentage' ? 'selected' : '' }}>Yüzde (%)</option>
                    <option value="fixed" {{ old('discount_type', $campaign->discount_type) == 'fixed' ? 'selected' : '' }}>Sabit Tutar (₺)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İndirim Değeri *</label>
                <input type="number" name="discount_value" step="0.01" value="{{ old('discount_value', $campaign->discount_value) }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Alışveriş Tutarı</label>
                <input type="number" name="min_purchase_amount" step="0.01" value="{{ old('min_purchase_amount', $campaign->min_purchase_amount) }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi *</label>
                <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date->format('Y-m-d')) }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sıralama</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $campaign->sort_order) }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $campaign->is_active) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
            
            <div class="md:col-span-2" id="categorySection" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Uygulanabilir Kategoriler</label>
                <div class="max-h-48 overflow-y-auto border rounded p-3">
                    @foreach($categories as $category)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="applicable_categories[]" value="{{ $category->id }}"
                                   {{ in_array($category->id, old('applicable_categories', $campaign->applicable_categories ?? [])) ? 'checked' : '' }} class="mr-2">
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
                            <input type="checkbox" name="applicable_products[]" value="{{ $product->id }}"
                                   {{ in_array($product->id, old('applicable_products', $campaign->applicable_products ?? [])) ? 'checked' : '' }} class="mr-2">
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
                Güncelle
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('campaignType');
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


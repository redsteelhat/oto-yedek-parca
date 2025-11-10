@extends('layouts.admin')

@section('title', 'Kupon Düzenle: ' . $coupon->code)
@section('page-title', 'Kupon Düzenle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kupon Kodu *</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kupon Adı *</label>
                <input type="text" name="name" value="{{ old('name', $coupon->name) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description', $coupon->description) }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İndirim Tipi *</label>
                <select name="type" required class="w-full border rounded px-3 py-2">
                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Yüzde (%)</option>
                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Sabit Tutar (₺)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İndirim Değeri *</label>
                <input type="number" name="value" step="0.01" value="{{ old('value', $coupon->value) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Alışveriş Tutarı</label>
                <input type="number" name="min_purchase_amount" step="0.01" value="{{ old('min_purchase_amount', $coupon->min_purchase_amount) }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maksimum İndirim Tutarı</label>
                <input type="number" name="max_discount_amount" step="0.01" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Toplam Kullanım Limiti</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kullanıcı Başına Limit *</label>
                <input type="number" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user) }}" required min="1"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Uygulanabilir Kategoriler</label>
                <div class="max-h-48 overflow-y-auto border rounded p-3">
                    @foreach($categories as $category)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="applicable_categories[]" value="{{ $category->id }}" 
                                   {{ in_array($category->id, old('applicable_categories', $coupon->applicable_categories ?? [])) ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Uygulanabilir Ürünler</label>
                <div class="max-h-48 overflow-y-auto border rounded p-3">
                    @foreach($products as $product)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="applicable_products[]" value="{{ $product->id }}"
                                   {{ in_array($product->id, old('applicable_products', $coupon->applicable_products ?? [])) ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">{{ $product->name }} ({{ $product->sku }})</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                İptal
            </a>
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection


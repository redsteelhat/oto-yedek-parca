@extends('layouts.admin')

@section('title', 'Ürün Raporu')
@section('page-title', 'Ürün Raporu')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.reports.products') }}" class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Tüm Kategoriler</option>
                    @foreach(\App\Models\Category::where('is_active', true)->get() as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Taslak</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok Durumu</label>
                <select name="stock_status" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Stokta Var</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Stokta Yok</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Düşük Stok</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
            <select name="sort" class="w-full border rounded px-3 py-2 text-sm">
                <option value="sales" {{ request('sort') == 'sales' ? 'selected' : '' }}>Satış Adedi</option>
                <option value="revenue" {{ request('sort') == 'revenue' ? 'selected' : '' }}>Toplam Gelir</option>
                <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Görüntülenme</option>
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>En Yeni</option>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Filtrele
            </button>
            <a href="{{ route('admin.reports.products', array_merge(request()->all(), ['format' => 'excel'])) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                Excel'e Aktar
            </a>
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm">
                Geri Dön
            </a>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Ürün Performansı</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satış Adedi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam Gelir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Görüntülenme</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        {{ $product->sku }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ Str::limit($product->name, 50) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $product->category ? $product->category->name : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($product->sale_price)
                            <span class="text-red-600 font-semibold">{{ number_format($product->sale_price, 2) }} ₺</span>
                            <span class="text-gray-400 line-through text-xs ml-1">{{ number_format($product->price, 2) }} ₺</span>
                        @else
                            <span>{{ number_format($product->price, 2) }} ₺</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($product->stock <= 0) bg-red-100 text-red-800
                            @elseif($product->stock <= $product->min_stock_level) bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                        {{ number_format($product->total_sales ?? 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                        {{ number_format($product->total_revenue ?? 0, 2) }} ₺
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($product->views) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        Ürün bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


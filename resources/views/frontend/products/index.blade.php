@extends('layouts.app')

@section('title', 'Ürünler')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Ürünler</h1>
        
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ara</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı, SKU, OEM..." 
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marka</label>
                    <select name="brand_id" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Tüm Markalar</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sıralama</label>
                    <select name="sort" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>En Yeni</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Fiyat (Düşük-Yüksek)</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Fiyat (Yüksek-Düşük)</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>İsim</option>
                        <option value="sales" {{ request('sort') == 'sales' ? 'selected' : '' }}>Çok Satanlar</option>
                    </select>
                </div>
                
                <div class="md:col-span-4">
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700 transition">
                        Filtrele
                    </button>
                    <a href="{{ route('products.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">
                        Temizle
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <a href="{{ route('products.show', $product->slug) }}">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-48 object-cover"
                                 loading="lazy">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Resim Yok</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2 text-gray-900">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">SKU: {{ $product->sku }}</p>
                            @if($product->oem_code)
                                <p class="text-gray-600 text-sm mb-2">OEM: {{ $product->oem_code }}</p>
                            @endif
                            <div class="flex justify-between items-center mt-4">
                                <div>
                                    @if($product->is_on_sale)
                                        <span class="text-2xl font-bold text-primary-600">{{ number_format($product->sale_price, 2) }} ₺</span>
                                        <span class="text-sm text-gray-500 line-through ml-2">{{ number_format($product->price, 2) }} ₺</span>
                                    @else
                                        <span class="text-2xl font-bold text-primary-600">{{ number_format($product->price, 2) }} ₺</span>
                                    @endif
                                </div>
                                @if($product->is_in_stock)
                                    <span class="text-green-600 text-sm font-medium">Stokta</span>
                                @else
                                    <span class="text-red-600 text-sm font-medium">Stokta Yok</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-600 text-lg">Ürün bulunamadı.</p>
            <a href="{{ route('products.index') }}" class="text-primary-600 hover:text-primary-800 mt-4 inline-block">
                Filtreleri Temizle
            </a>
        </div>
    @endif
</div>
@endsection


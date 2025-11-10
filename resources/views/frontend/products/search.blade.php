@extends('layouts.app')

@section('title', 'Arama Sonuçları: ' . $searchTerm)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 sm:mb-4 px-4 sm:px-0">Arama Sonuçları</h1>
        <p class="text-sm sm:text-base text-gray-600 px-4 sm:px-0">"<strong>{{ $searchTerm }}</strong>" için {{ $products->total() }} sonuç bulundu</p>
        
        <!-- Search Box -->
        <div class="mt-4 px-4 sm:px-0">
            <form action="{{ route('products.search') }}" method="GET" class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <input type="text" name="q" value="{{ $searchTerm }}" 
                       placeholder="Parça adı, OEM, ürün kodu ile arama..." 
                       class="flex-1 border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-primary-500">
                <button type="submit" class="bg-primary-600 text-white px-4 sm:px-6 py-2 rounded hover:bg-primary-700 transition text-sm sm:text-base">
                    Ara
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 sticky top-20">
                <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Filtreler</h3>
                
                <form method="GET" action="{{ route('products.search') }}">
                    <input type="hidden" name="q" value="{{ $searchTerm }}">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat Aralığı</label>
                        <div class="flex space-x-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                   class="w-full border rounded px-2 py-1 text-sm">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                   class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700 transition text-sm">
                        Filtrele
                    </button>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="lg:col-span-3">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-4">
                <p class="text-sm sm:text-base text-gray-600">{{ $products->total() }} sonuç</p>
                <select name="sort" form="sortForm" class="w-full sm:w-auto border rounded px-3 py-2 text-sm">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>En Yeni</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Fiyat (Düşük-Yüksek)</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Fiyat (Yüksek-Düşük)</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>İsim</option>
                </select>
                <form id="sortForm" method="GET" action="{{ route('products.search') }}" class="hidden">
                    <input type="hidden" name="q" value="{{ $searchTerm }}">
                    @foreach(request()->except(['sort', 'q']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <a href="{{ route('products.show', $product->slug) }}">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-48 object-cover">
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
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-primary-600">{{ number_format($product->final_price, 2) }} ₺</span>
                                        @if($product->is_in_stock)
                                            <span class="text-green-600 text-sm font-medium">Stokta</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-600 text-lg mb-4">Aradığınız kriterlere uygun ürün bulunamadı.</p>
                    <a href="{{ route('products.index') }}" class="text-primary-600 hover:text-primary-800">
                        Tüm Ürünleri Görüntüle
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


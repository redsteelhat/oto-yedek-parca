@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-gray-600">{{ $category->description }}</p>
        @endif
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
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Resim Yok</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2 text-gray-900">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">SKU: {{ $product->sku }}</p>
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
            <p class="text-gray-600 text-lg">Bu kategoride ürün bulunamadı.</p>
        </div>
    @endif
</div>
@endsection


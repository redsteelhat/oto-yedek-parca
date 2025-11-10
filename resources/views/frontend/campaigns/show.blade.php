@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $campaign->name }}</h1>
        @if($campaign->description)
            <p class="text-gray-600">{{ $campaign->description }}</p>
        @endif
        <div class="mt-4 flex items-center space-x-4">
            <span class="bg-red-100 text-red-800 px-4 py-2 rounded-lg font-bold text-lg">
                %{{ number_format($campaign->discount_value, 0) }} İndirim
            </span>
            <span class="text-gray-600">
                {{ $campaign->start_date->format('d.m.Y') }} - {{ $campaign->end_date->format('d.m.Y') }}
            </span>
        </div>
    </div>

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
                            <div class="flex justify-between items-center">
                                @php
                                    $discountPrice = $campaign->calculateDiscount($product->price);
                                    $finalPrice = $product->final_price - $discountPrice;
                                @endphp
                                <div>
                                    <span class="text-2xl font-bold text-primary-600">{{ number_format($finalPrice, 2) }} ₺</span>
                                    <span class="text-sm text-gray-500 line-through ml-2">{{ number_format($product->price, 2) }} ₺</span>
                                </div>
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">İndirimli</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <p class="text-gray-600 text-lg">Bu kampanyada henüz ürün bulunmuyor.</p>
        </div>
    @endif
</div>
@endsection


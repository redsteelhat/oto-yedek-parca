@extends('layouts.app')

@section('title', 'Sepetim')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">Sepetim</h1>

    @if(count($items) > 0)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adet</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                            <tr>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item['product']->primaryImage)
                                            <img src="{{ asset('storage/' . $item['product']->primaryImage->image_path) }}" 
                                                 alt="{{ $item['product']->name }}" 
                                                 class="h-12 w-12 sm:h-16 sm:w-16 object-cover rounded mr-3 sm:mr-4">
                                        @endif
                                        <div>
                                            <a href="{{ route('products.show', $item['product']->slug) }}" class="font-medium text-sm sm:text-base text-gray-900 hover:text-primary-600">
                                                {{ $item['product']->name }}
                                            </a>
                                            <p class="text-xs sm:text-sm text-gray-500">SKU: {{ $item['product']->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item['price'], 2) }} ₺
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}" 
                                               class="w-16 sm:w-20 border rounded px-2 py-1 text-center text-sm" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($item['total'], 2) }} ₺
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden">
                @foreach($items as $item)
                    <div class="border-b border-gray-200 p-4">
                        <div class="flex items-start space-x-4">
                            @if($item['product']->primaryImage)
                                <img src="{{ asset('storage/' . $item['product']->primaryImage->image_path) }}" 
                                     alt="{{ $item['product']->name }}" 
                                     class="h-20 w-20 object-cover rounded flex-shrink-0">
                            @endif
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('products.show', $item['product']->slug) }}" class="font-medium text-sm text-gray-900 hover:text-primary-600 block mb-1">
                                    {{ $item['product']->name }}
                                </a>
                                <p class="text-xs text-gray-500 mb-2">SKU: {{ $item['product']->sku }}</p>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($item['price'], 2) }} ₺</span>
                                    <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="text-xs text-gray-600">Adet:</label>
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}" 
                                               class="w-16 border rounded px-2 py-1 text-center text-sm" onchange="this.form.submit()">
                                    </form>
                                    <span class="text-sm font-medium text-gray-900 ml-auto">
                                        {{ number_format($item['total'], 2) }} ₺
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Coupon Section -->
            <div class="bg-gray-50 px-4 sm:px-6 py-4 border-t border-gray-200">
                <h3 class="text-sm sm:text-base font-semibold text-gray-700 mb-3">Kupon Kodu</h3>
                @if($coupon)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4 mb-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-800">Kupon Uygulandı: {{ $coupon->code }}</p>
                                <p class="text-xs text-green-600 mt-1">{{ $coupon->name }}</p>
                            </div>
                            <form action="{{ route('cart.remove-coupon') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Kaldır
                                </button>
                            </form>
                        </div>
                        @if($couponDiscount > 0)
                            <p class="text-sm text-green-700 mt-2">İndirim: <strong>-{{ number_format($couponDiscount, 2) }} ₺</strong></p>
                        @endif
                    </div>
                @else
                    <form action="{{ route('cart.apply-coupon') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <input type="text" name="code" placeholder="Kupon kodunu girin" 
                               class="flex-1 border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                               value="{{ old('code') }}">
                        <button type="submit" class="bg-primary-600 text-white px-4 sm:px-6 py-2 rounded hover:bg-primary-700 transition text-sm sm:text-base whitespace-nowrap">
                            Kupon Uygula
                        </button>
                    </form>
                    @error('code')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <!-- Summary -->
            <div class="bg-gray-50 px-4 sm:px-6 py-4 border-t">
                <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-8">
                    <div class="text-left sm:text-right">
                        <p class="text-sm text-gray-600">Ara Toplam:</p>
                        @if($campaignDiscount > 0)
                            <p class="text-sm text-gray-600">Kampanya İndirimi:</p>
                        @endif
                        @if($couponDiscount > 0)
                            <p class="text-sm text-gray-600">Kupon İndirimi:</p>
                        @endif
                        @if($discountAmount > 0)
                            <p class="text-sm text-red-600 font-medium">Toplam İndirim:</p>
                        @endif
                        <p class="text-sm text-gray-600">KDV:</p>
                        <p class="text-lg font-bold text-gray-900 mt-2">Toplam:</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-sm text-gray-900">{{ number_format($subtotal, 2) }} ₺</p>
                        @if($campaignDiscount > 0)
                            <p class="text-sm text-green-600">-{{ number_format($campaignDiscount, 2) }} ₺</p>
                        @endif
                        @if($couponDiscount > 0)
                            <p class="text-sm text-green-600">-{{ number_format($couponDiscount, 2) }} ₺</p>
                        @endif
                        @if($discountAmount > 0)
                            <p class="text-sm text-red-600 font-medium">-{{ number_format($discountAmount, 2) }} ₺</p>
                        @endif
                        <p class="text-sm text-gray-900">{{ number_format($tax, 2) }} ₺</p>
                        <p class="text-lg font-bold text-primary-600 mt-2">{{ number_format($total, 2) }} ₺</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('products.index') }}" class="bg-gray-200 text-gray-800 px-4 sm:px-6 py-2 sm:py-2 rounded text-center hover:bg-gray-300 transition text-sm sm:text-base">
                        Alışverişe Devam Et
                    </a>
                    <a href="{{ route('checkout.index') }}" class="bg-primary-600 text-white px-4 sm:px-6 py-2 sm:py-2 rounded text-center hover:bg-primary-700 transition text-sm sm:text-base">
                        Ödemeye Geç
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-8 sm:p-12 text-center">
            <p class="text-gray-600 text-lg mb-4">Sepetiniz boş.</p>
            <a href="{{ route('products.index') }}" class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700 transition inline-block text-sm sm:text-base">
                Alışverişe Başla
            </a>
        </div>
    @endif
</div>
@endsection

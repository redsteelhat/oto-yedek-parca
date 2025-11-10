@extends('layouts.app')

@section('title', 'Ödeme')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Ödeme</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        <!-- Shipping Address -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Teslimat Adresi</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                        <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                        <input type="text" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Şehir *</label>
                        <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">İlçe *</label>
                        <input type="text" name="shipping_district" value="{{ old('shipping_district') }}" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adres *</label>
                        <textarea name="shipping_address" rows="3" required
                                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('shipping_address') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Posta Kodu</label>
                        <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}"
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Ödeme Yöntemi</h2>
                
                <div class="space-y-3">
                    <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="credit_card" checked class="mr-3">
                        <span>Kredi Kartı</span>
                    </label>
                    <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                        <span>Havale/EFT</span>
                    </label>
                    <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="cash_on_delivery" class="mr-3">
                        <span>Kapıda Ödeme</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4">Sipariş Özeti</h2>
                
                <div class="space-y-2 mb-4">
                    @foreach($items as $item)
                        <div class="flex justify-between text-sm">
                            <span>{{ $item['product']->name }} x{{ $item['quantity'] }}</span>
                            <span>{{ number_format($item['total'], 2) }} ₺</span>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Ara Toplam:</span>
                        <span>{{ number_format($subtotal, 2) }} ₺</span>
                    </div>
                    @if($campaignDiscount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Kampanya İndirimi:</span>
                            <span>-{{ number_format($campaignDiscount, 2) }} ₺</span>
                        </div>
                    @endif
                    @if($couponDiscount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Kupon İndirimi ({{ $coupon->code ?? '' }}):</span>
                            <span>-{{ number_format($couponDiscount, 2) }} ₺</span>
                        </div>
                    @endif
                    @if($discountAmount > 0)
                        <div class="flex justify-between text-sm font-medium text-red-600">
                            <span>Toplam İndirim:</span>
                            <span>-{{ number_format($discountAmount, 2) }} ₺</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span>KDV:</span>
                        <span>{{ number_format($tax, 2) }} ₺</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Kargo:</span>
                        <span>{{ number_format($shippingCost, 2) }} ₺</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-bold text-lg">
                        <span>Toplam:</span>
                        <span class="text-primary-600">{{ number_format($total, 2) }} ₺</span>
                    </div>
                </div>
                
                @if($coupon)
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-800">
                            <strong>Kupon:</strong> {{ $coupon->code }} - {{ $coupon->name }}
                        </p>
                    </div>
                @endif
                
                <button type="submit" class="w-full bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition mt-6">
                    Siparişi Tamamla
                </button>
            </div>
        </div>
    </form>
</div>
@endsection


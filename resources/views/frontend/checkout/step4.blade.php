@extends('layouts.app')

@section('title', 'Ödeme - Adım 4: Sipariş Onayı')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white font-bold">✓</div>
                <span class="ml-2 font-semibold text-green-600">Adres</span>
            </div>
            <div class="w-16 h-1 bg-green-500"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white font-bold">✓</div>
                <span class="ml-2 font-semibold text-green-600">Kargo</span>
            </div>
            <div class="w-16 h-1 bg-green-500"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white font-bold">✓</div>
                <span class="ml-2 font-semibold text-green-600">Ödeme</span>
            </div>
            <div class="w-16 h-1 bg-primary-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-600 text-white font-bold">4</div>
                <span class="ml-2 font-semibold text-primary-600">Onay</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Review -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-6">Sipariş Özeti</h2>

                <!-- Shipping Address -->
                <div class="mb-6">
                    <h3 class="font-semibold mb-3">Teslimat Adresi</h3>
                    <div class="text-gray-700">
                        <div>{{ $checkoutData['shipping_name'] }}</div>
                        <div>{{ $checkoutData['shipping_phone'] }}</div>
                        <div>{{ $checkoutData['shipping_city'] }}, {{ $checkoutData['shipping_district'] }}</div>
                        <div>{{ $checkoutData['shipping_address'] }}</div>
                        @if(isset($checkoutData['shipping_postal_code']))
                            <div>{{ $checkoutData['shipping_postal_code'] }}</div>
                        @endif
                    </div>
                </div>

                <!-- Billing Address -->
                @if(isset($checkoutData['billing_name']) && $checkoutData['billing_name'] != $checkoutData['shipping_name'])
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3">Fatura Adresi</h3>
                        <div class="text-gray-700">
                            <div>{{ $checkoutData['billing_name'] }}</div>
                            <div>{{ $checkoutData['billing_phone'] }}</div>
                            <div>{{ $checkoutData['billing_city'] }}, {{ $checkoutData['billing_district'] }}</div>
                            <div>{{ $checkoutData['billing_address'] }}</div>
                            @if(isset($checkoutData['billing_postal_code']))
                                <div>{{ $checkoutData['billing_postal_code'] }}</div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Shipping Company -->
                <div class="mb-6">
                    <h3 class="font-semibold mb-3">Kargo Firması</h3>
                    <div class="text-gray-700">
                        <div>{{ $shippingCompany->name }}</div>
                        <div class="text-sm">Tahmini Teslimat: {{ $shippingCompany->estimated_days }} gün</div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-6">
                    <h3 class="font-semibold mb-3">Ödeme Yöntemi</h3>
                    <div class="text-gray-700">
                        @if($checkoutData['payment_method'] == 'credit_card')
                            Kredi Kartı
                        @elseif($checkoutData['payment_method'] == 'bank_transfer')
                            Havale/EFT
                        @elseif($checkoutData['payment_method'] == 'cash_on_delivery')
                            Kapıda Ödeme
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div>
                    <h3 class="font-semibold mb-3">Sipariş Detayları</h3>
                    <div class="space-y-3">
                        @foreach($totals['items'] as $item)
                            <div class="flex items-center justify-between border-b pb-3">
                                <div class="flex items-center space-x-3">
                                    @if($item['product']->images->first())
                                        <img src="{{ asset('storage/' . $item['product']->images->first()->image_path) }}" 
                                             alt="{{ $item['product']->name }}" 
                                             class="w-16 h-16 object-cover rounded">
                                    @endif
                                    <div>
                                        <div class="font-semibold">{{ $item['product']->name }}</div>
                                        <div class="text-sm text-gray-600">Adet: {{ $item['quantity'] }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">{{ number_format($item['total'], 2) }} ₺</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <label class="flex items-start">
                    <input type="checkbox" id="termsCheckbox" required class="mt-1 mr-3">
                    <span class="text-sm text-gray-700">
                        <a href="{{ route('distance-sales') }}" target="_blank" class="text-primary-600 hover:text-primary-800">Mesafeli Satış Sözleşmesi</a>'ni ve 
                        <a href="{{ route('privacy') }}" target="_blank" class="text-primary-600 hover:text-primary-800">Gizlilik Politikası</a>'nı okudum, kabul ediyorum. *
                    </span>
                </label>
            </div>

            <!-- Confirm Order Form -->
            <form action="{{ route('checkout.store') }}" method="POST" id="orderForm">
                @csrf
                
                <div class="flex justify-between">
                    <a href="{{ route('checkout.step3') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                        Geri
                    </a>
                    <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                        Siparişi Onayla
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                <h3 class="text-lg font-bold mb-4">Sipariş Özeti</h3>
                
                <div class="space-y-2 mb-4">
                    @foreach($totals['items'] as $item)
                        <div class="flex justify-between text-sm">
                            <span>{{ $item['product']->name }} x{{ $item['quantity'] }}</span>
                            <span>{{ number_format($item['total'], 2) }} ₺</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Ara Toplam</span>
                        <span>{{ number_format($totals['subtotal'], 2) }} ₺</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>KDV</span>
                        <span>{{ number_format($totals['tax'], 2) }} ₺</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Kargo</span>
                        <span>
                            @if($shippingCost == 0)
                                <span class="text-green-600">Ücretsiz</span>
                            @else
                                {{ number_format($shippingCost, 2) }} ₺
                            @endif
                        </span>
                    </div>
                    @if($totals['discountAmount'] > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>İndirim</span>
                            <span>-{{ number_format($totals['discountAmount'], 2) }} ₺</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm font-bold text-lg border-t pt-2">
                        <span>Toplam</span>
                        <span>{{ number_format($total, 2) }} ₺</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const termsCheckbox = document.getElementById('termsCheckbox');
    if (!termsCheckbox.checked) {
        e.preventDefault();
        alert('Lütfen mesafeli satış sözleşmesini ve gizlilik politikasını kabul edin.');
        return false;
    }
});
</script>
@endpush
@endsection


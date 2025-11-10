@extends('layouts.app')

@section('title', 'Ödeme - Adım 2: Kargo Seçimi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white font-bold">✓</div>
                <span class="ml-2 font-semibold text-green-600">Adres</span>
            </div>
            <div class="w-16 h-1 bg-primary-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-600 text-white font-bold">2</div>
                <span class="ml-2 font-semibold text-primary-600">Kargo</span>
            </div>
            <div class="w-16 h-1 bg-gray-300"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-bold">3</div>
                <span class="ml-2 text-gray-600">Ödeme</span>
            </div>
            <div class="w-16 h-1 bg-gray-300"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-bold">4</div>
                <span class="ml-2 text-gray-600">Onay</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Shipping Options -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Kargo Firması Seçimi</h2>

                <form action="{{ route('checkout.storeStep2') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        @forelse($shippingOptions as $option)
                            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('shipping_company_id', $selectedShipping) == $option['company']->id ? 'border-primary-500 bg-primary-50' : '' }}">
                                <input type="radio" name="shipping_company_id" value="{{ $option['company']->id }}" 
                                       {{ old('shipping_company_id', $selectedShipping) == $option['company']->id ? 'checked' : '' }}
                                       class="mt-1 mr-4" required onchange="updateShippingCost({{ $option['cost'] }})">
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <div class="font-semibold text-lg">{{ $option['company']->name }}</div>
                                            @if($option['company']->estimated_days)
                                                <div class="text-sm text-gray-600">Tahmini Teslimat: {{ $option['company']->estimated_days }} gün</div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-lg text-primary-600">
                                                @if($option['cost'] == 0)
                                                    <span class="text-green-600">Ücretsiz</span>
                                                @else
                                                    {{ number_format($option['cost'], 2) }} ₺
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($option['company']->free_shipping_threshold && $totals['subtotal'] < $option['company']->free_shipping_threshold)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ number_format($option['company']->free_shipping_threshold - $totals['subtotal'], 2) }} ₺ daha alışveriş yapın, ücretsiz kargo kazanın!
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Kargo firması bulunamadı.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('checkout.step1') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                            Geri
                        </a>
                        <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                            Devam Et
                        </button>
                    </div>
                </form>
            </div>
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
                    <div class="flex justify-between text-sm" id="shippingCost">
                        <span>Kargo</span>
                        <span>{{ number_format($shippingCost, 2) }} ₺</span>
                    </div>
                    @if($totals['discountAmount'] > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>İndirim</span>
                            <span>-{{ number_format($totals['discountAmount'], 2) }} ₺</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm font-bold text-lg border-t pt-2">
                        <span>Toplam</span>
                        <span id="totalAmount">{{ number_format($totals['subtotal'] + $totals['tax'] + $shippingCost - $totals['discountAmount'], 2) }} ₺</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateShippingCost(cost) {
    const subtotal = {{ $totals['subtotal'] }};
    const tax = {{ $totals['tax'] }};
    const discount = {{ $totals['discountAmount'] }};
    const total = subtotal + tax + cost - discount;
    
    document.getElementById('shippingCost').innerHTML = `
        <span>Kargo</span>
        <span>${cost == 0 ? '<span class="text-green-600">Ücretsiz</span>' : cost.toFixed(2) + ' ₺'}</span>
    `;
    document.getElementById('totalAmount').textContent = total.toFixed(2) + ' ₺';
}
</script>
@endpush
@endsection


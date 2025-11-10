@extends('layouts.app')

@section('title', 'Ödeme - Adım 3: Ödeme Yöntemi')

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
            <div class="w-16 h-1 bg-accent"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-accent text-white font-bold">3</div>
                <span class="ml-2 font-semibold text-accent">Ödeme</span>
            </div>
            <div class="w-16 h-1 bg-gray-300"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-bold">4</div>
                <span class="ml-2 text-gray-600">Onay</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Payment Methods -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Ödeme Yöntemi Seçimi</h2>

                <form action="{{ route('checkout.storeStep3') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        @if(isset($paymentMethods['credit_card']))
                            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('payment_method', $selectedPaymentMethod) == 'credit_card' ? 'border-accent bg-primary-50' : '' }}">
                                <input type="radio" name="payment_method" value="credit_card" 
                                       {{ old('payment_method', $selectedPaymentMethod) == 'credit_card' ? 'checked' : '' }}
                                       class="mt-1 mr-4" required>
                                <div class="flex-1">
                                    <div class="font-semibold text-lg">{{ $paymentMethods['credit_card'] }}</div>
                                    <div class="text-sm text-gray-600 mt-1">Kredi kartı ile güvenli ödeme</div>
                                    <div class="flex items-center mt-2 space-x-2">
                                        <img src="https://via.placeholder.com/40x25?text=MC" alt="Mastercard" class="h-6">
                                        <img src="https://via.placeholder.com/40x25?text=VS" alt="Visa" class="h-6">
                                        <img src="https://via.placeholder.com/40x25?text=AX" alt="American Express" class="h-6">
                                    </div>
                                </div>
                            </label>
                        @endif

                        @if(isset($paymentMethods['bank_transfer']))
                            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('payment_method', $selectedPaymentMethod) == 'bank_transfer' ? 'border-accent bg-primary-50' : '' }}">
                                <input type="radio" name="payment_method" value="bank_transfer" 
                                       {{ old('payment_method', $selectedPaymentMethod) == 'bank_transfer' ? 'checked' : '' }}
                                       class="mt-1 mr-4" required>
                                <div class="flex-1">
                                    <div class="font-semibold text-lg">{{ $paymentMethods['bank_transfer'] }}</div>
                                    <div class="text-sm text-gray-600 mt-1">Havale/EFT ile ödeme. Sipariş onayından sonra banka hesap bilgileri gösterilecektir.</div>
                                </div>
                            </label>
                        @endif

                        @if(isset($paymentMethods['cash_on_delivery']))
                            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('payment_method', $selectedPaymentMethod) == 'cash_on_delivery' ? 'border-accent bg-primary-50' : '' }}">
                                <input type="radio" name="payment_method" value="cash_on_delivery" 
                                       {{ old('payment_method', $selectedPaymentMethod) == 'cash_on_delivery' ? 'checked' : '' }}
                                       class="mt-1 mr-4" required>
                                <div class="flex-1">
                                    <div class="font-semibold text-lg">{{ $paymentMethods['cash_on_delivery'] }}</div>
                                    <div class="text-sm text-gray-600 mt-1">Kapıda ödeme ile teslimatta nakit olarak ödeme yapabilirsiniz.</div>
                                </div>
                            </label>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('checkout.step2') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                            Geri
                        </a>
                        <button type="submit" class="bg-accent text-white px-6 py-3 rounded-lg font-semibold hover:bg-accent-600 transition">
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

                <!-- Shipping Info -->
                <div class="border-t pt-4 mt-4">
                    <div class="text-sm">
                        <div class="font-semibold mb-1">Kargo Firması:</div>
                        <div class="text-gray-600">{{ $shippingCompany->name }}</div>
                        <div class="text-gray-600 mt-1">Tahmini Teslimat: {{ $shippingCompany->estimated_days }} gün</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


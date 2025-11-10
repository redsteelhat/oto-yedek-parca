@extends('layouts.app')

@section('title', 'Ödeme - Adım 1: Adres Bilgileri')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-600 text-white font-bold">1</div>
                <span class="ml-2 font-semibold text-primary-600">Adres</span>
            </div>
            <div class="w-16 h-1 bg-gray-300"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-bold">2</div>
                <span class="ml-2 text-gray-600">Kargo</span>
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
        <!-- Address Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Teslimat Adresi</h2>

                <form action="{{ route('checkout.storeStep1') }}" method="POST">
                    @csrf

                    <!-- Existing Addresses -->
                    @if($addresses->count() > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Kayıtlı Adreslerim</label>
                            <div class="space-y-3">
                                @foreach($addresses as $address)
                                    <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('address_id', $checkoutData['address_id'] ?? '') == $address->id ? 'border-primary-500 bg-primary-50' : '' }}">
                                        <input type="radio" name="address_id" value="{{ $address->id }}" 
                                               {{ old('address_id', $checkoutData['address_id'] ?? '') == $address->id ? 'checked' : '' }}
                                               class="mt-1 mr-3" onchange="toggleAddressForm()">
                                        <div class="flex-1">
                                            <div class="font-semibold">{{ $address->title }}</div>
                                            <div class="text-sm text-gray-600">{{ $address->full_name }}</div>
                                            <div class="text-sm text-gray-600">{{ $address->phone }}</div>
                                            <div class="text-sm text-gray-600">{{ $address->full_address }}</div>
                                            @if($address->is_default)
                                                <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Varsayılan</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-6 text-center">
                            <span class="text-gray-500">veya</span>
                        </div>
                    @endif

                    <!-- New Address Form -->
                    <div id="addressForm" class="{{ old('address_id', $checkoutData['address_id'] ?? '') ? 'hidden' : '' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                                <input type="text" name="shipping_name" value="{{ old('shipping_name', $checkoutData['shipping_name'] ?? auth()->user()->name) }}" required
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @error('shipping_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone', $checkoutData['shipping_phone'] ?? auth()->user()->phone) }}" required
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @error('shipping_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Şehir *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $checkoutData['shipping_city'] ?? '') }}" required
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @error('shipping_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">İlçe *</label>
                                <input type="text" name="shipping_district" value="{{ old('shipping_district', $checkoutData['shipping_district'] ?? '') }}" required
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @error('shipping_district')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Adres *</label>
                                <textarea name="shipping_address" rows="3" required
                                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('shipping_address', $checkoutData['shipping_address'] ?? '') }}</textarea>
                                @error('shipping_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Posta Kodu</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code', $checkoutData['shipping_postal_code'] ?? '') }}"
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @error('shipping_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="mt-6 border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Fatura Adresi</h3>
                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="billing_same_as_shipping" value="1" 
                                       {{ old('billing_same_as_shipping', !isset($checkoutData['billing_name']) || $checkoutData['billing_name'] == ($checkoutData['shipping_name'] ?? '')) ? 'checked' : '' }}
                                       class="mr-2" onchange="toggleBillingForm()">
                                <span class="text-sm text-gray-700">Fatura adresi teslimat adresi ile aynı</span>
                            </label>
                        </div>

                        <div id="billingForm" class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad</label>
                                    <input type="text" name="billing_name" value="{{ old('billing_name', $checkoutData['billing_name'] ?? '') }}"
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                                    <input type="text" name="billing_phone" value="{{ old('billing_phone', $checkoutData['billing_phone'] ?? '') }}"
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Şehir</label>
                                    <input type="text" name="billing_city" value="{{ old('billing_city', $checkoutData['billing_city'] ?? '') }}"
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">İlçe</label>
                                    <input type="text" name="billing_district" value="{{ old('billing_district', $checkoutData['billing_district'] ?? '') }}"
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Adres</label>
                                    <textarea name="billing_address" rows="3"
                                              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('billing_address', $checkoutData['billing_address'] ?? '') }}</textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Posta Kodu</label>
                                    <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code', $checkoutData['billing_postal_code'] ?? '') }}"
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
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
                    @if($totals['discountAmount'] > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>İndirim</span>
                            <span>-{{ number_format($totals['discountAmount'], 2) }} ₺</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm font-bold text-lg border-t pt-2">
                        <span>Toplam</span>
                        <span>{{ number_format($totals['subtotal'] + $totals['tax'] - $totals['discountAmount'], 2) }} ₺</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleAddressForm() {
    const addressForm = document.getElementById('addressForm');
    const selectedAddress = document.querySelector('input[name="address_id"]:checked');
    if (selectedAddress) {
        addressForm.classList.add('hidden');
    } else {
        addressForm.classList.remove('hidden');
    }
}

function toggleBillingForm() {
    const billingForm = document.getElementById('billingForm');
    const sameAsShipping = document.querySelector('input[name="billing_same_as_shipping"]').checked;
    if (sameAsShipping) {
        billingForm.classList.add('hidden');
    } else {
        billingForm.classList.remove('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleBillingForm();
});
</script>
@endpush
@endsection


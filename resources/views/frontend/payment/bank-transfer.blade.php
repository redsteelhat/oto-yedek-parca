@extends('layouts.app')

@section('title', 'Havale/EFT Ödeme Bilgileri')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h1 class="text-2xl font-bold mb-4">Havale/EFT Ödeme Bilgileri</h1>
        
        <div class="mb-6">
            <p class="text-gray-700 mb-2">Sipariş No: <strong>{{ $order->order_number }}</strong></p>
            <p class="text-gray-700 mb-2">Ödeme Tutarı: <strong class="text-primary-600">{{ number_format($order->total, 2) }} ₺</strong></p>
        </div>

        @if($bankAccounts['bank_name'])
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Banka Hesap Bilgileri</h2>
                
                <div class="space-y-3">
                    @if($bankAccounts['bank_name'])
                        <div class="flex justify-between">
                            <span class="font-medium">Banka:</span>
                            <span>{{ $bankAccounts['bank_name'] }}</span>
                        </div>
                    @endif
                    
                    @if($bankAccounts['account_holder'])
                        <div class="flex justify-between">
                            <span class="font-medium">Hesap Sahibi:</span>
                            <span>{{ $bankAccounts['account_holder'] }}</span>
                        </div>
                    @endif
                    
                    @if($bankAccounts['iban'])
                        <div class="flex justify-between">
                            <span class="font-medium">IBAN:</span>
                            <span class="font-mono">{{ $bankAccounts['iban'] }}</span>
                        </div>
                    @endif
                    
                    @if($bankAccounts['account_number'])
                        <div class="flex justify-between">
                            <span class="font-medium">Hesap No:</span>
                            <span>{{ $bankAccounts['account_number'] }}</span>
                        </div>
                    @endif
                    
                    @if($bankAccounts['branch'])
                        <div class="flex justify-between">
                            <span class="font-medium">Şube:</span>
                            <span>{{ $bankAccounts['branch'] }}</span>
                        </div>
                    @endif
                </div>

                @if($bankAccounts['notes'])
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-sm text-yellow-800">{{ $bankAccounts['notes'] }}</p>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-yellow-800">Banka hesap bilgileri henüz yapılandırılmamış. Lütfen müşteri hizmetleri ile iletişime geçin.</p>
            </div>
        @endif

        <!-- Receipt Upload Form -->
        @if(!$order->bank_transfer_receipt)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Dekont Yükleme</h3>
                
                <form action="{{ route('payment.bank-transfer.upload', $order) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Havale/EFT Dekontu (JPG, PNG, PDF - Max 5MB) *
                        </label>
                        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('receipt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notlar (Opsiyonel)
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                  placeholder="Ödeme ile ilgili ek bilgiler...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                        Dekontu Yükle
                    </button>
                </form>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-2 text-green-800">✓ Dekont Yüklendi</h3>
                <p class="text-sm text-green-700 mb-4">
                    Yükleme Tarihi: {{ $order->bank_transfer_receipt_uploaded_at->format('d.m.Y H:i') }}
                </p>
                <a href="{{ asset('storage/' . $order->bank_transfer_receipt) }}" target="_blank" 
                   class="text-primary-600 hover:text-primary-800 underline">
                    Dekontu Görüntüle
                </a>
            </div>
        @endif

        <!-- Order Status -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-2">Sipariş Durumu</h3>
            <p class="text-gray-700">
                @if($order->payment_status === 'pending')
                    Ödeme onayı bekleniyor. Dekont yükledikten sonra ödeme onayı için bekleyiniz.
                @elseif($order->payment_status === 'paid')
                    Ödeme onaylandı. Siparişiniz hazırlanıyor.
                @else
                    Ödeme durumu: {{ $order->payment_status }}
                @endif
            </p>
        </div>

        <div class="mt-6">
            <a href="{{ route('account.order-detail', $order) }}" class="text-primary-600 hover:text-primary-800 underline">
                Sipariş Detaylarına Dön
            </a>
        </div>
    </div>
</div>
@endsection


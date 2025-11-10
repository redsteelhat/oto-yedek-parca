@extends('layouts.admin')

@section('title', 'Havale/EFT Detay')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-bold mb-6">Havale/EFT Detay - {{ $order->order_number }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Order Info -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Sipariş Bilgileri</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Sipariş No:</span>
                    <span class="font-semibold">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tarih:</span>
                    <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tutar:</span>
                    <span class="font-semibold text-primary-600">{{ number_format($order->total, 2) }} ₺</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Durum:</span>
                    <span class="px-2 py-1 rounded text-sm bg-yellow-100 text-yellow-800">{{ $order->status }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Müşteri Bilgileri</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Ad Soyad:</span>
                    <span>{{ $order->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">E-posta:</span>
                    <span>{{ $order->user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Telefon:</span>
                    <span>{{ $order->shipping_phone }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Info -->
    @if($order->bank_transfer_receipt)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Dekont Bilgileri</h2>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Yükleme Tarihi:</span>
                    <span>{{ $order->bank_transfer_receipt_uploaded_at->format('d.m.Y H:i') }}</span>
                </div>
                @if($order->bank_transfer_notes)
                    <div class="mt-4">
                        <span class="text-gray-600 block mb-2">Notlar:</span>
                        <p class="bg-white p-3 rounded">{{ $order->bank_transfer_notes }}</p>
                    </div>
                @endif
            </div>
            <a href="{{ asset('storage/' . $order->bank_transfer_receipt) }}" target="_blank" 
               class="inline-block bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Dekontu Görüntüle
            </a>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
            <p class="text-yellow-800">Dekont henüz yüklenmemiş.</p>
        </div>
    @endif

    <!-- Order Items -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold mb-4">Sipariş Detayları</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">{{ $item->product_name }}</td>
                            <td class="px-6 py-4">{{ $item->quantity }}</td>
                            <td class="px-6 py-4">{{ number_format($item->price, 2) }} ₺</td>
                            <td class="px-6 py-4">{{ number_format($item->total, 2) }} ₺</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-4">
        <form action="{{ route('admin.bank-transfers.reject', $order) }}" method="POST" onsubmit="return confirm('Bu ödemeyi reddetmek istediğinizden emin misiniz?');">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Red Sebebi *</label>
                <textarea name="rejection_reason" rows="3" required
                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>
            <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                Reddet
            </button>
        </form>
        
        @if($order->bank_transfer_receipt)
            <form action="{{ route('admin.bank-transfers.approve', $order) }}" method="POST" onsubmit="return confirm('Bu ödemeyi onaylamak istediğinizden emin misiniz?');">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    Onayla
                </button>
            </form>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.bank-transfers.index') }}" class="text-primary-600 hover:text-primary-800 underline">
            ← Geri Dön
        </a>
    </div>
</div>
@endsection


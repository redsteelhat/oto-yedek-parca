@extends('layouts.app')

@section('title', 'Sipariş Onayı')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center mb-8">
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Siparişiniz Alındı!</h1>
        <p class="text-gray-600 mb-2">Sipariş numaranız: <strong class="text-primary-600">{{ $order->order_number }}</strong></p>
        <p class="text-gray-600">Siparişiniz en kısa sürede işleme alınacaktır.</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6">Sipariş Detayları</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Teslimat Adresi</h3>
                <p class="text-gray-600">{{ $order->shipping_name }}</p>
                <p class="text-gray-600">{{ $order->shipping_phone }}</p>
                <p class="text-gray-600">{{ $order->shipping_city }}, {{ $order->shipping_district }}</p>
                <p class="text-gray-600">{{ $order->shipping_address }}</p>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Sipariş Bilgileri</h3>
                <p class="text-gray-600"><strong>Durum:</strong> {{ ucfirst($order->status) }}</p>
                <p class="text-gray-600"><strong>Ödeme Durumu:</strong> {{ ucfirst($order->payment_status) }}</p>
                <p class="text-gray-600"><strong>Ödeme Yöntemi:</strong> {{ ucfirst($order->payment_method) }}</p>
                <p class="text-gray-600"><strong>Tarih:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="border-t pt-6">
            <h3 class="font-semibold text-gray-700 mb-4">Sipariş Kalemleri</h3>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->price, 2) }} ₺</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($item->total, 2) }} ₺</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-6 flex justify-end">
                <div class="text-right">
                    <p class="text-sm text-gray-600">Ara Toplam: {{ number_format($order->subtotal, 2) }} ₺</p>
                    <p class="text-sm text-gray-600">KDV: {{ number_format($order->tax_amount, 2) }} ₺</p>
                    <p class="text-sm text-gray-600">Kargo: {{ number_format($order->shipping_cost, 2) }} ₺</p>
                    @if($order->discount_amount > 0)
                        <p class="text-sm text-red-600">İndirim: -{{ number_format($order->discount_amount, 2) }} ₺</p>
                    @endif
                    <p class="text-lg font-bold text-gray-900 mt-2">Toplam: {{ number_format($order->total, 2) }} ₺</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('home') }}" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition inline-block">
            Ana Sayfaya Dön
        </a>
    </div>
</div>
@endsection


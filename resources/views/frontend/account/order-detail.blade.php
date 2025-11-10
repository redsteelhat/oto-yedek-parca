@extends('layouts.app')

@section('title', 'Sipariş Detayı: ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('account.orders') }}" class="text-primary-600 hover:text-primary-800">← Siparişlerime Dön</a>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-6">Sipariş Detayı: {{ $order->order_number }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Sipariş Kalemleri</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-4 border-b pb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                                <p class="text-sm text-gray-600">Adet: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ number_format($item->total, 2) }} ₺</p>
                                <p class="text-sm text-gray-500">Birim: {{ number_format($item->price, 2) }} ₺</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 pt-6 border-t">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Ara Toplam:</span>
                        <span>{{ number_format($order->subtotal, 2) }} ₺</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span>KDV:</span>
                        <span>{{ number_format($order->tax_amount, 2) }} ₺</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span>Kargo:</span>
                        <span>{{ number_format($order->shipping_cost, 2) }} ₺</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-sm mb-2 text-red-600">
                            <span>İndirim:</span>
                            <span>-{{ number_format($order->discount_amount, 2) }} ₺</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t">
                        <span>Toplam:</span>
                        <span class="text-primary-600">{{ number_format($order->total, 2) }} ₺</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Teslimat Adresi</h2>
                <p class="text-gray-700">{{ $order->shipping_name }}</p>
                <p class="text-gray-700">{{ $order->shipping_phone }}</p>
                <p class="text-gray-700">{{ $order->shipping_city }}, {{ $order->shipping_district }}</p>
                <p class="text-gray-700">{{ $order->shipping_address }}</p>
                @if($order->shipping_postal_code)
                    <p class="text-gray-700">Posta Kodu: {{ $order->shipping_postal_code }}</p>
                @endif
            </div>
        </div>

        <!-- Order Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h2 class="text-xl font-bold mb-4">Sipariş Bilgileri</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Sipariş No</p>
                        <p class="font-semibold">{{ $order->order_number }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Sipariş Tarihi</p>
                        <p class="font-semibold">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Durum</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold mt-1
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Ödeme Durumu</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold mt-1
                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Ödeme Yöntemi</p>
                        <p class="font-semibold">{{ ucfirst($order->payment_method) }}</p>
                    </div>

                    @if($order->tracking_number)
                        <div>
                            <p class="text-sm text-gray-600">Kargo Takip No</p>
                            <p class="font-semibold">{{ $order->tracking_number }}</p>
                            @if($order->cargo_company)
                                <p class="text-sm text-gray-500">{{ $order->cargo_company }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


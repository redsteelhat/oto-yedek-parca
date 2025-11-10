@extends('layouts.admin')

@section('title', 'Sipariş Detayı: ' . $order->order_number)
@section('page-title', 'Sipariş Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Order Info -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Sipariş Bilgileri</h2>
            <div class="flex space-x-2">
                <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Yazdır
                </button>
                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    Fatura (PDF)
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">Sipariş No</p>
                <p class="font-semibold text-lg">{{ $order->order_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Tarih</p>
                <p class="font-semibold">{{ $order->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Durum</p>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="inline">
                    @csrf
                    <select name="status" onchange="this.form.submit()" class="border rounded px-3 py-2 font-semibold
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800' : '' }}">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Beklemede</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Hazırlanıyor</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kargoda</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>İptal</option>
                        <option value="returned" {{ $order->status === 'returned' ? 'selected' : '' }}>İade</option>
                    </select>
                </form>
            </div>
            <div>
                <p class="text-sm text-gray-600">Ödeme Durumu</p>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Sipariş Detayları</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adet</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @if($item->product && $item->product->primaryImage)
                                            <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" alt="{{ $item->product_name }}" class="h-10 w-10 rounded object-cover mr-3">
                                        @endif
                                        <div>
                                            <div class="font-medium">{{ $item->product_name }}</div>
                                            @if($item->product)
                                                <a href="{{ route('admin.products.edit', $item->product) }}" class="text-xs text-primary-600 hover:text-primary-800">Ürünü Düzenle</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $item->product_sku }}</td>
                                <td class="px-4 py-3 text-sm">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm">{{ number_format($item->price, 2) }} ₺</td>
                                <td class="px-4 py-3 text-sm font-semibold">{{ number_format($item->total, 2) }} ₺</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-semibold">Ara Toplam:</td>
                            <td class="px-4 py-3 font-semibold">{{ number_format($order->subtotal, 2) }} ₺</td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-red-600">İndirim:</td>
                                <td class="px-4 py-3 text-red-600">-{{ number_format($order->discount_amount, 2) }} ₺</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right">KDV:</td>
                            <td class="px-4 py-3">{{ number_format($order->tax_amount, 2) }} ₺</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right">Kargo:</td>
                            <td class="px-4 py-3">{{ number_format($order->shipping_cost, 2) }} ₺</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-bold text-lg">Toplam:</td>
                            <td class="px-4 py-3 font-bold text-lg">{{ number_format($order->total, 2) }} ₺</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Shipping & Billing Addresses -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold mb-2">Teslimat Adresi</h3>
                <div class="bg-gray-50 rounded p-4">
                    <p class="font-medium">{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                    <p>{{ $order->shipping_postal_code }}</p>
                    <p>Tel: {{ $order->shipping_phone }}</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2">Fatura Adresi</h3>
                <div class="bg-gray-50 rounded p-4">
                    <p class="font-medium">{{ $order->billing_name ?? $order->shipping_name }}</p>
                    <p>{{ $order->billing_address ?? $order->shipping_address }}</p>
                    <p>{{ $order->billing_district ?? $order->shipping_district }} / {{ $order->billing_city ?? $order->shipping_city }}</p>
                    <p>{{ $order->billing_postal_code ?? $order->shipping_postal_code }}</p>
                    <p>Tel: {{ $order->billing_phone ?? $order->shipping_phone }}</p>
                </div>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Kargo Bilgileri</h3>
            <form action="{{ route('admin.orders.tracking', $order) }}" method="POST" class="bg-gray-50 rounded p-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kargo Firması</label>
                        <input type="text" name="cargo_company" value="{{ old('cargo_company', $order->cargo_company) }}" required
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Takip Numarası</label>
                        <div class="flex space-x-2">
                            <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" required
                                   class="w-full border rounded px-3 py-2">
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                                Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @if($order->cargo_company)
                <div class="mt-4 flex space-x-2">
                    <form action="{{ route('admin.orders.create-shipping-label', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Otomatik Kargo Etiketi Oluştur
                        </button>
                    </form>
                    @if($order->tracking_number)
                        <form action="{{ route('admin.orders.track-shipping', $order) }}" method="GET" class="inline">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Kargo Durumu Sorgula
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- Order Notes -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Sipariş Notları</h3>
            <form action="{{ route('admin.orders.notes', $order) }}" method="POST">
                @csrf
                <textarea name="notes" rows="4" class="w-full border rounded px-3 py-2">{{ old('notes', $order->notes) }}</textarea>
                <button type="submit" class="mt-2 bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    Notları Kaydet
                </button>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Müşteri Bilgileri</h3>
            @if($order->user)
                <div class="space-y-2">
                    <p><span class="font-medium">Ad Soyad:</span> {{ $order->user->name }}</p>
                    <p><span class="font-medium">E-posta:</span> {{ $order->user->email }}</p>
                    <p><span class="font-medium">Telefon:</span> {{ $order->user->phone ?? '-' }}</p>
                    <p><span class="font-medium">Kullanıcı Tipi:</span> {{ ucfirst($order->user->user_type ?? 'customer') }}</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.customers.show', $order->user) }}" class="text-primary-600 hover:text-primary-800 underline">
                        Müşteri Detayları →
                    </a>
                </div>
            @else
                <p class="text-gray-500">Misafir siparişi</p>
            @endif
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Ödeme Bilgileri</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Ödeme Yöntemi:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                @if($order->payment_transaction_id)
                    <p><span class="font-medium">İşlem No:</span> {{ $order->payment_transaction_id }}</p>
                @endif
                @if($order->coupon_code)
                    <p><span class="font-medium">Kupon:</span> {{ $order->coupon_code }}</p>
                @endif
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Sipariş Özeti</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Ara Toplam:</span>
                    <span>{{ number_format($order->subtotal, 2) }} ₺</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>İndirim:</span>
                        <span>-{{ number_format($order->discount_amount, 2) }} ₺</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span>KDV:</span>
                    <span>{{ number_format($order->tax_amount, 2) }} ₺</span>
                </div>
                <div class="flex justify-between">
                    <span>Kargo:</span>
                    <span>{{ number_format($order->shipping_cost, 2) }} ₺</span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Toplam:</span>
                    <span>{{ number_format($order->total, 2) }} ₺</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">İşlemler</h3>
            <div class="space-y-2">
                @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Bu siparişi iptal etmek istediğinize emin misiniz?');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Siparişi İptal Et
                        </button>
                    </form>
                @endif
                @if($order->status === 'delivered')
                    <form action="{{ route('admin.orders.return', $order) }}" method="POST" onsubmit="return confirm('Bu siparişi iade olarak işaretlemek istediğinize emin misiniz?');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                            İade Olarak İşaretle
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}" class="text-primary-600 hover:text-primary-800 underline">
        ← Siparişlere Dön
    </a>
</div>
@endsection


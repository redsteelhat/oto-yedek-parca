@extends('layouts.admin')

@section('title', 'Müşteri Detayı: ' . $customer->name)
@section('page-title', 'Müşteri Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Müşteri Bilgileri -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Müşteri Bilgileri</h2>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Düzenle
            </a>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Ad Soyad</p>
                <p class="font-semibold text-lg">{{ $customer->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">E-posta</p>
                <p class="font-semibold">{{ $customer->email }}</p>
            </div>

            @if($customer->phone)
                <div>
                    <p class="text-sm text-gray-600">Telefon</p>
                    <p class="font-semibold">{{ $customer->phone }}</p>
                </div>
            @endif

            <div>
                <p class="text-sm text-gray-600">Kullanıcı Tipi</p>
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                    @if($customer->user_type == 'admin') bg-purple-100 text-purple-800
                    @elseif($customer->user_type == 'dealer') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    @if($customer->user_type == 'admin') Admin
                    @elseif($customer->user_type == 'dealer') Bayi
                    @else Müşteri
                    @endif
                </span>
            </div>

            @if($customer->company_name)
                <div>
                    <p class="text-sm text-gray-600">Şirket Adı</p>
                    <p class="font-semibold">{{ $customer->company_name }}</p>
                </div>
            @endif

            @if($customer->tax_number)
                <div>
                    <p class="text-sm text-gray-600">Vergi Numarası</p>
                    <p class="font-semibold">{{ $customer->tax_number }}</p>
                </div>
            @endif

            <div>
                <p class="text-sm text-gray-600">Durum</p>
                @if($customer->is_verified)
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Doğrulanmış</span>
                @else
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Beklemede</span>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-600">Kayıt Tarihi</p>
                <p class="font-semibold">{{ $customer->created_at->format('d.m.Y H:i') }}</p>
            </div>

            @if($customer->notes)
                <div>
                    <p class="text-sm text-gray-600 mb-2">Notlar</p>
                    <div class="bg-gray-50 rounded p-3">
                        <p class="text-sm text-gray-900">{{ $customer->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">İstatistikler</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Toplam Sipariş</p>
                <p class="font-bold text-2xl text-primary-600">{{ $stats['total_orders'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Toplam Harcama</p>
                <p class="font-bold text-2xl text-green-600">{{ number_format($stats['total_spent'], 2) }} ₺</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Bekleyen Siparişler</p>
                <p class="font-bold text-2xl text-yellow-600">{{ $stats['pending_orders'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Adresler -->
@if($customer->addresses->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Adresler</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($customer->addresses as $address)
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold">{{ $address->title }}</h3>
                        @if($address->is_default)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Varsayılan</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $address->first_name }} {{ $address->last_name }}</p>
                    <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                    <p class="text-sm text-gray-900 mt-2">{{ $address->address }}</p>
                    <p class="text-sm text-gray-600">{{ $address->district }} / {{ $address->city }}</p>
                    <p class="text-sm text-gray-600">{{ $address->postal_code }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Siparişler -->
<div class="mt-6 bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-bold mb-4">Siparişler</h2>
    @if($customer->orders->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme Durumu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($customer->orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $order->payment_status == 'paid' ? 'Ödendi' : 'Bekliyor' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($order->total, 2) }} ₺
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 hover:text-primary-900">
                                    Detay
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-center py-4">Henüz sipariş bulunmuyor.</p>
    @endif
</div>
@endsection


@extends('layouts.admin')

@section('title', 'Müşteri Raporu')
@section('page-title', 'Müşteri Raporu')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.reports.customers') }}" class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri Tipi</label>
                <select name="user_type" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Tüm Tipler</option>
                    <option value="customer" {{ request('user_type') == 'customer' ? 'selected' : '' }}>Müşteri</option>
                    <option value="dealer" {{ request('user_type') == 'dealer' ? 'selected' : '' }}>Bayi</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Onay Durumu</label>
                <select name="is_verified" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>Onaylı</option>
                    <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>Onaysız</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                <select name="sort" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="spent" {{ request('sort') == 'spent' ? 'selected' : '' }}>Toplam Harcama</option>
                    <option value="orders" {{ request('sort') == 'orders' ? 'selected' : '' }}>Sipariş Sayısı</option>
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>En Yeni</option>
                </select>
            </div>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Filtrele
            </button>
            <a href="{{ route('admin.reports.customers', array_merge(request()->all(), ['format' => 'excel'])) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                Excel'e Aktar
            </a>
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm">
                Geri Dön
            </a>
        </div>
    </form>
</div>

<!-- Customers Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Müşteri Performansı</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad Soyad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-posta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam Sipariş</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam Harcama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ortalama Sipariş</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kayıt Tarihi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ $customer->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $customer->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $customer->phone ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                        {{ number_format($customer->total_orders ?? 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                        {{ number_format($customer->total_spent ?? 0, 2) }} ₺
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($customer->average_order_value ?? 0, 2) }} ₺
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $customer->created_at->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $customer->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $customer->is_verified ? 'Onaylı' : 'Onaysız' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        Müşteri bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


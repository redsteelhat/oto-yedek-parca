@extends('layouts.admin')

@section('title', 'Satış Raporu')
@section('page-title', 'Satış Raporu')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.reports.sales') }}" class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>İşleniyor</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Kargoya Verildi</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Durumu</label>
                <select name="payment_status" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Tüm Durumlar</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Ödendi</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Başarısız</option>
                </select>
            </div>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Filtrele
            </button>
            <a href="{{ route('admin.reports.sales', array_merge(request()->all(), ['format' => 'excel'])) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                Excel'e Aktar
            </a>
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm">
                Geri Dön
            </a>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam Sipariş</div>
        <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_orders']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam Gelir</div>
        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_revenue'], 2) }} ₺</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Ortalama Sipariş</div>
        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['average_order_value'], 2) }} ₺</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam İndirim</div>
        <div class="text-2xl font-bold text-red-600">{{ number_format($stats['total_discount'], 2) }} ₺</div>
    </div>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Sipariş Detayları</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $order->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ $order->user ? $order->user->name : 'Misafir' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($order->status == 'delivered') bg-green-100 text-green-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($order->payment_status == 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                        {{ number_format($order->total, 2) }} ₺
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Bu tarih aralığında sipariş bulunmamaktadır.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


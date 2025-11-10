@extends('layouts.admin')

@section('title', 'Siparişler')
@section('page-title', 'Siparişler')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Sipariş no veya müşteri ara..." class="border rounded px-3 py-2 flex-1">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Tüm Durumlar</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Hazırlanıyor</option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Kargoda</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
        </select>
        <select name="payment_status" class="border rounded px-3 py-2">
            <option value="">Tüm Ödeme Durumları</option>
            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Ödendi</option>
            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Başarısız</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Filtrele
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                        {{ $order->order_number }}
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $order->user->name ?? 'Misafir' }}</div>
                    <div class="text-sm text-gray-500">{{ $order->user->email ?? $order->shipping_email ?? '' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ number_format($order->total, 2) }} ₺
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800' : '' }}">
                        {{ $order->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $order->payment_status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $order->created_at->format('d.m.Y H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 hover:text-primary-900">Detay</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Sipariş bulunamadı.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection


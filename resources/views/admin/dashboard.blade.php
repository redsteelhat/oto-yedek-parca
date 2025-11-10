@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Period Filter -->
<div class="mb-6 flex justify-end">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex space-x-2">
        <select name="period" onchange="this.form.submit()" class="border rounded px-3 py-2">
            <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Günlük</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Haftalık</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Aylık</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Yıllık</option>
        </select>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Orders -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Toplam Sipariş</p>
                <p class="text-2xl font-bold">{{ $stats['total_orders'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Beklemede: {{ $stats['pending_orders'] }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Toplam Ürün</p>
                <p class="text-2xl font-bold">{{ $stats['total_products'] }}</p>
                <p class="text-xs text-red-500 mt-1">Düşük Stok: {{ $stats['low_stock_products'] }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Toplam Müşteri</p>
                <p class="text-2xl font-bold">{{ $stats['total_customers'] }}</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Toplam Gelir</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_revenue'], 2) }} ₺</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Sales Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Satış Grafiği</h3>
        <canvas id="salesChart" height="100"></canvas>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Gelir Grafiği</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
</div>

<!-- Bottom Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">En Çok Satan Ürünler</h3>
        <div class="space-y-3">
            @forelse($topProducts as $product)
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">Satış: {{ $product->total_sold ?? 0 }}</p>
                    </div>
                    <span class="text-primary-600 font-semibold">{{ number_format($product->final_price, 2) }} ₺</span>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Henüz satış verisi yok.</p>
            @endforelse
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4 text-red-600">Düşük Stok Uyarıları</h3>
        <div class="space-y-3">
            @forelse($lowStockProducts as $product)
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $product->name }}</p>
                        <p class="text-xs text-red-500">Stok: {{ $product->stock }} / Min: {{ $product->min_stock_level }}</p>
                    </div>
                    <a href="{{ route('admin.products.edit', $product) }}" class="text-primary-600 hover:text-primary-800 text-xs">
                        Düzenle
                    </a>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Düşük stok ürün bulunmuyor.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent XML Imports -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Son XML Importlar</h3>
        <div class="space-y-3">
            @forelse($recentImports as $import)
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $import->supplier->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $import->imported_items ?? 0 }} içe aktarıldı / {{ $import->failed_items ?? 0 }} hata
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $import->status === 'success' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $import->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($import->status) }}
                    </span>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Henüz import logu yok.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Son Siparişler</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sipariş No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentOrders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $order->user->name ?? 'Misafir' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ number_format($order->total, 2) }} ₺
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $order->created_at->format('d.m.Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Henüz sipariş bulunmuyor.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart');
if (salesCtx) {
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesData['labels']),
            datasets: [{
                label: 'Satış Sayısı',
                data: @json($salesData['data']),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: @json($revenueData['labels']),
            datasets: [{
                label: 'Gelir (₺)',
                data: @json($revenueData['data']),
                backgroundColor: 'rgba(147, 51, 234, 0.6)',
                borderColor: 'rgb(147, 51, 234)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('tr-TR') + ' ₺';
                        }
                    }
                }
            }
        }
    });
}
</script>
@endpush
@endsection

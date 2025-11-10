@extends('super-admin.layout')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam Tenant</div>
        <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_tenants']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Aktif Tenant</div>
        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['active_tenants']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam Kullanıcı</div>
        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_users']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam Gelir</div>
        <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_revenue'], 2) }} ₺</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Gelir Grafiği</h3>
        <canvas id="revenueChart"></canvas>
    </div>

    <!-- Tenant Growth Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Tenant Büyümesi</h3>
        <canvas id="growthChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Tenants -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Son Tenant'lar</h3>
        </div>
        <div class="p-6">
            @forelse($recentTenants as $tenant)
                <div class="mb-4 pb-4 border-b border-gray-200 last:border-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold">{{ $tenant->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $tenant->subdomain }}.site.com</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($tenant->status == 'active') bg-green-100 text-green-800
                            @elseif($tenant->status == 'suspended') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Henüz tenant yok.</p>
            @endforelse
        </div>
    </div>

    <!-- Top Tenants by Revenue -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">En Çok Gelir Getiren Tenant'lar</h3>
        </div>
        <div class="p-6">
            @forelse($topTenants as $tenant)
                <div class="mb-4 pb-4 border-b border-gray-200 last:border-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold">{{ $tenant->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $tenant->subdomain }}.site.com</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600">{{ number_format($tenant->total_revenue ?? 0, 2) }} ₺</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Henüz gelir verisi yok.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueData['labels']),
            datasets: [{
                label: 'Gelir (₺)',
                data: @json($revenueData['data']),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Growth Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'bar',
        data: {
            labels: @json($growthData['labels']),
            datasets: [{
                label: 'Yeni Tenant',
                data: @json($growthData['data']),
                backgroundColor: 'rgb(34, 197, 94)',
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection




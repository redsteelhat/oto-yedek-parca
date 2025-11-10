@extends('super-admin.layout')

@section('title', $tenant->name)
@section('page-title', 'Tenant Detay: ' . $tenant->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Temel Bilgiler</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Tenant Adı</label>
                    <p class="font-semibold">{{ $tenant->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Subdomain</label>
                    <p class="font-semibold">
                        <a href="http://{{ $tenant->subdomain }}.site.com" target="_blank" class="text-blue-600 hover:text-blue-800">
                            {{ $tenant->subdomain }}.site.com
                        </a>
                    </p>
                </div>
                @if($tenant->domain)
                    <div>
                        <label class="text-sm text-gray-600">Custom Domain</label>
                        <p class="font-semibold">{{ $tenant->domain }}</p>
                    </div>
                @endif
                @if($tenant->email)
                    <div>
                        <label class="text-sm text-gray-600">E-posta</label>
                        <p class="font-semibold">{{ $tenant->email }}</p>
                    </div>
                @endif
                @if($tenant->phone)
                    <div>
                        <label class="text-sm text-gray-600">Telefon</label>
                        <p class="font-semibold">{{ $tenant->phone }}</p>
                    </div>
                @endif
                <div>
                    <label class="text-sm text-gray-600">Durum</label>
                    <p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($tenant->status == 'active') bg-green-100 text-green-800
                            @elseif($tenant->status == 'suspended') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Abonelik Planı</label>
                    <p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($tenant->subscription_plan) }}
                        </span>
                    </p>
                </div>
                @if($tenant->subscription_expires_at)
                    <div>
                        <label class="text-sm text-gray-600">Abonelik Bitiş Tarihi</label>
                        <p class="font-semibold">{{ $tenant->subscription_expires_at->format('d.m.Y H:i') }}</p>
                    </div>
                @endif
                <div>
                    <label class="text-sm text-gray-600">Oluşturulma Tarihi</label>
                    <p class="font-semibold">{{ $tenant->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">İstatistikler</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Kullanıcı Sayısı</label>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['users_count']) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Ürün Sayısı</label>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['products_count']) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Sipariş Sayısı</label>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['orders_count']) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Kategori Sayısı</label>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['categories_count']) }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Toplam Gelir</label>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_revenue'], 2) }} ₺</p>
                </div>
            </div>
        </div>

        <!-- Limits -->
        @if($tenant->max_products || $tenant->max_users)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Limitler</h3>
                <div class="grid grid-cols-2 gap-4">
                    @if($tenant->max_products)
                        <div>
                            <label class="text-sm text-gray-600">Maksimum Ürün</label>
                            <p class="font-semibold">{{ number_format($tenant->max_products) }}</p>
                            <div class="mt-2 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($stats['products_count'] / $tenant->max_products) * 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['products_count'] }} / {{ $tenant->max_products }}</p>
                        </div>
                    @endif
                    @if($tenant->max_users)
                        <div>
                            <label class="text-sm text-gray-600">Maksimum Kullanıcı</label>
                            <p class="font-semibold">{{ number_format($tenant->max_users) }}</p>
                            <div class="mt-2 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, ($stats['users_count'] / $tenant->max_users) * 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['users_count'] }} / {{ $tenant->max_users }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Branding -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Marka Görünümü</h3>
            @if($tenant->logo)
                <div class="mb-4">
                    <label class="text-sm text-gray-600 mb-2 block">Logo</label>
                    <img src="{{ Storage::url($tenant->logo) }}" alt="Logo" class="h-20 w-auto">
                </div>
            @endif
            @if($tenant->favicon)
                <div class="mb-4">
                    <label class="text-sm text-gray-600 mb-2 block">Favicon</label>
                    <img src="{{ Storage::url($tenant->favicon) }}" alt="Favicon" class="h-16 w-auto">
                </div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600 mb-2 block">Ana Renk</label>
                    <div class="h-10 rounded border" style="background-color: {{ $tenant->primary_color }}"></div>
                    <p class="text-xs text-gray-500 mt-1">{{ $tenant->primary_color }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-2 block">İkincil Renk</label>
                    <div class="h-10 rounded border" style="background-color: {{ $tenant->secondary_color }}"></div>
                    <p class="text-xs text-gray-500 mt-1">{{ $tenant->secondary_color }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">İşlemler</h3>
            <div class="space-y-2">
                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center">
                    Düzenle
                </a>
                @if($tenant->status == 'active')
                    <form method="POST" action="{{ route('super-admin.tenants.suspend', $tenant) }}">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                            Askıya Al
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('super-admin.tenants.activate', $tenant) }}">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Aktif Et
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('super-admin.tenants.destroy', $tenant) }}" onsubmit="return confirm('Bu tenant\'ı silmek istediğinize emin misiniz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Sil
                    </button>
                </form>
                <a href="{{ route('super-admin.tenants.index') }}" class="block w-full bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-center">
                    Geri Dön
                </a>
            </div>
        </div>
    </div>
</div>
@endsection




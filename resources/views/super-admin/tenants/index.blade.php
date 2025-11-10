@extends('super-admin.layout')

@section('title', 'Tenant\'lar')
@section('page-title', 'Tenant Yönetimi')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam</div>
        <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Aktif</div>
        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['active']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Askıya Alınmış</div>
        <div class="text-2xl font-bold text-yellow-600">{{ number_format($stats['suspended']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Pasif</div>
        <div class="text-2xl font-bold text-gray-600">{{ number_format($stats['inactive']) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <form method="GET" action="{{ route('super-admin.tenants.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
            <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Tüm Durumlar</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Askıya Alınmış</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Abonelik Planı</label>
            <select name="subscription_plan" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Tüm Planlar</option>
                <option value="free" {{ request('subscription_plan') == 'free' ? 'selected' : '' }}>Ücretsiz</option>
                <option value="basic" {{ request('subscription_plan') == 'basic' ? 'selected' : '' }}>Temel</option>
                <option value="premium" {{ request('subscription_plan') == 'premium' ? 'selected' : '' }}>Premium</option>
                <option value="enterprise" {{ request('subscription_plan') == 'enterprise' ? 'selected' : '' }}>Kurumsal</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="İsim, subdomain, email..." class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="md:col-span-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Filtrele
            </button>
            <a href="{{ route('super-admin.tenants.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm ml-2">
                Temizle
            </a>
            <a href="{{ route('super-admin.tenants.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm ml-2">
                Yeni Tenant Ekle
            </a>
        </div>
    </form>
</div>

<!-- Tenants Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Tenant Listesi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İsim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subdomain</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oluşturulma</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tenants as $tenant)
                <tr class="{{ $tenant->trashed() ? 'bg-gray-100' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($tenant->logo)
                                <img src="{{ Storage::url($tenant->logo) }}" alt="{{ $tenant->name }}" class="w-10 h-10 rounded mr-3">
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $tenant->name }}</div>
                                @if($tenant->email)
                                    <div class="text-sm text-gray-500">{{ $tenant->email }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="http://{{ $tenant->subdomain }}.site.com" target="_blank" class="text-blue-600 hover:text-blue-800">
                            {{ $tenant->subdomain }}.site.com
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($tenant->status == 'active') bg-green-100 text-green-800
                            @elseif($tenant->status == 'suspended') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($tenant->status) }}
                        </span>
                        @if($tenant->trashed())
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Silinmiş
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($tenant->subscription_plan) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $tenant->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-900 mr-2">Görüntüle</a>
                        <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="text-green-600 hover:text-green-900 mr-2">Düzenle</a>
                        @if($tenant->trashed())
                            <form method="POST" action="{{ route('super-admin.tenants.restore', $tenant->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-2">Geri Yükle</button>
                            </form>
                            <form method="POST" action="{{ route('super-admin.tenants.force-delete', $tenant->id) }}" class="inline" onsubmit="return confirm('Bu tenant\'ı kalıcı olarak silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Kalıcı Sil</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('super-admin.tenants.destroy', $tenant) }}" class="inline" onsubmit="return confirm('Bu tenant\'ı silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tenant bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $tenants->links() }}
</div>
@endsection




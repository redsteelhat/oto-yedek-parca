@extends('layouts.admin')

@section('title', 'Kampanyalar')
@section('page-title', 'Kampanyalar')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <a href="{{ route('admin.campaigns.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
            Yeni Kampanya Ekle
        </a>
    </div>
    <form method="GET" action="{{ route('admin.campaigns.index') }}" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Kampanya adı ara..." class="border rounded px-3 py-2">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Tüm Durumlar</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Filtrele
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kampanya Adı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İndirim</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Geçerlilik</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($campaigns as $campaign)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($campaign->image)
                                <img src="{{ asset('storage/' . $campaign->image) }}" alt="{{ $campaign->name }}" class="h-12 w-12 object-cover rounded mr-3">
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                                @if($campaign->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($campaign->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($campaign->discount_type === 'percentage')
                                %{{ number_format($campaign->discount_value, 0) }}
                            @else
                                {{ number_format($campaign->discount_value, 2) }} ₺
                            @endif
                        </div>
                        @if($campaign->min_purchase_amount)
                            <div class="text-xs text-gray-500">Min: {{ number_format($campaign->min_purchase_amount, 2) }} ₺</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">
                            {{ $campaign->type === 'product' ? 'Ürün' : ($campaign->type === 'category' ? 'Kategori' : 'Genel') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $campaign->start_date->format('d.m.Y') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $campaign->end_date->format('d.m.Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($campaign->isActive())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Pasif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="text-primary-600 hover:text-primary-900 mr-3">Düzenle</a>
                        <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detay</a>
                        <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Bu kampanyayı silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Kampanya bulunamadı.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $campaigns->appends(request()->query())->links() }}
</div>
@endsection


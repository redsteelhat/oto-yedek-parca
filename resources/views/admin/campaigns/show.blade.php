@extends('layouts.admin')

@section('title', 'Kampanya Detayı: ' . $campaign->name)
@section('page-title', 'Kampanya Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Kampanya Bilgileri</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Kampanya Adı</p>
                    <p class="font-semibold text-lg">{{ $campaign->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kampanya Tipi</p>
                    <p class="font-semibold">
                        {{ $campaign->type === 'product' ? 'Ürün Bazlı' : ($campaign->type === 'category' ? 'Kategori Bazlı' : 'Genel') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">İndirim</p>
                    <p class="font-semibold text-lg text-primary-600">
                        @if($campaign->discount_type === 'percentage')
                            %{{ number_format($campaign->discount_value, 0) }}
                        @else
                            {{ number_format($campaign->discount_value, 2) }} ₺
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Durum</p>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $campaign->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $campaign->isActive() ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>
            </div>
            
            @if($campaign->description)
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">Açıklama</p>
                    <p class="text-gray-900">{{ $campaign->description }}</p>
                </div>
            @endif
            
            @if($campaign->image)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $campaign->image) }}" alt="{{ $campaign->name }}" class="h-48 w-full object-cover rounded">
                </div>
            @endif
            
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    Düzenle
                </a>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
            <h3 class="text-lg font-bold mb-4">Detaylar</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Başlangıç Tarihi</p>
                    <p class="font-semibold">{{ $campaign->start_date->format('d.m.Y') }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600">Bitiş Tarihi</p>
                    <p class="font-semibold">{{ $campaign->end_date->format('d.m.Y') }}</p>
                </div>
                
                @if($campaign->min_purchase_amount)
                    <div>
                        <p class="text-sm text-gray-600">Minimum Alışveriş</p>
                        <p class="font-semibold">{{ number_format($campaign->min_purchase_amount, 2) }} ₺</p>
                    </div>
                @endif
                
                <div>
                    <p class="text-sm text-gray-600">Sıralama</p>
                    <p class="font-semibold">{{ $campaign->sort_order }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


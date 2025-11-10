@extends('layouts.admin')

@section('title', 'Kargo Firması Detayı: ' . $shippingCompany->name)
@section('page-title', 'Kargo Firması Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Firma Bilgileri</h2>
        
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Firma Adı</p>
                <p class="font-semibold text-lg">{{ $shippingCompany->name }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600">Kod</p>
                <p class="font-semibold font-mono">{{ $shippingCompany->code }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600">API Tipi</p>
                <p class="font-semibold">
                    {{ $shippingCompany->api_type ? ucfirst(str_replace('_', ' ', $shippingCompany->api_type)) : 'Manuel' }}
                </p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600">Durum</p>
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $shippingCompany->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $shippingCompany->is_active ? 'Aktif' : 'Pasif' }}
                </span>
            </div>
            
            @if($shippingCompany->api_type && $shippingCompany->api_type !== 'manual')
                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-2">API Bilgileri</h3>
                    @if($shippingCompany->api_url)
                        <div class="mb-2">
                            <p class="text-sm text-gray-600">API URL</p>
                            <p class="font-mono text-sm">{{ $shippingCompany->api_url }}</p>
                        </div>
                    @endif
                    @if($shippingCompany->api_key)
                        <div class="mb-2">
                            <p class="text-sm text-gray-600">API Key</p>
                            <p class="font-mono text-sm">{{ substr($shippingCompany->api_key, 0, 10) }}...</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Fiyat Bilgileri</h2>
        
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Temel Fiyat</p>
                <p class="font-semibold text-lg">{{ number_format($shippingCompany->base_price, 2) }} ₺</p>
            </div>
            
            @if($shippingCompany->price_per_kg > 0)
                <div>
                    <p class="text-sm text-gray-600">Kilo Başı Ücret</p>
                    <p class="font-semibold">{{ number_format($shippingCompany->price_per_kg, 2) }} ₺/kg</p>
                </div>
            @endif
            
            @if($shippingCompany->price_per_cm3 > 0)
                <div>
                    <p class="text-sm text-gray-600">Desi Başı Ücret</p>
                    <p class="font-semibold">{{ number_format($shippingCompany->price_per_cm3, 2) }} ₺/cm³</p>
                </div>
            @endif
            
            @if($shippingCompany->free_shipping_threshold)
                <div>
                    <p class="text-sm text-gray-600">Ücretsiz Kargo Limiti</p>
                    <p class="font-semibold text-green-600">{{ number_format($shippingCompany->free_shipping_threshold, 2) }} ₺</p>
                </div>
            @endif
            
            <div>
                <p class="text-sm text-gray-600">Tahmini Teslimat</p>
                <p class="font-semibold">{{ $shippingCompany->estimated_days }} gün</p>
            </div>
            
            @if($shippingCompany->notes)
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 mb-2">Notlar</p>
                    <p class="text-gray-900">{{ $shippingCompany->notes }}</p>
                </div>
            @endif
        </div>
        
        <div class="mt-6 flex justify-end">
            <a href="{{ route('admin.shipping-companies.edit', $shippingCompany) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Düzenle
            </a>
        </div>
    </div>
</div>
@endsection


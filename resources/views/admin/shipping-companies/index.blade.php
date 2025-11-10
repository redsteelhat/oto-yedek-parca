@extends('layouts.admin')

@section('title', 'Kargo Firmaları')
@section('page-title', 'Kargo Firmaları')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <a href="{{ route('admin.shipping-companies.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
            Yeni Kargo Firması Ekle
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Firma Adı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">API Tipi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat Bilgileri</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahmini Süre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($companies as $company)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                        <div class="text-sm text-gray-500">{{ $company->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">
                            {{ $company->api_type ? ucfirst(str_replace('_', ' ', $company->api_type)) : 'Manuel' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            <div>Base: {{ number_format($company->base_price, 2) }} ₺</div>
                            @if($company->price_per_kg > 0)
                                <div>Kilo: {{ number_format($company->price_per_kg, 2) }} ₺/kg</div>
                            @endif
                            @if($company->free_shipping_threshold)
                                <div class="text-green-600">Ücretsiz: {{ number_format($company->free_shipping_threshold, 2) }} ₺</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $company->estimated_days }} gün</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($company->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.shipping-companies.edit', $company) }}" class="text-primary-600 hover:text-primary-900 mr-3">Düzenle</a>
                        <a href="{{ route('admin.shipping-companies.show', $company) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detay</a>
                        <form action="{{ route('admin.shipping-companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('Bu kargo firmasını silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Kargo firması bulunamadı. <a href="{{ route('admin.shipping-companies.create') }}" class="text-primary-600 hover:text-primary-800">İlk firmayı ekleyin</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $companies->links() }}
</div>
@endsection


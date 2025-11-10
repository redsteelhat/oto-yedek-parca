@extends('layouts.admin')

@section('title', 'Marka Detayı: ' . $carBrand->name)
@section('page-title', 'Marka Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Marka Bilgileri -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Marka Bilgileri</h2>
            <a href="{{ route('admin.car-brands.edit', $carBrand) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Düzenle
            </a>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Marka Adı</p>
                <p class="font-semibold text-lg">{{ $carBrand->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Slug</p>
                <p class="font-semibold font-mono">{{ $carBrand->slug }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Sıralama</p>
                <p class="font-semibold">{{ $carBrand->sort_order ?? 0 }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Durum</p>
                @if($carBrand->is_active)
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                @else
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                @endif
            </div>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">İstatistikler</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Model Sayısı</p>
                <p class="font-bold text-2xl text-primary-600">{{ $carBrand->models->count() ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Modeller -->
@if($carBrand->models->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Modeller</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model Adı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yıl Sayısı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($carBrand->models as $model)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $model->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ $model->slug }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $model->years->count() ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($model->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection


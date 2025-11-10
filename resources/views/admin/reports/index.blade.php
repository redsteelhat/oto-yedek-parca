@extends('layouts.admin')

@section('title', 'Raporlar')
@section('page-title', 'Raporlar')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Sales Report Card -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Satış Raporu</h3>
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <p class="text-gray-600 text-sm mb-4">Sipariş ve satış verilerini görüntüleyin, analiz edin ve Excel'e aktarın.</p>
        <a href="{{ route('admin.reports.sales') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            Raporu Görüntüle
        </a>
    </div>

    <!-- Products Report Card -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Ürün Raporu</h3>
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
        </div>
        <p class="text-gray-600 text-sm mb-4">Ürün satış performansı, stok durumu ve en çok satan ürünleri görüntüleyin.</p>
        <a href="{{ route('admin.reports.products') }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
            Raporu Görüntüle
        </a>
    </div>

    <!-- Customers Report Card -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Müşteri Raporu</h3>
            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>
        <p class="text-gray-600 text-sm mb-4">Müşteri harcamaları, sipariş sayıları ve müşteri segmentasyonunu görüntüleyin.</p>
        <a href="{{ route('admin.reports.customers') }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
            Raporu Görüntüle
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">Raporlar Hakkında</h2>
    <div class="space-y-4 text-gray-600">
        <p><strong>Satış Raporu:</strong> Belirli bir tarih aralığındaki siparişleri, gelirleri ve ödeme durumlarını görüntüleyin. Excel formatında indirebilirsiniz.</p>
        <p><strong>Ürün Raporu:</strong> Ürünlerin satış performansını, stok durumunu ve görüntülenme sayılarını analiz edin.</p>
        <p><strong>Müşteri Raporu:</strong> Müşterilerin toplam harcamalarını, sipariş sayılarını ve ortalama sipariş değerlerini görüntüleyin.</p>
    </div>
</div>
@endsection


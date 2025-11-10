@extends('layouts.app')

@section('title', 'İade ve Değişim Koşulları')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">İade ve Değişim Koşulları</h1>
        
        <div class="prose max-w-none">
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">İade Süresi</h2>
            <p class="text-gray-700 mb-4">
                Ürünlerinizi teslim aldığınız tarihten itibaren 14 gün içinde iade edebilirsiniz.
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">İade Koşulları</h2>
            <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                <li>Ürün orijinal ambalajında ve kullanılmamış olmalıdır</li>
                <li>Faturası ile birlikte gönderilmelidir</li>
                <li>İade kargo ücreti müşteriye aittir</li>
            </ul>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">İade İşlemi</h2>
            <p class="text-gray-700 mb-4">
                İade talebinizi müşteri hizmetlerimizden oluşturabilirsiniz.
            </p>
        </div>
    </div>
</div>
@endsection


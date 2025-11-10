@extends('layouts.app')

@section('title', 'Sık Sorulan Sorular')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Sık Sorulan Sorular</h1>
        
        <div class="space-y-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Siparişim ne zaman teslim edilir?</h2>
                <p class="text-gray-700">Siparişleriniz genellikle 3-5 iş günü içinde teslim edilir.</p>
            </div>
            
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Ürün aracıma uygun mu nasıl anlayabilirim?</h2>
                <p class="text-gray-700">Ürün detay sayfasında uyumlu araç listesi bulunmaktadır. Ayrıca "Araçla Parça Bul" sayfasından aracınızı seçerek uyumlu ürünleri görebilirsiniz.</p>
            </div>
            
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">İade nasıl yapabilirim?</h2>
                <p class="text-gray-700">Ürünlerinizi teslim aldığınız tarihten itibaren 14 gün içinde iade edebilirsiniz. İade koşulları için İade Koşulları sayfasını inceleyebilirsiniz.</p>
            </div>
            
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Hangi ödeme yöntemlerini kabul ediyorsunuz?</h2>
                <p class="text-gray-700">Kredi kartı, havale/EFT ve kapıda ödeme seçeneklerini kabul ediyoruz.</p>
            </div>
        </div>
    </div>
</div>
@endsection


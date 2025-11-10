@extends('layouts.app')

@section('title', 'Mesafeli Satış Sözleşmesi')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Mesafeli Satış Sözleşmesi</h1>
        
        <div class="prose max-w-none">
            <p class="text-gray-700 mb-4">
                Bu sözleşme, [Firma Adı] ile sipariş veren müşteri arasında düzenlenmiştir.
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Taraflar</h2>
            <p class="text-gray-700 mb-4">
                [Firma bilgileri ve müşteri bilgileri]
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Sipariş ve Teslimat</h2>
            <p class="text-gray-700 mb-4">
                Siparişleriniz en geç 3-5 iş günü içinde teslim edilir.
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">İptal ve İade</h2>
            <p class="text-gray-700 mb-4">
                Ürünlerinizi teslim aldığınız tarihten itibaren 14 gün içinde iade edebilirsiniz.
            </p>
        </div>
    </div>
</div>
@endsection


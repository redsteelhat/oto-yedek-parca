@extends('layouts.app')

@section('title', 'KVKK Aydınlatma Metni')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">KVKK Aydınlatma Metni</h1>
        
        <div class="prose max-w-none">
            <p class="text-gray-700 mb-4">
                Bu metin, 6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında hazırlanmıştır.
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Veri Sorumlusu</h2>
            <p class="text-gray-700 mb-4">
                [Firma Bilgileri]
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Toplanan Kişisel Veriler</h2>
            <p class="text-gray-700 mb-4">
                İsim, soyisim, e-posta adresi, telefon numarası, adres bilgileri, sipariş bilgileri.
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Verilerin İşlenme Amacı</h2>
            <p class="text-gray-700 mb-4">
                Sipariş işlemleri, müşteri hizmetleri, pazarlama faaliyetleri.
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Haklarınız</h2>
            <p class="text-gray-700 mb-4">
                KVKK kapsamında kişisel verileriniz hakkında bilgi talep etme, düzeltme, silme ve itiraz etme haklarınız bulunmaktadır.
            </p>
        </div>
    </div>
</div>
@endsection


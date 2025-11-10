@extends('layouts.app')

@section('title', 'Adreslerim')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h3 class="font-bold text-lg mb-4">Hesabım</h3>
                <nav class="space-y-2">
                    <a href="{{ route('account.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Ana Sayfa</a>
                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Profil Bilgilerim</a>
                    <a href="{{ route('account.orders') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Siparişlerim</a>
                    <a href="{{ route('account.addresses') }}" class="block px-4 py-2 rounded bg-primary-50 text-primary-600">Adreslerim</a>
                    <a href="{{ route('account.cars') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Araçlarım</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Adreslerim</h1>
                <button onclick="document.getElementById('addAddressForm').classList.toggle('hidden')" 
                        class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    Yeni Adres Ekle
                </button>
            </div>

            <!-- Add Address Form -->
            <div id="addAddressForm" class="hidden bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Yeni Adres Ekle</h2>
                <form action="{{ route('account.store-address') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adres Başlığı *</label>
                            <input type="text" name="title" value="Ev" required
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ad *</label>
                                <input type="text" name="first_name" required
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Soyad *</label>
                                <input type="text" name="last_name" required
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                            <input type="text" name="phone" required
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şehir *</label>
                            <input type="text" name="city" required
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İlçe *</label>
                            <input type="text" name="district" required
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Posta Kodu</label>
                            <input type="text" name="postal_code"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adres *</label>
                            <textarea name="address" rows="3" required
                                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_default" value="1" class="mr-2">
                                <span class="text-sm text-gray-700">Varsayılan adres olarak ayarla</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-4">
                        <button type="button" onclick="document.getElementById('addAddressForm').classList.add('hidden')" 
                                class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                            İptal
                        </button>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                            Kaydet
                        </button>
                    </div>
                </form>
            </div>

            <!-- Addresses List -->
            @if($addresses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($addresses as $address)
                        <div class="bg-white rounded-lg shadow-lg p-6 {{ $address->is_default ? 'border-2 border-primary-500' : '' }}">
                            @if($address->is_default)
                                <span class="inline-block bg-primary-100 text-primary-800 text-xs px-2 py-1 rounded mb-2">Varsayılan</span>
                            @endif
                            <h3 class="font-semibold text-lg mb-2">{{ $address->title }}</h3>
                            <p class="text-gray-700">{{ $address->full_name }}</p>
                            <p class="text-gray-700">{{ $address->phone }}</p>
                            <p class="text-gray-700">{{ $address->full_address }}</p>
                            @if($address->postal_code)
                                <p class="text-gray-700">Posta Kodu: {{ $address->postal_code }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <p class="text-gray-600 text-lg mb-4">Henüz adres eklenmemiş.</p>
                    <button onclick="document.getElementById('addAddressForm').classList.remove('hidden')" 
                            class="text-primary-600 hover:text-primary-800 font-medium">
                        İlk Adresinizi Ekleyin →
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


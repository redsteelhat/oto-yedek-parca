@extends('layouts.app')

@section('title', 'İletişim')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">İletişim</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Contact Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Bize Ulaşın</h2>
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                        <input type="text" name="name" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
                        <input type="email" name="email" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                        <input type="text" name="phone"
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               value="{{ old('phone') }}">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konu *</label>
                        <input type="text" name="subject" required
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               value="{{ old('subject') }}">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj *</label>
                        <textarea name="message" rows="5" required
                                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="privacy" required class="mr-2">
                            <span class="text-sm text-gray-700">
                                <a href="{{ route('privacy') }}" class="text-primary-600 hover:text-primary-800">KVKK</a> 
                                metnini okudum, kabul ediyorum. *
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition">
                        Gönder
                    </button>
                </div>
            </form>
        </div>

        <!-- Contact Info -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">İletişim Bilgileri</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Adres</h3>
                    <p class="text-gray-700">[Firma Adresi]</p>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Telefon</h3>
                    <p class="text-gray-700">0850 XXX XX XX</p>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">E-posta</h3>
                    <p class="text-gray-700">info@yedekparca.com</p>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Çalışma Saatleri</h3>
                    <p class="text-gray-700">Pazartesi - Cuma: 09:00 - 18:00</p>
                    <p class="text-gray-700">Cumartesi: 09:00 - 14:00</p>
                    <p class="text-gray-700">Pazar: Kapalı</p>
                </div>
            </div>
            
            <!-- Map -->
            <div class="mt-6">
                <div class="bg-gray-200 h-64 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500">Harita buraya eklenecek</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


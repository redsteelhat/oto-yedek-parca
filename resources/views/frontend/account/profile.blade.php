@extends('layouts.app')

@section('title', 'Profil Bilgilerim')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h3 class="font-bold text-lg mb-4">Hesabım</h3>
                <nav class="space-y-2">
                    <a href="{{ route('account.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Ana Sayfa</a>
                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 rounded bg-primary-50 text-primary-600">Profil Bilgilerim</a>
                    <a href="{{ route('account.orders') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Siparişlerim</a>
                    <a href="{{ route('account.addresses') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Adreslerim</a>
                    <a href="{{ route('account.cars') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Araçlarım</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Profil Bilgilerim</h1>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="{{ route('account.update-profile') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şirket Adı</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vergi Numarası</label>
                            <input type="text" name="tax_number" value="{{ old('tax_number', $user->tax_number) }}"
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('tax_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t pt-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Şifre Değiştir</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre</label>
                                <input type="password" name="password"
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                       placeholder="Boş bırakın, değiştirmek istemiyorsanız">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre Tekrar</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Şifreyi değiştirmek istemiyorsanız alanları boş bırakın.</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                            Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


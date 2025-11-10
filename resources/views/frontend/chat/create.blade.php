@extends('layouts.app')

@section('title', 'Yeni Mesaj Oluştur')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden p-6 sm:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Yeni Mesaj Oluştur</h1>
            <p class="text-gray-600">Sorularınız için bizimle iletişime geçebilirsiniz. En kısa sürede yanıtlanacaktır.</p>
        </div>

        <form method="POST" action="{{ route('chat.store') }}">
            @csrf

            @guest
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ad Soyad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2" maxlength="255">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            E-posta <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2" maxlength="255">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Telefon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border rounded px-3 py-2" maxlength="20">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endguest

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Konu <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full border rounded px-3 py-2" maxlength="255" placeholder="Mesajınızın konusu">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mesajınız <span class="text-red-500">*</span>
                </label>
                <textarea name="message" rows="6" required minlength="10" maxlength="2000" class="w-full border rounded px-3 py-2" placeholder="Mesajınızı buraya yazın...">{{ old('message') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Minimum 10, maksimum 2000 karakter</p>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 font-medium">
                    Mesaj Gönder
                </button>
                <a href="{{ route('chat.index') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 text-center font-medium">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection


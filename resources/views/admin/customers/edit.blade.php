@extends('layouts.admin')

@section('title', 'Müşteri Düzenle: ' . $customer->name)
@section('page-title', 'Müşteri Düzenle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kullanıcı Tipi *</label>
                <select name="user_type" required
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="customer" {{ old('user_type', $customer->user_type) == 'customer' ? 'selected' : '' }}>Müşteri</option>
                    <option value="dealer" {{ old('user_type', $customer->user_type) == 'dealer' ? 'selected' : '' }}>Bayi</option>
                    <option value="admin" {{ old('user_type', $customer->user_type) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('user_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Şirket Adı</label>
                <input type="text" name="company_name" value="{{ old('company_name', $customer->company_name) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('company_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vergi Numarası</label>
                <input type="text" name="tax_number" value="{{ old('tax_number', $customer->tax_number) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('tax_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="Değiştirmek için yeni şifre girin">
                <p class="text-xs text-gray-500 mt-1">Boş bırakılırsa şifre değişmez</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="flex items-center mt-6">
                    <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $customer->is_verified) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm text-gray-700">Doğrulanmış</span>
                </label>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                <textarea name="notes" rows="4" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('notes', $customer->notes) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Müşteri hakkında notlar (sadece admin görür)</p>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.customers.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                İptal
            </a>
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection


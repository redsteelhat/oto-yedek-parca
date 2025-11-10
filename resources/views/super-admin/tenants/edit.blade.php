@extends('super-admin.layout')

@section('title', 'Tenant Düzenle')
@section('page-title', 'Tenant Düzenle: ' . $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('super-admin.tenants.update', $tenant) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Temel Bilgiler</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tenant Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="w-full border rounded px-3 py-2">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Subdomain <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center">
                        <input type="text" name="subdomain" value="{{ old('subdomain', $tenant->subdomain) }}" required pattern="[a-z0-9-]+" class="w-full border rounded-l px-3 py-2">
                        <span class="bg-gray-100 border border-l-0 rounded-r px-3 py-2 text-gray-600">.site.com</span>
                    </div>
                    @error('subdomain')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Custom Domain (Opsiyonel)
                    </label>
                    <input type="text" name="domain" value="{{ old('domain', $tenant->domain) }}" placeholder="ornek.com" class="w-full border rounded px-3 py-2">
                    @error('domain')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        E-posta
                    </label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}" class="w-full border rounded px-3 py-2">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Telefon
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" class="w-full border rounded px-3 py-2">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branding -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-semibold mb-4">Marka Görünümü</h3>
                </div>

                @if($tenant->logo)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Logo</label>
                        <img src="{{ Storage::url($tenant->logo) }}" alt="Logo" class="h-20 w-auto">
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Yeni Logo
                    </label>
                    <input type="file" name="logo" accept="image/*" class="w-full border rounded px-3 py-2">
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($tenant->favicon)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Favicon</label>
                        <img src="{{ Storage::url($tenant->favicon) }}" alt="Favicon" class="h-16 w-auto">
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Yeni Favicon
                    </label>
                    <input type="file" name="favicon" accept="image/*" class="w-full border rounded px-3 py-2">
                    @error('favicon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ana Renk
                    </label>
                    <input type="color" name="primary_color" value="{{ old('primary_color', $tenant->primary_color) }}" class="w-full h-10 border rounded">
                    @error('primary_color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        İkincil Renk
                    </label>
                    <input type="color" name="secondary_color" value="{{ old('secondary_color', $tenant->secondary_color) }}" class="w-full h-10 border rounded">
                    @error('secondary_color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subscription -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-semibold mb-4">Abonelik Bilgileri</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Durum <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required class="w-full border rounded px-3 py-2">
                        <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="suspended" {{ old('status', $tenant->status) == 'suspended' ? 'selected' : '' }}>Askıya Alınmış</option>
                        <option value="inactive" {{ old('status', $tenant->status) == 'inactive' ? 'selected' : '' }}>Pasif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Abonelik Planı <span class="text-red-500">*</span>
                    </label>
                    <select name="subscription_plan" required class="w-full border rounded px-3 py-2">
                        <option value="free" {{ old('subscription_plan', $tenant->subscription_plan) == 'free' ? 'selected' : '' }}>Ücretsiz</option>
                        <option value="basic" {{ old('subscription_plan', $tenant->subscription_plan) == 'basic' ? 'selected' : '' }}>Temel</option>
                        <option value="premium" {{ old('subscription_plan', $tenant->subscription_plan) == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ old('subscription_plan', $tenant->subscription_plan) == 'enterprise' ? 'selected' : '' }}>Kurumsal</option>
                    </select>
                    @error('subscription_plan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Abonelik Bitiş Tarihi
                    </label>
                    <input type="datetime-local" name="subscription_expires_at" value="{{ old('subscription_expires_at', $tenant->subscription_expires_at ? $tenant->subscription_expires_at->format('Y-m-d\TH:i') : '') }}" class="w-full border rounded px-3 py-2">
                    @error('subscription_expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Maksimum Ürün Sayısı
                    </label>
                    <input type="number" name="max_products" value="{{ old('max_products', $tenant->max_products) }}" min="0" class="w-full border rounded px-3 py-2">
                    <p class="mt-1 text-xs text-gray-500">Boş bırakılırsa sınırsız</p>
                    @error('max_products')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Maksimum Kullanıcı Sayısı
                    </label>
                    <input type="number" name="max_users" value="{{ old('max_users', $tenant->max_users) }}" min="0" class="w-full border rounded px-3 py-2">
                    <p class="mt-1 text-xs text-gray-500">Boş bırakılırsa sınırsız</p>
                    @error('max_users')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Güncelle
                </button>
                <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection




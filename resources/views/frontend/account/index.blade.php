@extends('layouts.app')

@section('title', 'Hesabım')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 sticky top-20">
                <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Hesabım</h3>
                <nav class="space-y-2">
                    <a href="{{ route('account.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('account.index') ? 'bg-primary-50 text-primary-600' : '' }}">
                        Ana Sayfa
                    </a>
                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('account.profile') ? 'bg-primary-50 text-primary-600' : '' }}">
                        Profil Bilgilerim
                    </a>
                    <a href="{{ route('account.orders') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('account.orders*') ? 'bg-primary-50 text-primary-600' : '' }}">
                        Siparişlerim
                    </a>
                    <a href="{{ route('account.addresses') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('account.addresses') ? 'bg-primary-50 text-primary-600' : '' }}">
                        Adreslerim
                    </a>
                    <a href="{{ route('account.cars') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('account.cars') ? 'bg-primary-50 text-primary-600' : '' }}">
                        Araçlarım
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 sm:mb-6">Hoş Geldiniz, {{ $user->name }}</h1>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Son Siparişlerim</h2>
                @if($recentOrders->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <a href="{{ route('account.order-detail', $order) }}" class="font-semibold text-primary-600 hover:text-primary-800">
                                            {{ $order->order_number }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                                        <p class="text-sm text-gray-600">{{ number_format($order->total, 2) }} ₺</p>
                                    </div>
                                    <div>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('account.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                            Tüm Siparişleri Görüntüle →
                        </a>
                    </div>
                @else
                    <p class="text-gray-600">Henüz siparişiniz bulunmuyor.</p>
                    <a href="{{ route('products.index') }}" class="text-primary-600 hover:text-primary-800 font-medium mt-4 inline-block">
                        Alışverişe Başla →
                    </a>
                @endif
            </div>

            <!-- Account Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Hesap Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Ad Soyad</p>
                        <p class="font-semibold">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">E-posta</p>
                        <p class="font-semibold">{{ $user->email }}</p>
                    </div>
                    @if($user->phone)
                        <div>
                            <p class="text-sm text-gray-600">Telefon</p>
                            <p class="font-semibold">{{ $user->phone }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Üyelik Tarihi</p>
                        <p class="font-semibold">{{ $user->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('account.profile') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                        Profil Bilgilerini Düzenle →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'Siparişlerim')

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
                    <a href="{{ route('account.orders') }}" class="block px-4 py-2 rounded bg-primary-50 text-primary-600">Siparişlerim</a>
                    <a href="{{ route('account.addresses') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Adreslerim</a>
                    <a href="{{ route('account.cars') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Araçlarım</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Siparişlerim</h1>

            @if($orders->count() > 0)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('account.order-detail', $order) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($order->total, 2) }} ₺
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('account.order-detail', $order) }}" class="text-primary-600 hover:text-primary-800">
                                            Detay
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <p class="text-gray-600 text-lg mb-4">Henüz siparişiniz bulunmuyor.</p>
                    <a href="{{ route('products.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                        Alışverişe Başla →
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


@extends('layouts.admin')

@section('title', 'Kupon Detayı: ' . $coupon->code)
@section('page-title', 'Kupon Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Kupon Bilgileri</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Kupon Kodu</p>
                    <p class="font-semibold text-lg font-mono text-primary-600">{{ $coupon->code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kupon Adı</p>
                    <p class="font-semibold">{{ $coupon->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">İndirim Tipi</p>
                    <p class="font-semibold">{{ $coupon->type === 'percentage' ? 'Yüzde' : 'Sabit Tutar' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">İndirim Değeri</p>
                    <p class="font-semibold">
                        @if($coupon->type === 'percentage')
                            %{{ number_format($coupon->value, 0) }}
                        @else
                            {{ number_format($coupon->value, 2) }} ₺
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Durum</p>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $coupon->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $coupon->isActive() ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kullanım</p>
                    <p class="font-semibold">{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</p>
                </div>
            </div>
            
            @if($coupon->description)
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">Açıklama</p>
                    <p class="text-gray-900">{{ $coupon->description }}</p>
                </div>
            @endif
            
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    Düzenle
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Kupon Kullanım Geçmişi</h2>
            
            @if($coupon->orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($coupon->orders->take(10) as $order)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $order->user->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ number_format($order->total, 2) }} ₺</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Henüz bu kupon kullanılmamış.</p>
            @endif
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
            <h3 class="text-lg font-bold mb-4">İstatistikler</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Toplam Kullanım</p>
                    <p class="text-2xl font-bold text-primary-600">{{ $coupon->used_count }}</p>
                </div>
                
                @if($coupon->usage_limit)
                    <div>
                        <p class="text-sm text-gray-600">Kalan Kullanım</p>
                        <p class="text-2xl font-bold">{{ $coupon->usage_limit - $coupon->used_count }}</p>
                    </div>
                @endif
                
                <div>
                    <p class="text-sm text-gray-600">Kullanıcı Başına Limit</p>
                    <p class="text-xl font-semibold">{{ $coupon->usage_limit_per_user }}</p>
                </div>
                
                @if($coupon->start_date)
                    <div>
                        <p class="text-sm text-gray-600">Başlangıç Tarihi</p>
                        <p class="font-semibold">{{ $coupon->start_date->format('d.m.Y') }}</p>
                    </div>
                @endif
                
                @if($coupon->end_date)
                    <div>
                        <p class="text-sm text-gray-600">Bitiş Tarihi</p>
                        <p class="font-semibold">{{ $coupon->end_date->format('d.m.Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


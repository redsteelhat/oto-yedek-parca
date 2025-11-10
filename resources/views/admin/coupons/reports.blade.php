@extends('layouts.admin')

@section('title', 'Kupon Kullanım Raporları')
@section('page-title', 'Kupon Kullanım Raporları')

@section('content')
<div class="mb-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Filtreler</h2>
        
        <form method="GET" action="{{ route('admin.coupons.reports') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kupon</label>
                <select name="coupon_id" class="w-full border rounded px-3 py-2">
                    <option value="">Tüm Kuponlar</option>
                    @foreach($allCoupons as $coupon)
                        <option value="{{ $coupon->id }}" {{ request('coupon_id') == $coupon->id ? 'selected' : '' }}>
                            {{ $coupon->code }} - {{ $coupon->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded px-3 py-2">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700 w-full">
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Overall Statistics -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Genel İstatistikler</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Toplam Kupon Kullanımı</p>
                <p class="text-2xl font-bold text-blue-600">{{ $overallStats['total_coupons_used'] }}</p>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Toplam İndirim</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($overallStats['total_discount_given'], 2) }} ₺</p>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Toplam Gelir</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($overallStats['total_revenue'], 2) }} ₺</p>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Kullanılan Kupon Sayısı</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $overallStats['unique_coupons'] }}</p>
            </div>
            
            <div class="bg-red-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Benzersiz Kullanıcı</p>
                <p class="text-2xl font-bold text-red-600">{{ $overallStats['unique_users'] }}</p>
            </div>
        </div>
    </div>

    <!-- Coupon Statistics -->
    @if(count($statistics) > 0)
        <div class="space-y-6">
            @foreach($statistics as $stat)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold">{{ $stat['coupon']->code }} - {{ $stat['coupon']->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $stat['coupon']->description }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $stat['coupon']->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $stat['coupon']->isActive() ? 'Aktif' : 'Pasif' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Toplam Kullanım</p>
                            <p class="text-xl font-bold">{{ $stat['total_usage'] }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Toplam İndirim</p>
                            <p class="text-xl font-bold text-green-600">{{ number_format($stat['total_discount'], 2) }} ₺</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Toplam Gelir</p>
                            <p class="text-xl font-bold text-primary-600">{{ number_format($stat['total_revenue'], 2) }} ₺</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Ortalama Sipariş Değeri</p>
                            <p class="text-xl font-bold">{{ number_format($stat['avg_order_value'], 2) }} ₺</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Benzersiz Kullanıcı</p>
                            <p class="text-xl font-bold">{{ $stat['unique_users'] }}</p>
                        </div>
                    </div>

                    <!-- Top Users -->
                    @if(count($stat['top_users']) > 0)
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold mb-3">En Çok Kullananlar</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanım Sayısı</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam İndirim</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($stat['top_users'] as $topUser)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm font-medium">{{ $topUser['user']->name ?? 'Misafir' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $topUser['user']->email ?? '-' }}</div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $topUser['count'] }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold">{{ number_format($topUser['total_discount'], 2) }} ₺</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Usage by Date (if available) -->
                    @if(count($stat['usage_by_date']) > 0)
                        <div>
                            <h4 class="text-lg font-semibold mb-3">Günlük Kullanım</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanım Sayısı</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($stat['usage_by_date']->take(20) as $date => $count)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold">{{ $count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-6 text-center text-gray-500">
            <p>Seçilen kriterlere uygun kupon kullanım verisi bulunamadı.</p>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.coupons.index') }}" class="text-primary-600 hover:text-primary-800 underline">
            ← Kuponlara Dön
        </a>
    </div>
</div>
@endsection


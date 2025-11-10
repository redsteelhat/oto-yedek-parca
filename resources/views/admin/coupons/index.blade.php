@extends('layouts.admin')

@section('title', 'Kuponlar')
@section('page-title', 'Kuponlar')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <a href="{{ route('admin.coupons.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
            Yeni Kupon Ekle
        </a>
        <a href="{{ route('admin.coupons.reports') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Kupon Raporları
        </a>
    </div>
    <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Kupon kodu veya adı ara..." class="border rounded px-3 py-2">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Tüm Durumlar</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Filtrele
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kupon Kodu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İndirim</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanım</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Geçerlilik</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($coupons as $coupon)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-semibold text-primary-600">{{ $coupon->code }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $coupon->name }}</div>
                        @if($coupon->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($coupon->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($coupon->type === 'percentage')
                                %{{ number_format($coupon->value, 0) }}
                            @else
                                {{ number_format($coupon->value, 2) }} ₺
                            @endif
                        </div>
                        @if($coupon->min_purchase_amount)
                            <div class="text-xs text-gray-500">Min: {{ number_format($coupon->min_purchase_amount, 2) }} ₺</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $coupon->used_count }}
                            @if($coupon->usage_limit)
                                / {{ $coupon->usage_limit }}
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">Kullanıcı: {{ $coupon->usage_limit_per_user }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($coupon->start_date)
                                {{ $coupon->start_date->format('d.m.Y') }}
                            @else
                                -
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">
                            @if($coupon->end_date)
                                {{ $coupon->end_date->format('d.m.Y') }}
                            @else
                                Sınırsız
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($coupon->isActive())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Pasif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-primary-600 hover:text-primary-900 mr-3">Düzenle</a>
                        <a href="{{ route('admin.coupons.show', $coupon) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detay</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Bu kuponu silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Kupon bulunamadı.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $coupons->appends(request()->query())->links() }}
</div>
@endsection


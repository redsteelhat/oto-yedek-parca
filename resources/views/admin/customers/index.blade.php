@extends('layouts.admin')

@section('title', 'Müşteriler')
@section('page-title', 'Müşteriler')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ara..." 
                   class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <select name="user_type" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Tüm Tip</option>
                <option value="customer" {{ request('user_type') == 'customer' ? 'selected' : '' }}>Müşteri</option>
                <option value="dealer" {{ request('user_type') == 'dealer' ? 'selected' : '' }}>Bayi</option>
                <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Filtrele
            </button>
            @if(request('search') || request('user_type'))
                <a href="{{ route('admin.customers.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                    Temizle
                </a>
            @endif
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İletişim</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş Sayısı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kayıt Tarihi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($customers as $customer)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                        @if($customer->company_name)
                            <div class="text-sm text-gray-500">{{ $customer->company_name }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                        @if($customer->phone)
                            <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($customer->user_type == 'admin') bg-purple-100 text-purple-800
                            @elseif($customer->user_type == 'dealer') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($customer->user_type == 'admin') Admin
                            @elseif($customer->user_type == 'dealer') Bayi
                            @else Müşteri
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $customer->orders_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($customer->is_verified)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Doğrulanmış</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Beklemede</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $customer->created_at->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-primary-600 hover:text-primary-900 mr-3">Detay</a>
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="text-blue-600 hover:text-blue-900 mr-3">Düzenle</a>
                        @if($customer->orders_count == 0)
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Müşteri bulunamadı.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $customers->links() }}
</div>
@endsection


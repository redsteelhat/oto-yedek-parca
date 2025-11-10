@extends('layouts.admin')

@section('title', 'Tedarikçiler')
@section('page-title', 'Tedarikçiler')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.suppliers.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
        Yeni Tedarikçi Ekle
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tedarikçi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kod</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">XML URL</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Son Import</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Güncelleme Sıklığı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($suppliers as $supplier)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900 font-mono">{{ $supplier->code ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($supplier->xml_url)
                            <a href="{{ $supplier->xml_url }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-800 truncate block max-w-xs">
                                {{ \Illuminate\Support\Str::limit($supplier->xml_url, 40) }}
                            </a>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($supplier->latestImportLog)
                            <div class="text-sm text-gray-900">
                                {{ $supplier->latestImportLog->created_at->format('d.m.Y H:i') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $supplier->latestImportLog->status }}
                            </div>
                        @else
                            <span class="text-sm text-gray-500">Henüz import yapılmadı</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $supplier->update_frequency }} günde bir</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($supplier->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.suppliers.show', $supplier) }}" class="text-primary-600 hover:text-primary-900 mr-3">Detay</a>
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-900 mr-3">Düzenle</a>
                        <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Bu tedarikçiyi silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Tedarikçi bulunamadı. <a href="{{ route('admin.suppliers.create') }}" class="text-primary-600 hover:text-primary-800">İlk tedarikçiyi ekleyin</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $suppliers->links() }}
</div>
@endsection


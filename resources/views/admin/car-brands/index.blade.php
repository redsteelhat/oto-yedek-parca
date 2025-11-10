@extends('layouts.admin')

@section('title', 'Araç Markaları')
@section('page-title', 'Araç Markaları')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <a href="{{ route('admin.car-brands.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
            Yeni Marka Ekle
        </a>
        <a href="{{ route('admin.car-brands.export') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Dışa Aktar (CSV)
        </a>
        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            İçe Aktar (CSV)
        </button>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Araç Veritabanı İçe Aktarma</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form action="{{ route('admin.car-brands.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">CSV Dosyası Seç</label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Format: Marka, Model, Yıl, Motor Tipi, Motor Kodu</p>
                <p class="text-xs text-gray-500 mt-1">Maksimum dosya boyutu: 10MB</p>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                    İptal
                </button>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    İçe Aktar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marka</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model Sayısı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıralama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($brands as $brand)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $brand->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-500 font-mono">{{ $brand->slug }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $brand->models_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $brand->sort_order ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($brand->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.car-brands.show', $brand) }}" class="text-primary-600 hover:text-primary-900 mr-3">Detay</a>
                        <a href="{{ route('admin.car-brands.edit', $brand) }}" class="text-blue-600 hover:text-blue-900 mr-3">Düzenle</a>
                        <form action="{{ route('admin.car-brands.destroy', $brand) }}" method="POST" class="inline" onsubmit="return confirm('Bu markayı silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Marka bulunamadı. <a href="{{ route('admin.car-brands.create') }}" class="text-primary-600 hover:text-primary-800">İlk markayı ekleyin</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $brands->links() }}
</div>
@endsection


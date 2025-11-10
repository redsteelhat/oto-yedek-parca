@extends('layouts.admin')

@section('title', 'Ürünler')
@section('page-title', 'Ürünler')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <a href="{{ route('admin.products.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
            Yeni Ürün Ekle
        </a>
        <a href="{{ route('admin.products.export', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Dışa Aktar (CSV)
        </a>
        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            İçe Aktar (CSV)
        </button>
    </div>
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ara..." class="border rounded px-3 py-2">
        <select name="category_id" class="border rounded px-3 py-2">
            <option value="">Tüm Kategoriler</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Tüm Durumlar</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Taslak</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Filtrele
        </button>
    </form>
</div>

<!-- Bulk Actions -->
<form id="bulkActionForm" action="{{ route('admin.products.bulk-action') }}" method="POST" class="mb-4 hidden">
    @csrf
    <div class="bg-white rounded-lg shadow p-4 flex items-center justify-between">
        <div>
            <span id="selectedCount" class="font-semibold">0 ürün seçildi</span>
        </div>
        <div class="flex space-x-2">
            <select name="action" id="bulkActionSelect" class="border rounded px-3 py-2">
                <option value="">İşlem Seçin</option>
                <option value="activate">Aktif Et</option>
                <option value="deactivate">Pasif Et</option>
                <option value="delete">Sil</option>
            </select>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Uygula
            </button>
            <button type="button" onclick="clearSelection()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Temizle
            </button>
        </div>
    </div>
    <input type="hidden" name="product_ids" id="productIds">
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" class="product-checkbox" value="{{ $product->id }}" onchange="updateSelection()">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded object-cover mr-3">
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($product->short_description, 50) }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ number_format($product->price, 2) }} ₺
                    @if($product->sale_price)
                        <div class="text-xs text-red-600">{{ number_format($product->sale_price, 2) }} ₺</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="{{ $product->stock <= $product->min_stock_level ? 'text-red-600 font-bold' : '' }}">
                        {{ $product->stock }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $product->status === 'inactive' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $product->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ $product->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.products.edit', $product) }}" class="text-primary-600 hover:text-primary-900 mr-3">Düzenle</a>
                    <form action="{{ route('admin.products.duplicate', $product) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-900 mr-3">Kopyala</button>
                    </form>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">Ürün bulunamadı.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Ürün İçe Aktarma</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">CSV Dosyası Seç</label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full border rounded px-3 py-2">
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

@push('scripts')
<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    const count = checkboxes.length;
    const selectedCount = document.getElementById('selectedCount');
    const bulkForm = document.getElementById('bulkActionForm');
    const productIdsInput = document.getElementById('productIds');
    
    if (count > 0) {
        bulkForm.classList.remove('hidden');
        selectedCount.textContent = count + ' ürün seçildi';
        productIdsInput.value = Array.from(checkboxes).map(cb => cb.value).join(',');
    } else {
        bulkForm.classList.add('hidden');
    }
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelection();
}

// Bulk form validation
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const action = document.getElementById('bulkActionSelect').value;
    if (!action) {
        e.preventDefault();
        alert('Lütfen bir işlem seçin.');
        return false;
    }
    
    if (action === 'delete') {
        if (!confirm('Seçili ürünleri silmek istediğinize emin misiniz?')) {
            e.preventDefault();
            return false;
        }
    }
    
    // Convert comma-separated string to array
    const productIds = document.getElementById('productIds').value.split(',');
    document.getElementById('productIds').value = JSON.stringify(productIds);
});
</script>
@endpush
@endsection


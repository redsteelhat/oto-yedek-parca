@extends('layouts.admin')

@section('title', 'Yeni Ürün Ekle')
@section('page-title', 'Yeni Ürün Ekle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button type="button" onclick="showTab('general')" id="tab-general" class="tab-button border-b-2 border-primary-500 text-primary-600 py-4 px-1 text-sm font-medium">
                    Genel Bilgiler
                </button>
                <button type="button" onclick="showTab('price')" id="tab-price" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Fiyat & Stok
                </button>
                <button type="button" onclick="showTab('compatibility')" id="tab-compatibility" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Uyumlu Araçlar
                </button>
                <button type="button" onclick="showTab('images')" id="tab-images" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Görseller
                </button>
                <button type="button" onclick="showTab('seo')" id="tab-seo" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    SEO
                </button>
            </nav>
        </div>

        <!-- General Tab -->
        <div id="content-general" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ürün Adı *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">OEM Kodu</label>
                    <input type="text" name="oem_code" value="{{ old('oem_code') }}"
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Kategori Seçin</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tedarikçi</label>
                    <select name="supplier_id" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Tedarikçi Seçin</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durum *</label>
                    <select name="status" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Taslak</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Üretici</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer') }}"
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Parça Tipi</label>
                    <select name="part_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="aftermarket" {{ old('part_type') == 'aftermarket' ? 'selected' : '' }}>Yan Sanayi</option>
                        <option value="oem" {{ old('part_type') == 'oem' ? 'selected' : '' }}>Orijinal</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kısa Açıklama</label>
                    <textarea name="short_description" rows="2"
                              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('short_description') }}</textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="description" rows="5"
                              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm text-gray-700">Öne Çıkan Ürün</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Price & Stock Tab -->
        <div id="content-price" class="tab-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat *</label>
                    <input type="number" name="price" step="0.01" value="{{ old('price') }}" required
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">İndirimli Fiyat</label>
                    <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price') }}"
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" required
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kritik Stok Seviyesi</label>
                    <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 0) }}"
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">KDV Oranı (%)</label>
                    <input type="number" name="tax_rate" step="0.01" value="{{ old('tax_rate', 20.00) }}"
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Compatibility Tab -->
        <div id="content-compatibility" class="tab-content hidden">
            <div class="mb-4">
                <p class="text-gray-600 mb-4">Bu ürünün uyumlu olduğu araçları seçin:</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Marka</label>
                        <select id="brandSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Marka Seçin</option>
                            @foreach($carYears->pluck('model.brand')->unique('id') as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                        <select id="modelSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" disabled>
                            <option value="">Önce Marka Seçin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Yıl</label>
                        <select id="yearSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" disabled>
                            <option value="">Önce Model Seçin</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="addCar()" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700 transition mb-4">
                    Araç Ekle
                </button>
            </div>
            
            <div id="selectedCars" class="space-y-2">
                <!-- Selected cars will be added here -->
            </div>
            
            <input type="hidden" name="compatible_cars" id="compatibleCarsInput" value="">
        </div>

        <!-- Images Tab -->
        <div id="content-images" class="tab-content hidden">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ürün Görselleri</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <p class="text-sm text-gray-500 mt-2">Birden fazla görsel seçebilirsiniz. İlk görsel ana görsel olacaktır.</p>
                @error('images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- SEO Tab -->
        <div id="content-seo" class="tab-content hidden">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SEO Başlık</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SEO Açıklama</label>
                    <textarea name="meta_description" rows="3"
                              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                İptal
            </a>
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                Kaydet
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let selectedCars = [];

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-primary-500', 'text-primary-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tabName).classList.add('border-primary-500', 'text-primary-600');
}

function addCar() {
    const brandSelect = document.getElementById('brandSelect');
    const modelSelect = document.getElementById('modelSelect');
    const yearSelect = document.getElementById('yearSelect');
    
    const brandId = brandSelect.value;
    const modelId = modelSelect.value;
    const yearId = yearSelect.value;
    
    if (!yearId) {
        alert('Lütfen marka, model ve yıl seçin.');
        return;
    }
    
    const yearText = yearSelect.options[yearSelect.selectedIndex].text;
    
    if (!selectedCars.includes(yearId)) {
        selectedCars.push(yearId);
        updateSelectedCarsDisplay();
    }
    
    // Reset selects
    brandSelect.value = '';
    modelSelect.innerHTML = '<option value="">Önce Marka Seçin</option>';
    modelSelect.disabled = true;
    yearSelect.innerHTML = '<option value="">Önce Model Seçin</option>';
    yearSelect.disabled = true;
}

function removeCar(yearId) {
    selectedCars = selectedCars.filter(id => id != yearId);
    updateSelectedCarsDisplay();
}

function updateSelectedCarsDisplay() {
    const container = document.getElementById('selectedCars');
    const input = document.getElementById('compatibleCarsInput');
    
    input.value = JSON.stringify(selectedCars);
    
    container.innerHTML = selectedCars.map(yearId => {
        // This is a simplified version - in real app, you'd fetch car details
        return `
            <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                <span>Araç ID: ${yearId}</span>
                <button type="button" onclick="removeCar(${yearId})" class="text-red-600 hover:text-red-800">Kaldır</button>
            </div>
        `;
    }).join('');
}

// Brand/Model/Year dependent dropdowns
document.getElementById('brandSelect')?.addEventListener('change', function() {
    const brandId = this.value;
    const modelSelect = document.getElementById('modelSelect');
    
    modelSelect.innerHTML = '<option value="">Yükleniyor...</option>';
    modelSelect.disabled = true;
    
    if (brandId) {
        fetch(`/api/car-models/${brandId}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="">Model Seçin</option>';
                data.forEach(model => {
                    modelSelect.innerHTML += `<option value="${model.id}">${model.name}</option>`;
                });
                modelSelect.disabled = false;
            });
    }
});

document.getElementById('modelSelect')?.addEventListener('change', function() {
    const modelId = this.value;
    const yearSelect = document.getElementById('yearSelect');
    
    yearSelect.innerHTML = '<option value="">Yükleniyor...</option>';
    yearSelect.disabled = true;
    
    if (modelId) {
        fetch(`/api/car-years/${modelId}`)
            .then(response => response.json())
            .then(data => {
                yearSelect.innerHTML = '<option value="">Yıl Seçin</option>';
                data.forEach(year => {
                    const label = year.year + (year.motor_type ? ' - ' + year.motor_type : '');
                    yearSelect.innerHTML += `<option value="${year.id}">${label}</option>`;
                });
                yearSelect.disabled = false;
            });
    }
});
</script>
@endpush
@endsection


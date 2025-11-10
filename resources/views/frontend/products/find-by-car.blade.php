@extends('layouts.app')

@section('title', 'Araçla Parça Bul')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 sm:mb-4 px-4 sm:px-0">Araçla Parça Bul</h1>
        
        <!-- Car Selection Form -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
            <form action="{{ route('products.find-by-car') }}" method="GET" id="carSearchForm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Marka</label>
                        <select name="brand_id" id="brandSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Marka Seçin</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                        <select name="model_id" id="modelSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" {{ !request('brand_id') ? 'disabled' : '' }}>
                            <option value="">Model Seçin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Yıl</label>
                        <select name="year_id" id="yearSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" {{ !request('model_id') ? 'disabled' : '' }}>
                            <option value="">Yıl Seçin</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1 flex items-end">
                        <button type="submit" class="w-full bg-primary-600 text-white px-4 sm:px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition text-sm sm:text-base">
                            Parça Bul
                        </button>
                    </div>
                </div>
            </form>
            
            @if($selectedCar)
                <div class="mt-4 p-3 sm:p-4 bg-primary-50 rounded-lg">
                    <p class="text-xs sm:text-sm text-gray-700">
                        <strong>Seçili Araç:</strong>
                        @if($selectedCar instanceof \App\Models\CarYear)
                            {{ $selectedCar->model->brand->name }} {{ $selectedCar->model->name }} 
                            ({{ $selectedCar->year }}{{ $selectedCar->motor_type ? ' - ' . $selectedCar->motor_type : '' }})
                        @elseif($selectedCar instanceof \App\Models\CarModel)
                            {{ $selectedCar->brand->name }} {{ $selectedCar->name }}
                        @else
                            {{ $selectedCar->name }}
                        @endif
                        <a href="{{ route('products.find-by-car') }}" class="text-primary-600 hover:text-primary-800 ml-2">[Değiştir]</a>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Filters and Products -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 sticky top-20">
                <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Filtreler</h3>
                
                <form method="GET" action="{{ route('products.find-by-car') }}">
                    @if(request('brand_id'))
                        <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                    @endif
                    @if(request('model_id'))
                        <input type="hidden" name="model_id" value="{{ request('model_id') }}">
                    @endif
                    @if(request('year_id'))
                        <input type="hidden" name="year_id" value="{{ request('year_id') }}">
                    @endif
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category_id" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat Aralığı</label>
                        <div class="flex space-x-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                   class="w-full border rounded px-2 py-1 text-sm">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                   class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700 transition text-sm">
                        Filtrele
                    </button>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="lg:col-span-3">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-4">
                <p class="text-sm sm:text-base text-gray-600">{{ $products->total() }} ürün bulundu</p>
                <select name="sort" form="sortForm" class="w-full sm:w-auto border rounded px-3 py-2 text-sm">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>En Yeni</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Fiyat (Düşük-Yüksek)</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Fiyat (Yüksek-Düşük)</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>İsim</option>
                </select>
                <form id="sortForm" method="GET" action="{{ route('products.find-by-car') }}" class="hidden">
                    @foreach(request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <a href="{{ route('products.show', $product->slug) }}">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">Resim Yok</span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <div class="mb-2">
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Bu araca uygun</span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-2 text-gray-900">{{ $product->name }}</h3>
                                    <p class="text-gray-600 text-sm mb-2">SKU: {{ $product->sku }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-primary-600">{{ number_format($product->final_price, 2) }} ₺</span>
                                        @if($product->is_in_stock)
                                            <span class="text-green-600 text-sm font-medium">Stokta</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-600 text-lg">Seçili araca uygun ürün bulunamadı.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandSelect = document.getElementById('brandSelect');
    const modelSelect = document.getElementById('modelSelect');
    const yearSelect = document.getElementById('yearSelect');

    @if(request('brand_id'))
        loadModels({{ request('brand_id') }}, {{ request('model_id') ?? 'null' }});
    @endif

    @if(request('model_id'))
        loadYears({{ request('model_id') }}, {{ request('year_id') ?? 'null' }});
    @endif

    brandSelect.addEventListener('change', function() {
        const brandId = this.value;
        modelSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        modelSelect.disabled = true;
        yearSelect.innerHTML = '<option value="">Önce Model Seçin</option>';
        yearSelect.disabled = true;

        if (brandId) {
            loadModels(brandId);
        } else {
            modelSelect.innerHTML = '<option value="">Önce Marka Seçin</option>';
        }
    });

    modelSelect.addEventListener('change', function() {
        const modelId = this.value;
        yearSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        yearSelect.disabled = true;

        if (modelId) {
            loadYears(modelId);
        } else {
            yearSelect.innerHTML = '<option value="">Önce Model Seçin</option>';
        }
    });

    function loadModels(brandId, selectedModelId = null) {
        fetch(`/api/car-models/${brandId}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="">Model Seçin</option>';
                data.forEach(model => {
                    const selected = selectedModelId && model.id == selectedModelId ? 'selected' : '';
                    modelSelect.innerHTML += `<option value="${model.id}" ${selected}>${model.name}</option>`;
                });
                modelSelect.disabled = false;
            });
    }

    function loadYears(modelId, selectedYearId = null) {
        fetch(`/api/car-years/${modelId}`)
            .then(response => response.json())
            .then(data => {
                yearSelect.innerHTML = '<option value="">Yıl Seçin</option>';
                data.forEach(year => {
                    const label = year.year + (year.motor_type ? ' - ' + year.motor_type : '');
                    const selected = selectedYearId && year.id == selectedYearId ? 'selected' : '';
                    yearSelect.innerHTML += `<option value="${year.id}" ${selected}>${label}</option>`;
                });
                yearSelect.disabled = false;
            });
    }
});
</script>
@endpush
@endsection


@extends('layouts.app')

@section('title', 'Ana Sayfa')

@section('content')
@if($isAggregator ?? false)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-10">
        <!-- Aggregator Hero -->
        <div class="rounded-lg shadow-lg p-6 sm:p-10 text-white" style="background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold mb-4">Türkiye'nin Otomotiv Yedek Parça Pazaryeri</h1>
                    <p class="text-base sm:text-lg text-white/90">
                        Aktif tüm mağazaların ürünlerini tek ekranda inceleyin, mağazalara hemen geçiş yaparak
                        ihtiyaç duyduğunuz parçaları satın alın.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-4 text-sm sm:text-base">
                        <div class="bg-white/10 backdrop-blur rounded-lg px-4 py-3">
                            <span class="block text-white/70 uppercase tracking-wide text-xs">Aktif Mağaza</span>
                            <span class="text-2xl font-bold">{{ $tenantHighlights->count() }}</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-lg px-4 py-3">
                            <span class="block text-white/70 uppercase tracking-wide text-xs">Listelenen Ürün</span>
                            <span class="text-2xl font-bold">{{ $totalProducts }}</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-lg px-4 py-3">
                            <span class="block text-white/70 uppercase tracking-wide text-xs">Kategori</span>
                            <span class="text-2xl font-bold">{{ $totalCategories }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white text-gray-900 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-3">Bir mağazaya hızlıca geçiş yapın</h2>
                    <p class="text-sm text-gray-600 mb-4">Mağaza ana sayfasına yönlendirilmek istediğiniz markayı seçin.</p>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse($tenantHighlights as $tenant)
                            <a href="{{ $tenant->visit_url }}" class="flex items-center justify-between border border-gray-200 rounded-lg px-4 py-3 hover:border-gray-300 transition">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $tenant->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $tenant->active_products_count }} ürün</p>
                                </div>
                                <span class="text-sm font-medium brand-text">Mağazaya Git →</span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">Aktif mağaza bulunamadı.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @if($popularCategories->count() > 0)
        <section>
            <h2 class="text-2xl font-bold mb-4 text-gray-900">Popüler Kategoriler</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($popularCategories as $category)
                    <div class="bg-white rounded-lg shadow p-4 border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500 mb-3">{{ $category->products_count }} ürün</p>
                        <a href="{{ url('/products?category=' . $category->slug) }}" class="text-sm brand-text hover:underline">
                            Ürünleri Gör
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        @if($tenantHighlights->count() > 0)
        <section class="space-y-8">
            <h2 class="text-2xl font-bold text-gray-900">Mağazalardan Öne Çıkanlar</h2>
            <div class="space-y-6">
                @foreach($tenantHighlights as $tenant)
                    <div class="bg-white rounded-lg shadow border border-gray-100">
                        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $tenant->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $tenant->active_products_count }} aktif ürün</p>
                            </div>
                            <a href="{{ $tenant->visit_url }}" class="brand-bg px-4 py-2 rounded text-sm font-medium">
                                Mağazaya Git
                            </a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 px-5 py-5">
                            @forelse($tenant->products as $product)
                                <div class="bg-white border border-gray-100 rounded-lg shadow-sm hover:shadow transition overflow-hidden">
                                    <a href="{{ $product->tenant_url }}">
                                        @if($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover" loading="lazy">
                                        @else
                                            <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-500 text-sm">Resim Yok</div>
                                        @endif
                                        <div class="p-3">
                                            <p class="text-xs text-gray-400 mb-1">{{ $tenant->name }}</p>
                                            <h4 class="font-semibold text-sm text-gray-900 line-clamp-2 mb-2">{{ $product->name }}</h4>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="font-bold brand-text">{{ number_format($product->final_price, 2) }} ₺</span>
                                                @if($product->is_on_sale)
                                                    <span class="text-gray-400 line-through">{{ number_format($product->price, 2) }} ₺</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="col-span-full text-sm text-gray-500">
                                    Bu mağazada öne çıkan ürün bulunamadı.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        @if($recentProducts->count() > 0)
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Tüm Mağazalardan Son Eklenen Ürünler</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($recentProducts as $product)
                    <div class="bg-white border border-gray-100 rounded-lg shadow-sm hover:shadow transition overflow-hidden">
                        <a href="{{ $product->tenant_url }}">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-44 object-cover" loading="lazy">
                            @else
                                <div class="w-full h-44 bg-gray-200 flex items-center justify-center text-gray-500 text-sm">Resim Yok</div>
                            @endif
                            <div class="p-3 space-y-2">
                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">{{ $product->tenant_name }}</span>
                                <h3 class="font-semibold text-sm text-gray-900 line-clamp-2">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-bold brand-text">{{ number_format($product->final_price, 2) }} ₺</span>
                                    <span class="text-gray-400 text-xs">SKU: {{ $product->sku }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
@else
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- Hero Section - Araç Seçimi -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-lg shadow-lg p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8 text-white">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-center">
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4">Aracınıza Uygun Parçayı Bulun</h1>
                <p class="text-base sm:text-lg lg:text-xl mb-4 sm:mb-6">Marka, model ve yıl seçerek aracınıza uygun parçaları kolayca bulun.</p>
            </div>
            <div class="bg-white rounded-lg p-4 sm:p-6 text-gray-900">
                <form action="{{ route('products.find-by-car') }}" method="GET" id="carSearchForm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Marka</label>
                            <select name="brand_id" id="brandSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Marka Seçin</option>
                                @foreach(\App\Models\CarBrand::where('is_active', true)->get() as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Model</label>
                            <select name="model_id" id="modelSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" disabled>
                                <option value="">Önce Marka Seçin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Yıl</label>
                            <select name="year_id" id="yearSelect" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" disabled>
                                <option value="">Önce Model Seçin</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2 flex items-end">
                            <button type="submit" class="w-full bg-primary-600 text-white px-4 sm:px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition text-sm sm:text-base">
                                Parça Bul
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Öne Çıkan Kategoriler -->
    @if($categories->count() > 0)
    <section class="mb-8 sm:mb-12">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6 text-gray-900 px-4 sm:px-0">Öne Çıkan Kategoriler</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 px-4 sm:px-0">
            @foreach($categories as $category)
            <a href="{{ route('products.category', $category->slug) }}" class="bg-white rounded-lg shadow-md p-3 sm:p-4 lg:p-6 text-center hover:shadow-lg transition">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-16 sm:h-20 lg:h-24 object-cover rounded mb-2" loading="lazy">
                @else
                    <div class="w-full h-16 sm:h-20 lg:h-24 bg-gray-200 rounded mb-2 flex items-center justify-center">
                        <span class="text-gray-400 text-xs sm:text-sm">{{ $category->name }}</span>
                    </div>
                @endif
                <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900">{{ $category->name }}</h3>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Kampanyalı Ürünler -->
    @if($campaigns->count() > 0)
    <section class="mb-8 sm:mb-12">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6 text-gray-900 px-4 sm:px-0">Kampanyalı Ürünler</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 px-4 sm:px-0">
            @foreach($campaigns as $campaign)
                @if($campaign->isActive())
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition border-2 border-red-500">
                        <div class="bg-red-600 text-white text-center py-2 font-bold">
                            %{{ number_format($campaign->discount_value, 0) }} İndirim
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2">{{ $campaign->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($campaign->description, 100) }}</p>
                            <a href="{{ route('campaigns.show', $campaign->slug) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700 transition inline-block">
                                Kampanyaya Git
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
    @endif

    <!-- Öne Çıkan Ürünler -->
    @if($featuredProducts->count() > 0)
    <section class="mb-8 sm:mb-12">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6 text-gray-900 px-4 sm:px-0">Öne Çıkan Ürünler</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 px-4 sm:px-0">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('products.show', $product->slug) }}">
                    @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-32 sm:h-40 lg:h-48 object-cover" loading="lazy">
                    @else
                        <div class="w-full h-32 sm:h-40 lg:h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-xs sm:text-sm">Resim Yok</span>
                        </div>
                    @endif
                    <div class="p-2 sm:p-3 lg:p-4">
                        <h3 class="font-semibold text-xs sm:text-sm lg:text-base xl:text-lg mb-1 sm:mb-2 text-gray-900 line-clamp-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-xs sm:text-sm mb-1 sm:mb-2 hidden sm:block">SKU: {{ $product->sku }}</p>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 sm:gap-0">
                            <span class="text-base sm:text-lg lg:text-xl xl:text-2xl font-bold text-primary-600">{{ number_format($product->final_price, 2) }} ₺</span>
                            @if($product->is_on_sale)
                                <span class="text-xs sm:text-sm text-gray-500 line-through">{{ number_format($product->price, 2) }} ₺</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Çok Satanlar -->
    @if($bestsellers->count() > 0)
    <section class="mb-8 sm:mb-12">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6 text-gray-900 px-4 sm:px-0">Çok Satanlar</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 px-4 sm:px-0">
            @foreach($bestsellers as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('products.show', $product->slug) }}">
                    @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">Resim Yok</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 text-gray-900">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-2">SKU: {{ $product->sku }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary-600">{{ number_format($product->final_price, 2) }} ₺</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Yeni Ürünler -->
    @if($newProducts->count() > 0)
    <section class="mb-8 sm:mb-12">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6 text-gray-900 px-4 sm:px-0">Yeni Gelenler</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 px-4 sm:px-0">
            @foreach($newProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('products.show', $product->slug) }}">
                    @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">Resim Yok</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 text-gray-900">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-2">SKU: {{ $product->sku }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary-600">{{ number_format($product->final_price, 2) }} ₺</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandSelect = document.getElementById('brandSelect');
    const modelSelect = document.getElementById('modelSelect');
    const yearSelect = document.getElementById('yearSelect');

    brandSelect.addEventListener('change', function() {
        const brandId = this.value;
        modelSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        modelSelect.disabled = true;
        yearSelect.innerHTML = '<option value="">Önce Model Seçin</option>';
        yearSelect.disabled = true;

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
        } else {
            modelSelect.innerHTML = '<option value="">Önce Marka Seçin</option>';
        }
    });

    modelSelect.addEventListener('change', function() {
        const modelId = this.value;
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
        } else {
            yearSelect.innerHTML = '<option value="">Önce Model Seçin</option>';
        }
    });
});
</script>
@endpush
@endsection

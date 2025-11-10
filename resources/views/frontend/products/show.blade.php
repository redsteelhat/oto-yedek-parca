@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 p-4 sm:p-8">
            <!-- Product Images -->
            <div>
                @if($product->images->count() > 0)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-64 sm:h-80 lg:h-96 object-cover rounded-lg" id="mainImage">
                    </div>
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($product->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-16 sm:h-20 lg:h-24 object-cover rounded cursor-pointer hover:opacity-75 transition"
                                     onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $image->image_path) }}'">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full h-64 sm:h-80 lg:h-96 bg-gray-200 flex items-center justify-center rounded-lg">
                        <span class="text-gray-400">Resim Yok</span>
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                
                <div class="mb-4 space-y-2">
                    <p class="text-sm sm:text-base text-gray-600"><strong>SKU:</strong> {{ $product->sku }}</p>
                    @if($product->oem_code)
                        <p class="text-sm sm:text-base text-gray-600"><strong>OEM Kodu:</strong> {{ $product->oem_code }}</p>
                    @endif
                    @if($product->manufacturer)
                        <p class="text-sm sm:text-base text-gray-600"><strong>Üretici:</strong> {{ $product->manufacturer }}</p>
                    @endif
                    <p class="text-sm sm:text-base text-gray-600"><strong>Parça Tipi:</strong> {{ $product->part_type == 'oem' ? 'Orijinal' : 'Yan Sanayi' }}</p>
                </div>

                <div class="mb-6">
                    @if($product->is_on_sale)
                        <div class="flex flex-wrap items-baseline gap-2 sm:gap-3">
                            <span class="text-3xl sm:text-4xl font-bold text-primary-600">{{ number_format($product->sale_price, 2) }} ₺</span>
                            <span class="text-lg sm:text-xl text-gray-500 line-through">{{ number_format($product->price, 2) }} ₺</span>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs sm:text-sm font-semibold">
                                %{{ number_format((($product->price - $product->sale_price) / $product->price) * 100, 0) }} İndirim
                            </span>
                        </div>
                    @else
                        <span class="text-3xl sm:text-4xl font-bold text-primary-600">{{ number_format($product->price, 2) }} ₺</span>
                    @endif
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">KDV Dahil</p>
                </div>

                <!-- Rating and Reviews Summary -->
                @if($product->total_reviews > 0)
                    <div class="mb-4 flex items-center space-x-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->average_rating))
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @elseif($i == ceil($product->average_rating) && $product->average_rating - floor($product->average_rating) > 0)
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <defs>
                                            <linearGradient id="half-{{ $i }}">
                                                <stop offset="50%" stop-color="currentColor"/>
                                                <stop offset="50%" stop-color="#E5E7EB"/>
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#half-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm sm:text-base font-medium text-gray-700">{{ number_format($product->average_rating, 1) }}</span>
                        <span class="text-sm sm:text-base text-gray-500">({{ $product->total_reviews }} {{ $product->total_reviews == 1 ? 'yorum' : 'yorum' }})</span>
                    </div>
                @endif

                <div class="mb-6">
                    @if($product->is_in_stock)
                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs sm:text-sm font-medium">
                            ✓ Stokta Var ({{ $product->stock }} adet)
                        </span>
                    @else
                        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs sm:text-sm font-medium">
                            ✗ Stokta Yok
                        </span>
                    @endif
                </div>

                @if($product->is_in_stock)
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 mb-4">
                            <label for="quantity" class="text-sm sm:text-base text-gray-700 font-medium whitespace-nowrap">Adet:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                   class="border rounded px-3 py-2 w-20 sm:w-24 text-center text-sm sm:text-base">
                        </div>
                        <button type="submit" class="w-full bg-primary-600 text-white px-4 sm:px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition text-sm sm:text-base">
                            Sepete Ekle
                        </button>
                    </form>
                @endif

                @if($product->description)
                    <div class="border-t pt-6">
                        <h2 class="text-lg sm:text-xl font-bold mb-4">Açıklama</h2>
                        <div class="text-sm sm:text-base text-gray-600 prose max-w-none">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif

                @if($product->compatibleCars->count() > 0)
                    <div class="border-t pt-6 mt-6">
                        <h2 class="text-lg sm:text-xl font-bold mb-4">Uyumlu Araçlar</h2>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($product->compatibleCars as $carYear)
                                <p class="text-sm sm:text-base text-gray-600">
                                    {{ $carYear->model->brand->name }} {{ $carYear->model->name }} 
                                    ({{ $carYear->year }})
                                    @if($carYear->motor_type)
                                        - {{ $carYear->motor_type }}
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-8 sm:mt-12 bg-white rounded-lg shadow-lg overflow-hidden p-4 sm:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl sm:text-2xl font-bold">Yorumlar ({{ $product->total_reviews }})</h2>
            <a href="{{ route('products.reviews.create', $product->slug) }}" class="bg-primary-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-700 text-sm sm:text-base">
                Yorum Yap
            </a>
        </div>

        @if($reviews->count() > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border-b pb-6 last:border-b-0 last:pb-0">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="font-semibold text-sm sm:text-base">{{ $review->name ?? $review->user->name ?? 'Misafir' }}</span>
                                    @if($review->is_verified_purchase)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Doğrulanmış Satın Alma
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs sm:text-sm text-gray-500">{{ $review->created_at->format('d.m.Y') }}</span>
                        </div>
                        @if($review->title)
                            <h3 class="font-semibold text-sm sm:text-base mb-2">{{ $review->title }}</h3>
                        @endif
                        <p class="text-sm sm:text-base text-gray-700 whitespace-pre-wrap">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">Henüz yorum yapılmamış.</p>
                <a href="{{ route('products.reviews.create', $product->slug) }}" class="bg-primary-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-700 text-sm sm:text-base inline-block">
                    İlk Yorumu Siz Yapın
                </a>
            </div>
        @endif
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-8 sm:mt-12">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 px-4 sm:px-0">Benzer Ürünler</h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 px-4 sm:px-0">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}">
                            @if($relatedProduct->primaryImage)
                                <img src="{{ asset('storage/' . $relatedProduct->primaryImage->image_path) }}" 
                                     alt="{{ $relatedProduct->name }}" 
                                     class="w-full h-32 sm:h-40 lg:h-48 object-cover">
                            @else
                                <div class="w-full h-32 sm:h-40 lg:h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400 text-xs sm:text-sm">Resim Yok</span>
                                </div>
                            @endif
                            <div class="p-3 sm:p-4">
                                <h3 class="font-semibold text-sm sm:text-base lg:text-lg mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                                <p class="text-lg sm:text-xl lg:text-2xl font-bold text-primary-600">{{ number_format($relatedProduct->final_price, 2) }} ₺</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

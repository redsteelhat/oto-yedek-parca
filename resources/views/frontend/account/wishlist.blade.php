@extends('layouts.app')

@section('title', 'Favorilerim')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Favorilerim</h1>
    </div>

    @if($wishlistItems->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($wishlistItems as $product)
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
                    </a>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 text-gray-900">
                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-2">SKU: {{ $product->sku }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <div>
                                @if($product->is_on_sale)
                                    <span class="text-2xl font-bold text-primary-600">{{ number_format($product->sale_price, 2) }} ₺</span>
                                    <span class="text-sm text-gray-500 line-through ml-2">{{ number_format($product->price, 2) }} ₺</span>
                                @else
                                    <span class="text-2xl font-bold text-primary-600">{{ number_format($product->price, 2) }} ₺</span>
                                @endif
                            </div>
                            @if($product->is_in_stock)
                                <span class="text-green-600 text-sm font-medium">Stokta</span>
                            @else
                                <span class="text-red-600 text-sm font-medium">Stokta Yok</span>
                            @endif
                        </div>
                        <div class="mt-4 flex gap-2">
                            @if($product->is_in_stock)
                                <form action="{{ route('cart.add') }}" method="POST" class="flex-1 quick-add-to-cart">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700 transition">
                                        Sepete Ekle
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="remove-from-wishlist bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition" data-product-id="{{ $product->id }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $wishlistItems->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-600 text-lg mb-4">Favoriler listeniz boş.</p>
            <a href="{{ route('products.index') }}" class="text-primary-600 hover:text-primary-800 inline-block">
                Ürünlere Göz At
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Remove from wishlist
    document.querySelectorAll('.remove-from-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const card = this.closest('.bg-white');
            
            fetch(`/favoriler/kaldir/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    card.remove();
                    // Show success message
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu.');
            });
        });
    });

    // Quick add to cart
    document.querySelectorAll('.quick-add-to-cart').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Update cart count if needed
                    if (data.cart_count) {
                        const cartCountEl = document.querySelector('.cart-count');
                        if (cartCountEl) {
                            cartCountEl.textContent = data.cart_count;
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Sepete eklenirken bir hata oluştu.');
            });
        });
    });
</script>
@endpush
@endsection


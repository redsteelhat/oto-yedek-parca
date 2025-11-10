@extends('layouts.app')

@section('title', 'Yorum Yap - ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden p-4 sm:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">Yorum Yap</h1>
            <div class="flex items-center space-x-4">
                <a href="{{ route('products.show', $product->slug) }}" class="text-primary-600 hover:text-primary-800 text-sm sm:text-base">
                    ← Ürüne Dön
                </a>
                <span class="text-gray-400">|</span>
                <span class="text-sm sm:text-base text-gray-600">{{ $product->name }}</span>
            </div>
        </div>

        @if($hasPurchased)
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm sm:text-base text-green-800 font-medium">Bu ürünü satın aldınız. Yorumunuz "Doğrulanmış Satın Alma" olarak işaretlenecektir.</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('products.reviews.store', $product->slug) }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">
                    Puan <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2" id="ratingContainer">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" class="hidden" {{ $i == 5 ? 'checked' : '' }}>
                        <label for="rating-{{ $i }}" class="cursor-pointer rating-star" data-rating="{{ $i }}">
                            <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 transition" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @guest
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">
                            Ad Soyad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2 text-sm sm:text-base" maxlength="255">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">
                            E-posta <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2 text-sm sm:text-base" maxlength="255">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endguest

            <div class="mb-6">
                <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">
                    Başlık (Opsiyonel)
                </label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2 text-sm sm:text-base" maxlength="255" placeholder="Yorumunuz için bir başlık yazın...">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">
                    Yorumunuz <span class="text-red-500">*</span>
                </label>
                <textarea name="comment" rows="6" required minlength="10" maxlength="2000" class="w-full border rounded px-3 py-2 text-sm sm:text-base" placeholder="Ürün hakkındaki deneyimlerinizi paylaşın...">{{ old('comment') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Minimum 10, maksimum 2000 karakter ({{ old('comment') ? strlen(old('comment')) : 0 }}/2000)</p>
                @error('comment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm sm:text-base text-blue-800">
                    <strong>Not:</strong> Yorumunuz yayınlanmadan önce admin onayından geçecektir. Onaylandıktan sonra ürün sayfasında görünecektir.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 font-medium text-sm sm:text-base transition">
                    Yorumu Gönder
                </button>
                <a href="{{ route('products.show', $product->slug) }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 text-center font-medium text-sm sm:text-base transition">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingStars = document.querySelectorAll('.rating-star');
    let selectedRating = 5;

    // Update star display based on selected rating
    function updateStars(rating) {
        ratingStars.forEach((star, index) => {
            const starRating = 5 - index; // Reverse order (5 to 1)
            const svg = star.querySelector('svg');
            if (starRating <= rating) {
                svg.classList.remove('text-gray-300');
                svg.classList.add('text-yellow-400');
            } else {
                svg.classList.remove('text-yellow-400');
                svg.classList.add('text-gray-300');
            }
        });
    }

    // Handle click on stars
    ratingStars.forEach((star) => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            selectedRating = rating;
            document.getElementById(`rating-${rating}`).checked = true;
            updateStars(rating);
        });

        // Hover effect
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            updateStars(rating);
        });
    });

    // Reset to selected rating on mouse leave
    document.getElementById('ratingContainer').addEventListener('mouseleave', function() {
        updateStars(selectedRating);
    });

    // Initialize with default rating (5)
    updateStars(5);

    // Character counter for comment
    const commentTextarea = document.querySelector('textarea[name="comment"]');
    if (commentTextarea) {
        commentTextarea.addEventListener('input', function() {
            const length = this.value.length;
            const counter = this.parentElement.querySelector('p.text-gray-500');
            if (counter) {
                counter.textContent = `Minimum 10, maksimum 2000 karakter (${length}/2000)`;
                if (length > 2000) {
                    counter.classList.add('text-red-600');
                    counter.classList.remove('text-gray-500');
                } else {
                    counter.classList.remove('text-red-600');
                    counter.classList.add('text-gray-500');
                }
            }
        });
    }
});
</script>
@endsection


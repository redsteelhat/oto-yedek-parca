@extends('layouts.admin')

@section('title', 'Yorum Düzenle')
@section('page-title', 'Yorum Düzenle')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ürün</label>
            <input type="text" value="{{ $review->product->name }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Müşteri</label>
            <input type="text" value="{{ $review->name ?? $review->user->name ?? 'Misafir' }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Puan <span class="text-red-500">*</span></label>
            <select name="rating" class="w-full border rounded px-3 py-2" required>
                <option value="1" {{ $review->rating == 1 ? 'selected' : '' }}>1 Yıldız</option>
                <option value="2" {{ $review->rating == 2 ? 'selected' : '' }}>2 Yıldız</option>
                <option value="3" {{ $review->rating == 3 ? 'selected' : '' }}>3 Yıldız</option>
                <option value="4" {{ $review->rating == 4 ? 'selected' : '' }}>4 Yıldız</option>
                <option value="5" {{ $review->rating == 5 ? 'selected' : '' }}>5 Yıldız</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
            <input type="text" name="title" value="{{ old('title', $review->title) }}" class="w-full border rounded px-3 py-2" maxlength="255">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Yorum <span class="text-red-500">*</span></label>
            <textarea name="comment" rows="5" class="w-full border rounded px-3 py-2" required minlength="10" maxlength="2000">{{ old('comment', $review->comment) }}</textarea>
            <p class="text-sm text-gray-500 mt-1">Minimum 10, maksimum 2000 karakter</p>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', $review->is_approved) ? 'checked' : '' }} class="rounded">
                <span class="ml-2 text-sm text-gray-700">Onaylanmış</span>
            </label>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_verified_purchase" value="1" {{ old('is_verified_purchase', $review->is_verified_purchase) ? 'checked' : '' }} class="rounded">
                <span class="ml-2 text-sm text-gray-700">Doğrulanmış Satın Alma</span>
            </label>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Güncelle
            </button>
            <a href="{{ route('admin.reviews.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                İptal
            </a>
        </div>
    </form>
</div>
@endsection


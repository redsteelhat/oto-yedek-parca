@extends('layouts.admin')

@section('title', 'Kategori Detayı: ' . $category->name)
@section('page-title', 'Kategori Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kategori Bilgileri -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Kategori Bilgileri</h2>
            <a href="{{ route('admin.categories.edit', $category) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Düzenle
            </a>
        </div>

        <div class="space-y-4">
            @if($category->image)
                <div>
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-32 w-32 rounded object-cover">
                </div>
            @endif

            <div>
                <p class="text-sm text-gray-600">Kategori Adı</p>
                <p class="font-semibold text-lg">{{ $category->name }}</p>
            </div>

            @if($category->parent)
                <div>
                    <p class="text-sm text-gray-600">Üst Kategori</p>
                    <a href="{{ route('admin.categories.show', $category->parent) }}" class="font-semibold text-primary-600 hover:text-primary-800">
                        {{ $category->parent->name }}
                    </a>
                </div>
            @endif

            @if($category->description)
                <div>
                    <p class="text-sm text-gray-600 mb-2">Açıklama</p>
                    <p class="text-gray-900">{{ $category->description }}</p>
                </div>
            @endif

            <div>
                <p class="text-sm text-gray-600">Sıralama</p>
                <p class="font-semibold">{{ $category->sort_order ?? 0 }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Durum</p>
                @if($category->is_active)
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                @else
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                @endif
            </div>

            @if($category->meta_title)
                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-2">SEO Bilgileri</h3>
                    <div class="mb-2">
                        <p class="text-sm text-gray-600">Meta Başlık</p>
                        <p class="font-semibold">{{ $category->meta_title }}</p>
                    </div>
                    @if($category->meta_description)
                        <div>
                            <p class="text-sm text-gray-600">Meta Açıklama</p>
                            <p class="text-gray-900">{{ $category->meta_description }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">İstatistikler</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Ürün Sayısı</p>
                <p class="font-bold text-2xl text-primary-600">{{ $category->products->count() ?? 0 }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Alt Kategori Sayısı</p>
                <p class="font-bold text-2xl text-blue-600">{{ $category->children->count() ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Alt Kategoriler -->
@if($category->children->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Alt Kategoriler</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($category->children as $child)
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold mb-2">{{ $child->name }}</h3>
                    <a href="{{ route('admin.categories.show', $child) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                        Detay Gör
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection


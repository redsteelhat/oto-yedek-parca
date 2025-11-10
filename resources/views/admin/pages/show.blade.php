@extends('layouts.admin')

@section('title', 'Sayfa Detayı: ' . $page->title)
@section('page-title', 'Sayfa Detayı')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">{{ $page->title }}</h2>
        <div class="flex space-x-2">
            <a href="{{ route('admin.pages.edit', $page) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Düzenle
            </a>
            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Görüntüle
            </a>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <p class="text-sm text-gray-600">Slug</p>
            <p class="font-semibold">{{ $page->slug }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-600">Durum</p>
            @if($page->is_active)
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
            @else
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
            @endif
        </div>

        <div>
            <p class="text-sm text-gray-600 mb-2">İçerik</p>
            <div class="prose max-w-none border rounded p-4">
                {!! $page->content !!}
            </div>
        </div>

        @if($page->meta_title || $page->meta_description)
            <div class="border-t pt-4">
                <h3 class="text-lg font-semibold mb-4">SEO Ayarları</h3>
                @if($page->meta_title)
                    <div class="mb-2">
                        <p class="text-sm text-gray-600">Meta Başlık</p>
                        <p class="font-semibold">{{ $page->meta_title }}</p>
                    </div>
                @endif
                @if($page->meta_description)
                    <div>
                        <p class="text-sm text-gray-600">Meta Açıklama</p>
                        <p class="font-semibold">{{ $page->meta_description }}</p>
                    </div>
                @endif
            </div>
        @endif

        <div class="border-t pt-4">
            <p class="text-sm text-gray-600">Oluşturulma Tarihi</p>
            <p class="font-semibold">{{ $page->created_at->format('d.m.Y H:i') }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-600">Son Güncelleme</p>
            <p class="font-semibold">{{ $page->updated_at->format('d.m.Y H:i') }}</p>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.pages.index') }}" class="text-primary-600 hover:text-primary-800 underline">
        ← Sayfalara Dön
    </a>
</div>
@endsection


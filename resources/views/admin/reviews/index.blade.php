@extends('layouts.admin')

@section('title', 'Ürün Yorumları')
@section('page-title', 'Ürün Yorumları')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex space-x-2 flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün, müşteri veya yorum ara..." class="border rounded px-3 py-2 flex-1 min-w-64">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Tüm Durumlar</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Onaylanmış</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
        </select>
        <select name="rating" class="border rounded px-3 py-2">
            <option value="">Tüm Puanlar</option>
            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Yıldız</option>
            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Yıldız</option>
            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Yıldız</option>
            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Yıldız</option>
            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Yıldız</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Filtrele
        </button>
    </form>
</div>

<form method="POST" action="{{ route('admin.reviews.bulk-action') }}" id="bulkActionForm">
    @csrf
    <div class="mb-4 flex items-center space-x-2">
        <select name="action" class="border rounded px-3 py-2" required>
            <option value="">Toplu İşlem Seçin</option>
            <option value="approve">Onayla</option>
            <option value="reject">Reddet</option>
            <option value="delete">Sil</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" onclick="return confirm('Seçili yorumlara bu işlemi uygulamak istediğinize emin misiniz?')">
            Uygula
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yorum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="review_ids[]" value="{{ $review->id }}" class="review-checkbox">
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('products.show', $review->product->slug) }}" class="text-primary-600 hover:text-primary-800 font-medium" target="_blank">
                            {{ Str::limit($review->product->name, 30) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="font-medium">{{ $review->name ?? $review->user->name ?? 'Misafir' }}</div>
                            @if($review->email)
                                <div class="text-sm text-gray-500">{{ $review->email }}</div>
                            @endif
                            @if($review->is_verified_purchase)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Doğrulanmış Satın Alma
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                            @endfor
                            <span class="ml-2 text-sm font-medium">{{ $review->rating }}/5</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            @if($review->title)
                                <div class="font-medium mb-1">{{ Str::limit($review->title, 50) }}</div>
                            @endif
                            <div class="text-sm text-gray-600">{{ Str::limit($review->comment, 100) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($review->is_approved)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Onaylanmış
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Beklemede
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $review->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.reviews.edit', $review) }}" class="text-blue-600 hover:text-blue-900">Düzenle</a>
                            @if(!$review->is_approved)
                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Onayla</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">Reddet</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline" onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        Henüz yorum bulunmamaktadır.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

<div class="mt-4">
    {{ $reviews->links() }}
</div>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}
</script>
@endsection


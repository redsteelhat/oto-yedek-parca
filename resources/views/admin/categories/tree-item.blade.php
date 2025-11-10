<div class="category-item border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition" data-category-id="{{ $category->id }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center flex-1">
            <span class="drag-handle cursor-move mr-3 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                </svg>
            </span>
            
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded object-cover mr-3">
            @endif
            
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-900">{{ $category->name }}</span>
                    @if($category->is_active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                    @endif
                    <span class="text-xs text-gray-500">({{ $category->products->count() }} ürün)</span>
                </div>
                @if($category->description)
                    <p class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($category->description, 100) }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-2 ml-4">
            <a href="{{ route('admin.categories.show', $category) }}" class="text-primary-600 hover:text-primary-800 text-sm">Detay</a>
            <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800 text-sm">Düzenle</a>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Sil</button>
            </form>
        </div>
    </div>

    @if($category->children->count() > 0)
        <div class="category-children mt-3 ml-8 space-y-2 border-l-2 border-gray-300 pl-4">
            @foreach($category->children->sortBy('sort_order') as $child)
                @include('admin.categories.tree-item', ['category' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>


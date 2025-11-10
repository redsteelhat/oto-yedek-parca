@extends('layouts.admin')

@section('title', 'Kategoriler')
@section('page-title', 'Kategoriler')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <a href="{{ route('admin.categories.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
            Yeni Kategori Ekle
        </a>
        <a href="{{ route('admin.categories.index', ['view' => 'list']) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            {{ request('view') === 'list' ? 'Tree View' : 'List View' }}
        </a>
    </div>
</div>

@if(request('view') === 'list')
    <!-- List View -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Üst Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıralama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded object-cover mr-3">
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                    @if($category->description)
                                        <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($category->description, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->parent)
                                <span class="text-sm text-gray-900">{{ $category->parent->name }}</span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $category->sort_order ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.categories.show', $category) }}" class="text-primary-600 hover:text-primary-900 mr-3">Detay</a>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 mr-3">Düzenle</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Kategori bulunamadı. <a href="{{ route('admin.categories.create') }}" class="text-primary-600 hover:text-primary-800">İlk kategoriyi ekleyin</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@else
    <!-- Tree View with Drag & Drop -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-4">
            <p class="text-sm text-gray-600">Kategorileri sürükle-bırak ile sıralayabilirsiniz. Alt kategorileri üst kategorilere sürükleyerek hiyerarşi oluşturabilirsiniz.</p>
        </div>
        
        <div id="categoryTree" class="space-y-2">
            @foreach($rootCategories as $category)
                @include('admin.categories.tree-item', ['category' => $category, 'level' => 0])
            @endforeach
        </div>

        @if($rootCategories->isEmpty())
            <div class="text-center text-gray-500 py-8">
                <p>Henüz kategori bulunmuyor.</p>
                <a href="{{ route('admin.categories.create') }}" class="text-primary-600 hover:text-primary-800 underline mt-2 inline-block">
                    İlk kategoriyi ekleyin
                </a>
            </div>
        @endif
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialize Sortable for category tree
const categoryTree = document.getElementById('categoryTree');
if (categoryTree) {
    new Sortable(categoryTree, {
        animation: 150,
        handle: '.drag-handle',
        group: 'categories',
        onEnd: function(evt) {
            updateCategoryOrder();
        }
    });

    // Make nested lists sortable
    document.querySelectorAll('.category-children').forEach(function(childrenList) {
        new Sortable(childrenList, {
            animation: 150,
            handle: '.drag-handle',
            group: 'categories',
            onEnd: function(evt) {
                updateCategoryOrder();
            }
        });
    });
}

function updateCategoryOrder() {
    const tree = document.getElementById('categoryTree');
    const categories = [];
    let sortOrder = 0;

    function extractCategories(element, parentId = null) {
        const items = element.querySelectorAll('.category-item');
        items.forEach(function(item) {
            const categoryId = item.dataset.categoryId;
            const childrenList = item.querySelector('.category-children');
            
            categories.push({
                id: parseInt(categoryId),
                sort_order: sortOrder++,
                parent_id: parentId
            });

            if (childrenList && childrenList.children.length > 0) {
                extractCategories(childrenList, parseInt(categoryId));
            }
        });
    }

    extractCategories(tree);

    fetch('{{ route("admin.categories.update-order") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ categories: categories })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              // Show success message
              const message = document.createElement('div');
              message.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
              message.textContent = 'Kategori sıralaması güncellendi.';
              document.body.appendChild(message);
              setTimeout(() => message.remove(), 3000);
          }
      });
}
</script>
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'Ürün Düzenle: ' . $product->name)
@section('page-title', 'Ürün Düzenle')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Similar to create form but with existing values -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ürün Adı *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">OEM Kodu</label>
                <input type="text" name="oem_code" value="{{ old('oem_code', $product->oem_code) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="category_id" class="w-full border rounded px-3 py-2">
                    <option value="">Kategori Seçin</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat *</label>
                <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İndirimli Fiyat</label>
                <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price', $product->sale_price) }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stok *</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required
                       class="w-full border rounded px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum *</label>
                <select name="status" required class="w-full border rounded px-3 py-2">
                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Taslak</option>
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                <textarea name="description" rows="5"
                          class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Görseller Ekle</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full border rounded px-3 py-2">
            </div>
            
            @if($product->images->count() > 0)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Görseller (Sürükle-bırak ile sıralayabilirsiniz)</label>
                    <div id="imageContainer" class="grid grid-cols-4 gap-4">
                        @foreach($product->images->sortBy('sort_order') as $image)
                            <div class="relative image-item cursor-move" data-image-id="{{ $image->id }}" draggable="true">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="w-full h-24 object-cover rounded">
                                @if($image->is_primary)
                                    <span class="absolute top-0 right-0 bg-primary-600 text-white text-xs px-2 py-1 rounded">Ana</span>
                                @endif
                                <button type="button" onclick="deleteImage({{ $image->id }})" class="absolute top-0 left-0 bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Drag & Drop Image Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Görsel Yükle (Sürükle-Bırak)</label>
                <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary-500 transition">
                    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="hidden" onchange="handleFiles(this.files)">
                    <p class="text-gray-600 mb-2">Görselleri buraya sürükleyin veya</p>
                    <button type="button" onclick="document.getElementById('imageInput').click()" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                        Dosya Seç
                    </button>
                    <div id="imagePreview" class="mt-4 grid grid-cols-4 gap-4"></div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                İptal
            </a>
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                Güncelle
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let draggedElement = null;

// Drag and drop for images
document.querySelectorAll('.image-item').forEach(item => {
    item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.style.opacity = '0.5';
    });

    item.addEventListener('dragend', function() {
        this.style.opacity = '1';
    });

    item.addEventListener('dragover', function(e) {
        e.preventDefault();
        if (this !== draggedElement) {
            this.style.border = '2px dashed #3b82f6';
        }
    });

    item.addEventListener('dragleave', function() {
        this.style.border = 'none';
    });

    item.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.border = 'none';
        
        if (draggedElement && this !== draggedElement) {
            const container = document.getElementById('imageContainer');
            const allItems = Array.from(container.children);
            const draggedIndex = allItems.indexOf(draggedElement);
            const targetIndex = allItems.indexOf(this);

            if (draggedIndex < targetIndex) {
                container.insertBefore(draggedElement, this.nextSibling);
            } else {
                container.insertBefore(draggedElement, this);
            }
            
            updateImageOrder();
        }
    });
});

function updateImageOrder() {
    const container = document.getElementById('imageContainer');
    const imageIds = Array.from(container.children).map(item => item.dataset.imageId);
    
    fetch('{{ route("admin.products.update-image-order", $product) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ image_ids: imageIds })
    });
}

function deleteImage(imageId) {
    if (confirm('Bu görseli silmek istediğinize emin misiniz?')) {
        fetch('{{ route("admin.products.delete-image", $product) }}'.replace('imageId', imageId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            location.reload();
        });
    }
}

// Drag and drop for file upload
const dropZone = document.getElementById('dropZone');
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-primary-500');
});

dropZone.addEventListener('dragleave', function() {
    this.classList.remove('border-primary-500');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-primary-500');
    handleFiles(e.dataTransfer.files);
});

function handleFiles(files) {
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded">
                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-0 right-0 bg-red-600 text-white text-xs px-2 py-1 rounded">✕</button>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
}

imageInput.addEventListener('change', function() {
    handleFiles(this.files);
});
</script>
@endpush
@endsection


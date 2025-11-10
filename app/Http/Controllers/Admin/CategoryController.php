<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends BaseAdminController
{
    public function index(Request $request)
    {
        // Get root categories with children for tree view
        $rootCategories = Category::whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Flat list for table view (if requested)
        if ($request->view === 'list') {
            $categories = Category::with('parent')->latest()->paginate(20);
            return view('admin.categories.index', compact('categories', 'rootCategories'));
        }

        return view('admin.categories.index', compact('rootCategories'));
    }

    /**
     * Update category sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer',
            'categories.*.parent_id' => 'nullable|exists:categories,id',
        ]);

        foreach ($request->categories as $categoryData) {
            Category::where('id', $categoryData['id'])->update([
                'sort_order' => $categoryData['sort_order'],
                'parent_id' => $categoryData['parent_id'] ?? null,
            ]);
        }

        // Clear category cache
        CacheService::clearCategoryCache();

        return response()->json(['success' => true, 'message' => 'Kategori sıralaması güncellendi.']);
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Add tenant_id to validated data
        $validated['tenant_id'] = $this->getCurrentTenantId();
        
        Category::create($validated);

        // Clear category cache
        CacheService::clearCategoryCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->where('is_active', true)
            ->get();
        
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|different:' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        // Clear category cache
        CacheService::clearCategoryCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori başarıyla güncellendi.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Bu kategoriye ait ürünler bulunduğu için silinemez.');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Bu kategoriye ait alt kategoriler bulunduğu için silinemez.');
        }

        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        // Clear category cache
        CacheService::clearCategoryCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori başarıyla silindi.');
    }
}

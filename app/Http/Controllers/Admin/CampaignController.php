<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Campaign::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            }
        }

        $campaigns = $query->orderBy('sort_order')->latest()->paginate(20);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('status', 'active')->get(['id', 'name', 'sku']);

        return view('admin.campaigns.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'type' => 'required|in:product,category,general',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        if (isset($validated['applicable_categories'])) {
            $validated['applicable_categories'] = array_filter($validated['applicable_categories']);
        }

        if (isset($validated['applicable_products'])) {
            $validated['applicable_products'] = array_filter($validated['applicable_products']);
        }

        // Add tenant_id to validated data
        $validated['tenant_id'] = $this->getCurrentTenantId();
        
        Campaign::create($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanya başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        return view('admin.campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('status', 'active')->get(['id', 'name', 'sku']);

        return view('admin.campaigns.edit', compact('campaign', 'categories', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'type' => 'required|in:product,category,general',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($campaign->image) {
                \Storage::disk('public')->delete($campaign->image);
            }
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        if (isset($validated['applicable_categories'])) {
            $validated['applicable_categories'] = array_filter($validated['applicable_categories']);
        }

        if (isset($validated['applicable_products'])) {
            $validated['applicable_products'] = array_filter($validated['applicable_products']);
        }

        $campaign->update($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanya başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        // Delete image
        if ($campaign->image) {
            \Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanya başarıyla silindi.');
    }
}

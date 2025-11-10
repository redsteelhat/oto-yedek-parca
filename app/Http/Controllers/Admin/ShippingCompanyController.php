<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = ShippingCompany::orderBy('sort_order')->latest()->paginate(20);

        return view('admin.shipping-companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shipping-companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:shipping_companies,code',
            'api_type' => 'nullable|in:yurtici_api,aras_api,mng_api,surat_api,manual',
            'api_url' => 'nullable|url',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'api_username' => 'nullable|string|max:255',
            'api_password' => 'nullable|string|max:255',
            'base_price' => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'price_per_cm3' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Handle API config
        if ($request->has('api_config')) {
            $validated['api_config'] = $request->api_config;
        }

        // Add tenant_id to validated data
        $validated['tenant_id'] = $this->getCurrentTenantId();
        
        ShippingCompany::create($validated);

        return redirect()->route('admin.shipping-companies.index')
            ->with('success', 'Kargo firması başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ShippingCompany $shippingCompany)
    {
        return view('admin.shipping-companies.show', compact('shippingCompany'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingCompany $shippingCompany)
    {
        return view('admin.shipping-companies.edit', compact('shippingCompany'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingCompany $shippingCompany)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:shipping_companies,code,' . $shippingCompany->id,
            'api_type' => 'nullable|in:yurtici_api,aras_api,mng_api,surat_api,manual',
            'api_url' => 'nullable|url',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'api_username' => 'nullable|string|max:255',
            'api_password' => 'nullable|string|max:255',
            'base_price' => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'price_per_cm3' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle API config
        if ($request->has('api_config')) {
            $validated['api_config'] = $request->api_config;
        }

        $shippingCompany->update($validated);

        return redirect()->route('admin.shipping-companies.index')
            ->with('success', 'Kargo firması başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingCompany $shippingCompany)
    {
        $shippingCompany->delete();

        return redirect()->route('admin.shipping-companies.index')
            ->with('success', 'Kargo firması başarıyla silindi.');
    }
}

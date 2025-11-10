<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index(Request $request)
    {
        $query = Tenant::withTrashed();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by subscription plan
        if ($request->has('subscription_plan') && $request->subscription_plan) {
            $query->where('subscription_plan', $request->subscription_plan);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
            'inactive' => Tenant::where('status', 'inactive')->count(),
        ];

        return view('super-admin.tenants.index', compact('tenants', 'stats'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('super-admin.tenants.create');
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:tenants,subdomain|regex:/^[a-z0-9-]+$/',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:512',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'status' => 'required|in:active,suspended,inactive',
            'subscription_plan' => 'required|in:free,basic,premium,enterprise',
            'subscription_expires_at' => 'nullable|date',
            'max_products' => 'nullable|integer|min:0',
            'max_users' => 'nullable|integer|min:0',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('tenants/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('tenants/favicons', 'public');
        }

        $tenant = Tenant::create($validated);

        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant başarıyla oluşturuldu.');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load([
            'users',
            'products',
            'orders',
            'categories',
            'coupons',
            'campaigns',
        ]);

        // Statistics
        $stats = [
            'users_count' => $tenant->users()->count(),
            'products_count' => $tenant->products()->count(),
            'orders_count' => $tenant->orders()->count(),
            'categories_count' => $tenant->categories()->count(),
            'total_revenue' => $tenant->orders()->where('payment_status', 'paid')->sum('total'),
        ];

        return view('super-admin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:tenants,subdomain,' . $tenant->id . '|regex:/^[a-z0-9-]+$/',
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:512',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'status' => 'required|in:active,suspended,inactive',
            'subscription_plan' => 'required|in:free,basic,premium,enterprise',
            'subscription_expires_at' => 'nullable|date',
            'max_products' => 'nullable|integer|min:0',
            'max_users' => 'nullable|integer|min:0',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $validated['logo'] = $request->file('logo')->store('tenants/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon
            if ($tenant->favicon) {
                Storage::disk('public')->delete($tenant->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('tenants/favicons', 'public');
        }

        $tenant->update($validated);

        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant başarıyla güncellendi.');
    }

    /**
     * Remove the specified tenant (soft delete).
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant başarıyla silindi.');
    }

    /**
     * Restore a soft deleted tenant.
     */
    public function restore($id)
    {
        $tenant = Tenant::withTrashed()->findOrFail($id);
        $tenant->restore();

        return redirect()->back()
            ->with('success', 'Tenant başarıyla geri yüklendi.');
    }

    /**
     * Permanently delete a tenant.
     */
    public function forceDelete($id)
    {
        $tenant = Tenant::withTrashed()->findOrFail($id);
        
        // Delete logo and favicon
        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
        }
        if ($tenant->favicon) {
            Storage::disk('public')->delete($tenant->favicon);
        }

        $tenant->forceDelete();

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant kalıcı olarak silindi.');
    }

    /**
     * Suspend a tenant.
     */
    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);

        return redirect()->back()
            ->with('success', 'Tenant askıya alındı.');
    }

    /**
     * Activate a tenant.
     */
    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Tenant aktif edildi.');
    }
}




<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Campaign;
use App\Models\Tenant;
use App\Services\CacheService;
use App\Services\TenantService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(TenantService $tenantService)
    {
        $currentTenant = $tenantService->getCurrentTenant();

        // Tenant seçilmemişse (public ana sayfa)
        if (!$currentTenant) {
            $tenantHighlights = Tenant::where('status', 'active')
                ->withCount(['products as active_products_count' => function ($query) {
                    $query->where('status', 'active');
                }])
                ->with(['products' => function ($query) {
                    $query->with(['primaryImage'])
                        ->where('status', 'active')
                        ->latest()
                        ->take(4);
                }])
                ->orderBy('name')
                ->get();

            $recentProducts = Product::with(['primaryImage', 'tenant'])
                ->where('status', 'active')
                ->latest()
                ->take(12)
                ->get();

            $totalProducts = Product::where('status', 'active')->count();

            $popularCategories = Category::withCount('products')
                ->orderBy('products_count', 'desc')
                ->take(8)
                ->get();

            $totalCategories = Category::where('is_active', true)->count();

            $tenantHighlights->each(function ($tenant) use ($tenantService) {
                $tenant->visit_url = $tenantService->getTenantUrl($tenant);

                $tenant->products->each(function ($product) use ($tenantService, $tenant) {
                    $product->setAttribute('tenant_url', $tenantService->getTenantUrl($tenant, 'products/' . $product->slug));
                    $product->setAttribute('tenant_name', $tenant->name);
                });
            });

            $recentProducts->each(function ($product) use ($tenantService) {
                if ($product->relationLoaded('tenant') && $product->tenant) {
                    $product->setAttribute('tenant_url', $tenantService->getTenantUrl($product->tenant, 'products/' . $product->slug));
                    $product->setAttribute('tenant_name', $product->tenant->name);
                }
            });

            return view('frontend.home', [
                'isAggregator' => true,
                'tenantHighlights' => $tenantHighlights,
                'recentProducts' => $recentProducts,
                'popularCategories' => $popularCategories,
                'totalProducts' => $totalProducts,
                'totalCategories' => $totalCategories,
                'featuredProducts' => collect(),
                'newProducts' => collect(),
                'bestsellers' => collect(),
                'categories' => collect(),
                'campaigns' => collect(),
            ]);
        }

        // Tenant ana sayfası
        $featuredProducts = CacheService::getFeaturedProducts(8);
        $newProducts = CacheService::getNewProducts(8);
        $bestsellers = CacheService::getBestsellerProducts(8);

        $categories = CacheService::getActiveCategories()->take(6);

        $campaigns = Campaign::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.home', compact('featuredProducts', 'newProducts', 'bestsellers', 'categories', 'campaigns'));
    }
}

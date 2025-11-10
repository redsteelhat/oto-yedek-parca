<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantService;

class CacheService
{
    /**
     * Cache duration in minutes
     */
    const CATEGORY_CACHE_DURATION = 60; // 1 hour
    const PRODUCT_CACHE_DURATION = 30; // 30 minutes
    const PRICE_RANGE_CACHE_DURATION = 60; // 1 hour

    /**
     * Get active categories with children (cached)
     */
    public static function getActiveCategories()
    {
        return Cache::remember(self::cacheKey('categories.active'), self::CATEGORY_CACHE_DURATION, function () {
            return Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Get category by slug (cached)
     */
    public static function getCategoryBySlug($slug)
    {
        return Cache::remember(self::cacheKey("category.slug.{$slug}"), self::CATEGORY_CACHE_DURATION, function () use ($slug) {
            return Category::where('slug', $slug)
                ->where('is_active', true)
                ->with('children')
                ->first();
        });
    }

    /**
     * Get featured products (cached)
     */
    public static function getFeaturedProducts($limit = 8)
    {
        return Cache::remember(self::cacheKey("products.featured.{$limit}"), self::PRODUCT_CACHE_DURATION, function () use ($limit) {
            return Product::with(['primaryImage', 'category'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->latest()
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get new products (cached)
     */
    public static function getNewProducts($limit = 8)
    {
        return Cache::remember(self::cacheKey("products.new.{$limit}"), self::PRODUCT_CACHE_DURATION, function () use ($limit) {
            return Product::with(['primaryImage', 'category'])
                ->where('status', 'active')
                ->latest()
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get bestseller products (cached)
     */
    public static function getBestsellerProducts($limit = 8)
    {
        return Cache::remember(self::cacheKey("products.bestseller.{$limit}"), self::PRODUCT_CACHE_DURATION, function () use ($limit) {
            return Product::with(['primaryImage', 'category'])
                ->where('status', 'active')
                ->orderBy('sales_count', 'desc')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get price range for products (cached)
     */
    public static function getPriceRange()
    {
        return Cache::remember(self::cacheKey('products.price_range'), self::PRICE_RANGE_CACHE_DURATION, function () {
            return Product::where('status', 'active')
                ->selectRaw('MIN(COALESCE(sale_price, price)) as min_price, MAX(COALESCE(sale_price, price)) as max_price')
                ->first();
        });
    }

    /**
     * Clear category cache
     */
    public static function clearCategoryCache()
    {
        Cache::forget(self::cacheKey('categories.active'));
        // Clear all category slug caches
        $categories = Category::pluck('slug');
        foreach ($categories as $slug) {
            Cache::forget(self::cacheKey("category.slug.{$slug}"));
        }
    }

    /**
     * Clear product cache
     */
    public static function clearProductCache()
    {
        // Clear featured, new, bestseller caches
        for ($i = 1; $i <= 20; $i++) {
            Cache::forget(self::cacheKey("products.featured.{$i}"));
            Cache::forget(self::cacheKey("products.new.{$i}"));
            Cache::forget(self::cacheKey("products.bestseller.{$i}"));
        }
        Cache::forget(self::cacheKey('products.price_range'));
    }

    /**
     * Clear all caches
     */
    public static function clearAll()
    {
        self::clearCategoryCache();
        self::clearProductCache();
    }

    /**
     * Generate tenant-aware cache key.
     */
    protected static function cacheKey(string $suffix): string
    {
        $tenantId = app(TenantService::class)->getCurrentTenantId();
        $prefix = $tenantId ? "tenant:{$tenantId}:" : 'public:';

        return $prefix . $suffix;
    }
}


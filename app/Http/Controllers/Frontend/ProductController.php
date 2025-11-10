<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\CarYear;
use App\Services\CacheService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage', 'category'])
            ->where('status', 'active');

        // Multiple category filter
        if ($request->categories && is_array($request->categories)) {
            $categoryIds = [];
            foreach ($request->categories as $categorySlug) {
                $category = CacheService::getCategoryBySlug($categorySlug);
                if ($category) {
                    $categoryIds[] = $category->id;
                    $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
                }
            }
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        } elseif ($request->category) {
            // Single category filter (backward compatibility) - use cached
            $category = CacheService::getCategoryBySlug($request->category);
            if ($category) {
                $categoryIds = [$category->id];
                $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Car compatibility filter
        if ($request->brand_id || $request->model_id || $request->year_id) {
            $query->whereHas('compatibleCars', function ($q) use ($request) {
                if ($request->year_id) {
                    $q->where('cars_years.id', $request->year_id);
                } elseif ($request->model_id) {
                    $q->where('cars_years.model_id', $request->model_id);
                } elseif ($request->brand_id) {
                    $q->whereHas('model', function ($q2) use ($request) {
                        $q2->where('brand_id', $request->brand_id);
                    });
                }
            });
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('oem_code', 'like', '%' . $request->search . '%');
            });
        }

        // Price filter (use sale_price if available, otherwise price)
        if ($request->min_price || $request->max_price) {
            $query->where(function ($q) use ($request) {
                if ($request->min_price) {
                    $q->where(function ($subQ) use ($request) {
                        $subQ->where(function ($orQ) use ($request) {
                            $orQ->whereNotNull('sale_price')
                                ->where('sale_price', '>=', $request->min_price);
                        })->orWhere(function ($orQ) use ($request) {
                            $orQ->whereNull('sale_price')
                                ->where('price', '>=', $request->min_price);
                        });
                    });
                }
                if ($request->max_price) {
                    $q->where(function ($subQ) use ($request) {
                        $subQ->where(function ($orQ) use ($request) {
                            $orQ->whereNotNull('sale_price')
                                ->where('sale_price', '<=', $request->max_price);
                        })->orWhere(function ($orQ) use ($request) {
                            $orQ->whereNull('sale_price')
                                ->where('price', '<=', $request->max_price);
                        });
                    });
                }
            });
        }

        // Stock status filter
        if ($request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('stock', '<=', 'min_stock_level')
                          ->where('stock', '>', 0);
                    break;
            }
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'sales':
                $query->orderBy('sales_count', 'desc');
                break;
            default:
                $query->latest();
        }

        // Get price range for slider (cached)
        $priceRange = CacheService::getPriceRange();

        $products = $query->paginate(20);
        $categories = CacheService::getActiveCategories();
        $brands = CarBrand::where('is_active', true)->get();

        // Get user wishlist product IDs if logged in
        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = \App\Models\Wishlist::where('user_id', auth()->id())
                ->pluck('product_id')
                ->toArray();
        }

        return view('frontend.products.index', compact('products', 'categories', 'brands', 'priceRange', 'wishlistProductIds'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        $products = Product::with(['primaryImage'])
            ->where('category_id', $category->id)
            ->where('status', 'active')
            ->latest()
            ->paginate(20);

        return view('frontend.products.category', compact('category', 'products'));
    }

    public function show($slug)
    {
        $product = Product::with(['images', 'category', 'compatibleCars.model.brand', 'supplier'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Increment view count
        $product->increment('views');

        // Add to recently viewed products (session)
        $recentlyViewed = session('recently_viewed', []);
        if (!in_array($product->id, $recentlyViewed)) {
            array_unshift($recentlyViewed, $product->id);
            $recentlyViewed = array_slice($recentlyViewed, 0, 20); // Keep last 20
            session(['recently_viewed' => $recentlyViewed]);
        }

        // Related products
        $relatedProducts = Product::with(['primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        // Check if product is in wishlist
        $inWishlist = false;
        if (auth()->check()) {
            $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
        }

        // Get approved reviews
        $reviews = $product->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        // Check if user has purchased this product (for verified purchase badge)
        $hasPurchased = false;
        if (auth()->check()) {
            $hasPurchased = auth()->user()->hasPurchasedProduct($product->id);
        }

        return view('frontend.products.show', compact('product', 'relatedProducts', 'inWishlist', 'reviews', 'hasPurchased'));
    }

    public function findByCar(Request $request)
    {
        $query = Product::with(['primaryImage', 'category'])
            ->where('status', 'active');

        // Car compatibility filter
        if ($request->year_id) {
            $query->whereHas('compatibleCars', function ($q) use ($request) {
                $q->where('cars_years.id', $request->year_id);
            });
        } elseif ($request->model_id) {
            $query->whereHas('compatibleCars', function ($q) use ($request) {
                $q->where('cars_years.model_id', $request->model_id);
            });
        } elseif ($request->brand_id) {
            $query->whereHas('compatibleCars', function ($q) use ($request) {
                $q->whereHas('model', function ($q2) use ($request) {
                    $q2->where('brand_id', $request->brand_id);
                });
            });
        }

        // Category filter
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Price filter
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'sales':
                $query->orderBy('sales_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(20);
        $categories = Category::where('is_active', true)->whereNull('parent_id')->get();
        $brands = CarBrand::where('is_active', true)->get();
        
        // Selected car info
        $selectedCar = null;
        if ($request->year_id) {
            $selectedCar = CarYear::with(['model.brand'])->find($request->year_id);
        } elseif ($request->model_id) {
            $selectedCar = CarModel::with('brand')->find($request->model_id);
        } elseif ($request->brand_id) {
            $selectedCar = CarBrand::find($request->brand_id);
        }

        return view('frontend.products.find-by-car', compact('products', 'categories', 'brands', 'selectedCar'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->get('q', $request->get('search'));
        
        if (empty($searchTerm)) {
            return redirect()->route('products.index');
        }

        $query = Product::with(['primaryImage', 'category'])
            ->where('status', 'active')
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%')
                  ->orWhere('oem_code', 'like', '%' . $searchTerm . '%');
            });

        // Category filter
        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $categoryIds = [$category->id];
                $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'sales':
                $query->orderBy('sales_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(20);
        $categories = Category::where('is_active', true)->whereNull('parent_id')->get();
        $brands = CarBrand::where('is_active', true)->get();

        return view('frontend.products.search', compact('products', 'categories', 'brands', 'searchTerm'));
    }
}

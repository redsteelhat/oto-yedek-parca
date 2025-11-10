<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * Store a new review.
     */
    public function store(Request $request, $productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        // Check if user is authenticated
        $userId = Auth::id();
        $isVerifiedPurchase = false;

        if ($userId) {
            // Check if user has purchased this product
            $user = Auth::user();
            $isVerifiedPurchase = $user->hasPurchasedProduct($product->id);
        } else {
            // Guest review - name and email are required
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);
        }

        // Get current tenant ID
        $tenantId = app(\App\Services\TenantService::class)->getCurrentTenantId();
        
        $review = ProductReview::create([
            'product_id' => $product->id,
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'name' => $validated['name'] ?? Auth::user()->name ?? null,
            'email' => $validated['email'] ?? Auth::user()->email ?? null,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'is_approved' => false, // Admin onayı gerekiyor
            'is_verified_purchase' => $isVerifiedPurchase,
        ]);

        return redirect()->back()->with('success', 'Yorumunuz başarıyla gönderildi. Onay sürecinden sonra yayınlanacaktır.');
    }

    /**
     * Show review form.
     */
    public function create($productSlug)
    {
        $product = Product::where('slug', $productSlug)
            ->where('status', 'active')
            ->firstOrFail();
        
        // Check if user can review (has purchased or is guest)
        $canReview = true;
        $hasPurchased = false;

        if (Auth::check()) {
            $hasPurchased = Auth::user()->hasPurchasedProduct($product->id);
            
            // Check if user already reviewed this product
            $existingReview = ProductReview::where('product_id', $product->id)
                ->where('user_id', Auth::id())
                ->exists();
            
            if ($existingReview) {
                return redirect()->route('products.show', $product->slug)
                    ->with('error', 'Bu ürün için zaten bir yorum yaptınız.');
            }
        }

        return view('frontend.products.reviews.create', compact('product', 'hasPurchased', 'canReview'));
    }
}


<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = Auth::user()->wishlistProducts()->with(['primaryImage', 'category'])->paginate(20);
        
        return view('frontend.account.wishlist', compact('wishlistItems'));
    }

    public function add(Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Giriş yapmanız gerekiyor'], 401);
        }

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ürün zaten favorilerinizde']);
        }

        // Get current tenant ID
        $tenantId = app(\App\Services\TenantService::class)->getCurrentTenantId();
        
        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'tenant_id' => $tenantId,
        ]);

        return response()->json(['success' => true, 'message' => 'Ürün favorilere eklendi']);
    }

    public function remove(Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Giriş yapmanız gerekiyor'], 401);
        }

        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Ürün favorilerden kaldırıldı']);
    }

    public function toggle(Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Giriş yapmanız gerekiyor'], 401);
        }

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => true, 'added' => false, 'message' => 'Ürün favorilerden kaldırıldı']);
        } else {
            // Get current tenant ID
            $tenantId = app(\App\Services\TenantService::class)->getCurrentTenantId();
            
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'tenant_id' => $tenantId,
            ]);
            return response()->json(['success' => true, 'added' => true, 'message' => 'Ürün favorilere eklendi']);
        }
    }

    public function check(Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['in_wishlist' => false]);
        }

        $inWishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->exists();

        return response()->json(['in_wishlist' => $inWishlist]);
    }
}


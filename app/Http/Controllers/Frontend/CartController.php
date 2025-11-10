<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $items = [];
        $total = 0;
        $subtotal = 0;
        $tax = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $price = $product->final_price;
                $itemTotal = $price * $item['quantity'];
                $itemTax = ($itemTotal * $product->tax_rate) / 100;
                
                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $itemTotal,
                    'tax' => $itemTax,
                ];

                $subtotal += $itemTotal;
                $tax += $itemTax;
            }
        }

        // Apply campaign discounts
        $campaignDiscount = 0;
        $activeCampaigns = Campaign::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('sort_order')
            ->get();

        foreach ($activeCampaigns as $campaign) {
            if (!$campaign->isActive()) {
                continue;
            }

            // Check if campaign applies to cart items
            $appliesToCart = false;

            if ($campaign->type === 'general') {
                // General campaign applies to all
                $appliesToCart = true;
            } elseif ($campaign->type === 'product' && $campaign->applicable_products) {
                // Check if any product in cart matches
                foreach ($items as $item) {
                    if (in_array($item['product']->id, $campaign->applicable_products)) {
                        $appliesToCart = true;
                        break;
                    }
                }
            } elseif ($campaign->type === 'category' && $campaign->applicable_categories) {
                // Check if any product category in cart matches
                foreach ($items as $item) {
                    if ($item['product']->category_id && in_array($item['product']->category_id, $campaign->applicable_categories)) {
                        $appliesToCart = true;
                        break;
                    }
                }
            }

            if ($appliesToCart) {
                $campaignDiscount += $campaign->calculateDiscount($subtotal);
                break; // Only apply first matching campaign
            }
        }

        // Apply coupon if exists
        $couponCode = Session::get('coupon_code');
        $coupon = null;
        $couponDiscount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isActive()) {
                // Check user limit
                if (auth()->check()) {
                    $userCouponUsage = \App\Models\Order::where('user_id', auth()->id())
                        ->where('coupon_id', $coupon->id)
                        ->count();
                    
                    if ($userCouponUsage >= $coupon->usage_limit_per_user) {
                        $coupon = null;
                        Session::forget('coupon_code');
                    } else {
                        $couponDiscount = $coupon->calculateDiscount($subtotal);
                    }
                } else {
                    $couponDiscount = $coupon->calculateDiscount($subtotal);
                }
            } else {
                Session::forget('coupon_code');
            }
        }

        $discountAmount = $campaignDiscount + $couponDiscount;
        $total = $subtotal + $tax - $discountAmount;

        return view('frontend.cart.index', compact('items', 'subtotal', 'tax', 'total', 'coupon', 'couponCode', 'discountAmount', 'campaignDiscount', 'couponDiscount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'active') {
            return back()->with('error', 'Bu ürün şu anda satışta değil.');
        }

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Yeterli stok bulunmuyor.');
        }

        $cart = Session::get('cart', []);
        $productId = $product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $request->quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $request->quantity,
            ];
        }

        Session::put('cart', $cart);

        // Get cart count for response
        $cartCount = count($cart);

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ürün sepete eklendi.',
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Ürün sepete eklendi.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Yeterli stok bulunmuyor.');
        }

        $cart = Session::get('cart', []);
        $productId = $product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Sepet güncellendi.');
    }

    public function remove($id)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Ürün sepetten çıkarıldı.');
    }

    public function clear()
    {
        Session::forget('cart');
        Session::forget('coupon_code');
        return back()->with('success', 'Sepet temizlendi.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return back()->with('error', 'Geçersiz kupon kodu.');
        }

        if (!$coupon->isActive()) {
            return back()->with('error', 'Bu kupon geçersiz veya süresi dolmuş.');
        }

        // Check user limit
        if (auth()->check()) {
            $userCouponUsage = \App\Models\Order::where('user_id', auth()->id())
                ->where('coupon_id', $coupon->id)
                ->count();
            
            if ($userCouponUsage >= $coupon->usage_limit_per_user) {
                return back()->with('error', 'Bu kuponu daha fazla kullanamazsınız.');
            }
        }

        // Check minimum purchase amount
        $cart = Session::get('cart', []);
        $subtotal = 0;
        $cartItems = [];
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->status === 'active') {
                $subtotal += $product->final_price * $item['quantity'];
                $cartItems[] = $product;
            }
        }

        if ($coupon->min_purchase_amount && $subtotal < $coupon->min_purchase_amount) {
            return back()->with('error', 'Bu kupon için minimum ' . number_format($coupon->min_purchase_amount, 2) . ' ₺ alışveriş yapmalısınız.');
        }

        // Check if coupon applies to cart items
        $appliesToCart = true;

        if ($coupon->applicable_products && !empty($coupon->applicable_products)) {
            $appliesToCart = false;
            foreach ($cartItems as $product) {
                if (in_array($product->id, $coupon->applicable_products)) {
                    $appliesToCart = true;
                    break;
                }
            }
            if (!$appliesToCart) {
                return back()->with('error', 'Bu kupon sepetinizdeki ürünlere uygulanamaz.');
            }
        }

        if ($coupon->applicable_categories && !empty($coupon->applicable_categories)) {
            $appliesToCart = false;
            foreach ($cartItems as $product) {
                if ($product->category_id && in_array($product->category_id, $coupon->applicable_categories)) {
                    $appliesToCart = true;
                    break;
                }
            }
            if (!$appliesToCart) {
                return back()->with('error', 'Bu kupon sepetinizdeki kategorilere uygulanamaz.');
            }
        }

        Session::put('coupon_code', $coupon->code);

        $discount = $coupon->calculateDiscount($subtotal);
        return back()->with('success', 'Kupon uygulandı! İndirim: ' . number_format($discount, 2) . ' ₺');
    }

    public function removeCoupon()
    {
        Session::forget('coupon_code');
        return back()->with('success', 'Kupon kaldırıldı.');
    }
}

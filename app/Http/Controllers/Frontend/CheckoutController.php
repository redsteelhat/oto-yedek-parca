<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Campaign;
use App\Models\Address;
use App\Models\ShippingCompany;
use App\Mail\OrderConfirmation;
use App\Notifications\OrderSmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Get cart totals and discounts
     */
    private function getCartTotals()
    {
        $cart = Session::get('cart', []);
        $items = [];
        $subtotal = 0;
        $tax = 0;
        $weight = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->status === 'active' && $product->stock >= $item['quantity']) {
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
                // Estimate weight (you can add weight field to products later)
                $weight += $item['quantity'] * 1; // Assuming 1kg per product
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

            $appliesToCart = false;

            if ($campaign->type === 'general') {
                $appliesToCart = true;
            } elseif ($campaign->type === 'product' && $campaign->applicable_products) {
                foreach ($items as $item) {
                    if (in_array($item['product']->id, $campaign->applicable_products)) {
                        $appliesToCart = true;
                        break;
                    }
                }
            } elseif ($campaign->type === 'category' && $campaign->applicable_categories) {
                foreach ($items as $item) {
                    if ($item['product']->category_id && in_array($item['product']->category_id, $campaign->applicable_categories)) {
                        $appliesToCart = true;
                        break;
                    }
                }
            }

            if ($appliesToCart) {
                $campaignDiscount += $campaign->calculateDiscount($subtotal);
                break;
            }
        }

        // Apply coupon if exists
        $couponCode = Session::get('coupon_code');
        $coupon = null;
        $couponDiscount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isActive()) {
                if (auth()->check()) {
                    $userCouponUsage = Order::where('user_id', auth()->id())
                        ->where('coupon_id', $coupon->id)
                        ->count();
                    
                    if ($userCouponUsage < $coupon->usage_limit_per_user) {
                        $couponDiscount = $coupon->calculateDiscount($subtotal);
                    } else {
                        Session::forget('coupon_code');
                        $coupon = null;
                    }
                } else {
                    $couponDiscount = $coupon->calculateDiscount($subtotal);
                }
            } else {
                Session::forget('coupon_code');
            }
        }

        $discountAmount = $campaignDiscount + $couponDiscount;
        
        return compact('items', 'subtotal', 'tax', 'discountAmount', 'campaignDiscount', 'couponDiscount', 'coupon', 'couponCode', 'weight');
    }

    /**
     * Step 1: Address Selection
     */
    public function step1()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        $totals = $this->getCartTotals();
        if (empty($totals['items'])) {
            return redirect()->route('cart.index')->with('error', 'Sepetinizde geçerli ürün bulunmuyor.');
        }

        $addresses = auth()->check() ? auth()->user()->addresses : collect();
        $checkoutData = Session::get('checkout_data', []);

        return view('frontend.checkout.step1', compact('addresses', 'checkoutData', 'totals'));
    }

    /**
     * Store Step 1: Address Selection
     */
    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'nullable|exists:addresses,id',
            'shipping_name' => 'required_without:address_id|string|max:255',
            'shipping_phone' => 'required_without:address_id|string|max:20',
            'shipping_city' => 'required_without:address_id|string|max:255',
            'shipping_district' => 'required_without:address_id|string|max:255',
            'shipping_address' => 'required_without:address_id|string',
            'shipping_postal_code' => 'nullable|string|max:10',
            'billing_same_as_shipping' => 'boolean',
            'billing_name' => 'nullable|string|max:255',
            'billing_phone' => 'nullable|string|max:20',
            'billing_city' => 'nullable|string|max:255',
            'billing_district' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'billing_postal_code' => 'nullable|string|max:10',
        ]);

        // If address_id is provided, use that address
        if ($request->address_id) {
            $address = Address::find($request->address_id);
            $checkoutData = [
                'shipping_name' => $address->first_name . ' ' . $address->last_name,
                'shipping_phone' => $address->phone,
                'shipping_city' => $address->city,
                'shipping_district' => $address->district,
                'shipping_address' => $address->address,
                'shipping_postal_code' => $address->postal_code,
                'address_id' => $address->id,
            ];
        } else {
            $checkoutData = [
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_district' => $validated['shipping_district'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_postal_code' => $validated['shipping_postal_code'] ?? null,
            ];
        }

        // Handle billing address
        if ($request->has('billing_same_as_shipping') && $request->billing_same_as_shipping) {
            $checkoutData['billing_name'] = $checkoutData['shipping_name'];
            $checkoutData['billing_phone'] = $checkoutData['shipping_phone'];
            $checkoutData['billing_city'] = $checkoutData['shipping_city'];
            $checkoutData['billing_district'] = $checkoutData['shipping_district'];
            $checkoutData['billing_address'] = $checkoutData['shipping_address'];
            $checkoutData['billing_postal_code'] = $checkoutData['shipping_postal_code'];
        } else {
            $checkoutData['billing_name'] = $validated['billing_name'] ?? $checkoutData['shipping_name'];
            $checkoutData['billing_phone'] = $validated['billing_phone'] ?? $checkoutData['shipping_phone'];
            $checkoutData['billing_city'] = $validated['billing_city'] ?? $checkoutData['shipping_city'];
            $checkoutData['billing_district'] = $validated['billing_district'] ?? $checkoutData['shipping_district'];
            $checkoutData['billing_address'] = $validated['billing_address'] ?? $checkoutData['shipping_address'];
            $checkoutData['billing_postal_code'] = $validated['billing_postal_code'] ?? $checkoutData['shipping_postal_code'];
        }

        Session::put('checkout_data', $checkoutData);

        return redirect()->route('checkout.step2');
    }

    /**
     * Step 2: Shipping Selection
     */
    public function step2()
    {
        $checkoutData = Session::get('checkout_data');
        
        if (!$checkoutData) {
            return redirect()->route('checkout.step1')->with('error', 'Lütfen önce adres bilgilerinizi girin.');
        }

        $totals = $this->getCartTotals();
        $shippingCompanies = ShippingCompany::getActive();
        $selectedShipping = $checkoutData['shipping_company_id'] ?? null;
        $shippingCost = 0;

        // Calculate shipping cost for each company
        $shippingOptions = [];
        foreach ($shippingCompanies as $company) {
            $cost = $company->calculateShippingCost(
                $totals['weight'] ?? 0,
                0, // volume - can be calculated later
                $totals['subtotal']
            );
            $shippingOptions[] = [
                'company' => $company,
                'cost' => $cost,
                'estimated_days' => $company->estimated_days,
            ];
        }

        // If shipping company is already selected, calculate cost
        if ($selectedShipping) {
            $shippingCompany = ShippingCompany::find($selectedShipping);
            if ($shippingCompany && $shippingCompany->is_active) {
                $shippingCost = $shippingCompany->calculateShippingCost(
                    $totals['weight'] ?? 0,
                    0,
                    $totals['subtotal']
                );
            }
        }

        return view('frontend.checkout.step2', compact('shippingOptions', 'selectedShipping', 'shippingCost', 'totals', 'checkoutData'));
    }

    /**
     * Store Step 2: Shipping Selection
     */
    public function storeStep2(Request $request)
    {
        $validated = $request->validate([
            'shipping_company_id' => 'required|exists:shipping_companies,id',
        ]);

        $checkoutData = Session::get('checkout_data', []);
        $checkoutData['shipping_company_id'] = $validated['shipping_company_id'];
        Session::put('checkout_data', $checkoutData);

        return redirect()->route('checkout.step3');
    }

    /**
     * Step 3: Payment Method Selection
     */
    public function step3()
    {
        $checkoutData = Session::get('checkout_data');
        
        if (!$checkoutData || !isset($checkoutData['shipping_company_id'])) {
            return redirect()->route('checkout.step2')->with('error', 'Lütfen önce kargo firması seçin.');
        }

        $totals = $this->getCartTotals();
        $shippingCompany = ShippingCompany::find($checkoutData['shipping_company_id']);
        $shippingCost = $shippingCompany->calculateShippingCost(
            $totals['weight'] ?? 0,
            0,
            $totals['subtotal']
        );

        $total = $totals['subtotal'] + $totals['tax'] + $shippingCost - $totals['discountAmount'];

        // Get payment methods from settings
        $paymentMethods = [];
        if (\App\Models\Setting::getValue('payment_iyzico_enabled', false)) {
            $paymentMethods['credit_card'] = 'Kredi Kartı (İyzico)';
        }
        if (\App\Models\Setting::getValue('payment_paytr_enabled', false)) {
            $paymentMethods['credit_card'] = 'Kredi Kartı (PayTR)';
        }
        if (\App\Models\Setting::getValue('payment_bank_transfer_enabled', false)) {
            $paymentMethods['bank_transfer'] = 'Havale/EFT';
        }
        if (\App\Models\Setting::getValue('payment_cash_on_delivery_enabled', false)) {
            $paymentMethods['cash_on_delivery'] = 'Kapıda Ödeme';
        }

        // Default payment methods if no settings
        if (empty($paymentMethods)) {
            $paymentMethods = [
                'credit_card' => 'Kredi Kartı',
                'bank_transfer' => 'Havale/EFT',
                'cash_on_delivery' => 'Kapıda Ödeme',
            ];
        }

        $selectedPaymentMethod = $checkoutData['payment_method'] ?? null;

        return view('frontend.checkout.step3', compact('paymentMethods', 'selectedPaymentMethod', 'totals', 'shippingCost', 'total', 'checkoutData', 'shippingCompany'));
    }

    /**
     * Store Step 3: Payment Method Selection
     */
    public function storeStep3(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer,cash_on_delivery',
        ]);

        $checkoutData = Session::get('checkout_data', []);
        $checkoutData['payment_method'] = $validated['payment_method'];
        Session::put('checkout_data', $checkoutData);

        return redirect()->route('checkout.step4');
    }

    /**
     * Step 4: Review and Confirm
     */
    public function step4()
    {
        $checkoutData = Session::get('checkout_data');
        
        if (!$checkoutData || !isset($checkoutData['payment_method'])) {
            return redirect()->route('checkout.step3')->with('error', 'Lütfen önce ödeme yöntemi seçin.');
        }

        $totals = $this->getCartTotals();
        $shippingCompany = ShippingCompany::find($checkoutData['shipping_company_id']);
        $shippingCost = $shippingCompany->calculateShippingCost(
            $totals['weight'] ?? 0,
            0,
            $totals['subtotal']
        );

        $total = $totals['subtotal'] + $totals['tax'] + $shippingCost - $totals['discountAmount'];

        return view('frontend.checkout.step4', compact('totals', 'shippingCost', 'total', 'checkoutData', 'shippingCompany'));
    }

    /**
     * Legacy index method - redirect to step1
     */
    public function index()
    {
        return redirect()->route('checkout.step1');
    }

    /**
     * Store Step 4: Create Order
     */
    public function store(Request $request)
    {
        $checkoutData = Session::get('checkout_data');
        
        if (!$checkoutData) {
            return redirect()->route('checkout.step1')->with('error', 'Lütfen önce adres bilgilerinizi girin.');
        }

        if (!isset($checkoutData['shipping_company_id'])) {
            return redirect()->route('checkout.step2')->with('error', 'Lütfen önce kargo firması seçin.');
        }

        if (!isset($checkoutData['payment_method'])) {
            return redirect()->route('checkout.step3')->with('error', 'Lütfen önce ödeme yöntemi seçin.');
        }

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return back()->with('error', 'Sepetiniz boş.');
        }

        $totals = $this->getCartTotals();
        if (empty($totals['items'])) {
            return back()->with('error', 'Sepetinizde geçerli ürün bulunmuyor.');
        }

        $shippingCompany = ShippingCompany::find($checkoutData['shipping_company_id']);
        $shippingCost = $shippingCompany->calculateShippingCost(
            $totals['weight'] ?? 0,
            0,
            $totals['subtotal']
        );

        $total = $totals['subtotal'] + $totals['tax'] + $shippingCost - $totals['discountAmount'];

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $checkoutData['payment_method'],
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax'],
            'shipping_cost' => $shippingCost,
            'discount_amount' => $totals['discountAmount'],
            'total' => $total,
            'coupon_code' => $totals['couponCode'],
            'coupon_id' => $totals['coupon']?->id,
            'shipping_name' => $checkoutData['shipping_name'],
            'shipping_phone' => $checkoutData['shipping_phone'],
            'shipping_city' => $checkoutData['shipping_city'],
            'shipping_district' => $checkoutData['shipping_district'],
            'shipping_address' => $checkoutData['shipping_address'],
            'shipping_postal_code' => $checkoutData['shipping_postal_code'] ?? null,
            'billing_name' => $checkoutData['billing_name'] ?? $checkoutData['shipping_name'],
            'billing_phone' => $checkoutData['billing_phone'] ?? $checkoutData['shipping_phone'],
            'billing_city' => $checkoutData['billing_city'] ?? $checkoutData['shipping_city'],
            'billing_district' => $checkoutData['billing_district'] ?? $checkoutData['shipping_district'],
            'billing_address' => $checkoutData['billing_address'] ?? $checkoutData['shipping_address'],
            'billing_postal_code' => $checkoutData['billing_postal_code'] ?? $checkoutData['shipping_postal_code'] ?? null,
            'cargo_company' => $shippingCompany->name,
        ]);

        // Create order items
        foreach ($totals['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'product_sku' => $item['product']->sku,
                'price' => $item['price'],
                'tax_rate' => $item['product']->tax_rate,
                'quantity' => $item['quantity'],
                'subtotal' => $item['total'],
                'tax_amount' => $item['tax'],
                'total' => $item['total'] + $item['tax'],
            ]);

            // Update product stock
            $item['product']->decrement('stock', $item['quantity']);
            $item['product']->increment('sales_count', $item['quantity']);
        }

        // Update coupon usage
        if ($totals['coupon']) {
            $totals['coupon']->increment('used_count');
        }

        // Send order confirmation email and SMS
        try {
            $order->load(['user', 'items.product']);
            $email = auth()->user()->email ?? $checkoutData['shipping_email'] ?? null;
            if ($email) {
                Mail::to($email)->send(new OrderConfirmation($order));
            }

            // Send SMS notification
            $smsEnabled = \App\Models\Setting::getValue('sms_enabled', false);
            if ($smsEnabled) {
                $phone = auth()->user()->phone ?? $checkoutData['shipping_phone'] ?? null;
                if ($phone) {
                    $order->notify(new \App\Notifications\OrderSmsNotification($order, 'confirmation'));
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Order confirmation notification gönderim hatası: ' . $e->getMessage());
        }

        // Clear cart and checkout data
        Session::forget('cart');
        Session::forget('coupon_code');
        Session::forget('checkout_data');

        // Process payment based on method
        if ($checkoutData['payment_method'] === 'credit_card') {
            // For credit card, redirect to payment process
            return redirect()->route('payment.process', $order);
        } elseif ($checkoutData['payment_method'] === 'bank_transfer') {
            // For bank transfer, redirect to bank transfer page
            return redirect()->route('payment.bank-transfer.show', $order)
                ->with('success', 'Siparişiniz oluşturuldu. Lütfen havale/EFT bilgilerini görüntüleyin ve ödeme yapın.');
        } elseif ($checkoutData['payment_method'] === 'cash_on_delivery') {
            // For cash on delivery, just show confirmation
            return redirect()->route('checkout.confirm', $order)
                ->with('success', 'Kapıda ödeme siparişiniz oluşturuldu.');
        }

        return redirect()->route('checkout.confirm', $order)
            ->with('success', 'Siparişiniz başarıyla oluşturuldu.');
    }

    public function confirm(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'coupon']);

        return view('frontend.checkout.confirm', compact('order'));
    }
}

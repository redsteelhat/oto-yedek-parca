<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CouponController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where(function ($q) {
                          $q->whereNull('start_date')
                            ->orWhere('start_date', '<=', now());
                      })
                      ->where(function ($q) {
                          $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                      });
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            }
        }

        $coupons = $query->latest()->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('status', 'active')->get(['id', 'name', 'sku']);

        return view('admin.coupons.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        if (isset($validated['applicable_categories'])) {
            $validated['applicable_categories'] = array_filter($validated['applicable_categories']);
        }

        if (isset($validated['applicable_products'])) {
            $validated['applicable_products'] = array_filter($validated['applicable_products']);
        }

        // Add tenant_id to validated data
        $validated['tenant_id'] = $this->getCurrentTenantId();
        
        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('orders.user');
        
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('status', 'active')->get(['id', 'name', 'sku']);

        return view('admin.coupons.edit', compact('coupon', 'categories', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        if (isset($validated['applicable_categories'])) {
            $validated['applicable_categories'] = array_filter($validated['applicable_categories']);
        }

        if (isset($validated['applicable_products'])) {
            $validated['applicable_products'] = array_filter($validated['applicable_products']);
        }

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon başarıyla silindi.');
    }

    /**
     * Show coupon usage reports
     */
    public function reports(Request $request)
    {
        $query = Coupon::withCount('orders');

        // Filter by date range
        if ($request->date_from) {
            $query->whereHas('orders', function ($q) use ($request) {
                $q->where('created_at', '>=', $request->date_from);
            });
        }

        if ($request->date_to) {
            $query->whereHas('orders', function ($q) use ($request) {
                $q->where('created_at', '<=', $request->date_to . ' 23:59:59');
            });
        }

        // Filter by coupon
        if ($request->coupon_id) {
            $query->where('id', $request->coupon_id);
        }

        $coupons = $query->get();

        // Get detailed statistics
        $statistics = [];
        foreach ($coupons as $coupon) {
            $orders = Order::where('coupon_id', $coupon->id)
                ->when($request->date_from, function ($q) use ($request) {
                    $q->where('created_at', '>=', $request->date_from);
                })
                ->when($request->date_to, function ($q) use ($request) {
                    $q->where('created_at', '<=', $request->date_to . ' 23:59:59');
                })
                ->get();

            $totalDiscount = $orders->sum('discount_amount');
            $totalRevenue = $orders->sum('total');
            $avgOrderValue = $orders->count() > 0 ? $orders->avg('total') : 0;
            $uniqueUsers = $orders->pluck('user_id')->unique()->count();

            // Get usage by date
            $usageByDate = $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($group) {
                return $group->count();
            });

            // Get top users
            $topUsers = $orders->groupBy('user_id')
                ->map(function ($userOrders) {
                    return [
                        'user' => $userOrders->first()->user,
                        'count' => $userOrders->count(),
                        'total_discount' => $userOrders->sum('discount_amount'),
                    ];
                })
                ->sortByDesc('count')
                ->take(10);

            $statistics[$coupon->id] = [
                'coupon' => $coupon,
                'total_usage' => $orders->count(),
                'total_discount' => $totalDiscount,
                'total_revenue' => $totalRevenue,
                'avg_order_value' => $avgOrderValue,
                'unique_users' => $uniqueUsers,
                'usage_by_date' => $usageByDate,
                'top_users' => $topUsers,
            ];
        }

        // Overall statistics
        $allOrders = Order::whereNotNull('coupon_id')
            ->when($request->date_from, function ($q) use ($request) {
                $q->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->where('created_at', '<=', $request->date_to . ' 23:59:59');
            })
            ->get();

        $overallStats = [
            'total_coupons_used' => $allOrders->count(),
            'total_discount_given' => $allOrders->sum('discount_amount'),
            'total_revenue' => $allOrders->sum('total'),
            'unique_coupons' => $allOrders->pluck('coupon_id')->unique()->count(),
            'unique_users' => $allOrders->pluck('user_id')->unique()->count(),
        ];

        // Get all coupons for filter dropdown
        $allCoupons = Coupon::orderBy('code')->get();

        return view('admin.coupons.reports', compact('statistics', 'overallStats', 'allCoupons'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Supplier;
use App\Models\XmlImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends BaseAdminController
{
    /**
     * Display dashboard with statistics and charts
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        // Overall statistics
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('user_type', 'customer')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'low_stock_products' => Product::whereColumn('stock', '<=', 'min_stock_level')->count(),
        ];

        // Sales data for charts
        $salesData = $this->getSalesData($period);
        $revenueData = $this->getRevenueData($period);
        $topProducts = $this->getTopProducts(10);
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock_level')
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // Recent XML imports
        $recentImports = XmlImportLog::with('supplier')
            ->latest()
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'salesData',
            'revenueData',
            'topProducts',
            'lowStockProducts',
            'recentImports',
            'recentOrders',
            'period'
        ));
    }

    /**
     * Get sales data for chart
     */
    private function getSalesData($period)
    {
        $startDate = match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $orders = Order::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $orders->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d.m.Y'))->toArray(),
            'data' => $orders->pluck('count')->toArray(),
        ];
    }

    /**
     * Get revenue data for chart
     */
    private function getRevenueData($period)
    {
        $startDate = match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $orders = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $orders->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d.m.Y'))->toArray(),
            'data' => $orders->pluck('revenue')->map(fn($rev) => (float) $rev)->toArray(),
        ];
    }

    /**
     * Get top selling products
     */
    private function getTopProducts($limit = 10)
    {
        return Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }
}

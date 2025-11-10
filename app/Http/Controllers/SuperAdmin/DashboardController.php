<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display super admin dashboard.
     */
    public function index(Request $request)
    {
        // Period filter
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);

        // Overall statistics
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'total_users' => User::whereNotNull('tenant_id')->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ];

        // Recent tenants
        $recentTenants = Tenant::orderBy('created_at', 'desc')->limit(5)->get();

        // Top tenants by revenue
        $topTenants = Tenant::withCount(['orders as total_revenue' => function ($query) {
            $query->select(DB::raw('COALESCE(SUM(total), 0)'))
                ->where('payment_status', 'paid');
        }])
        ->orderBy('total_revenue', 'desc')
        ->limit(5)
        ->get();

        // Revenue chart data
        $revenueData = $this->getRevenueChartData($startDate);

        // Tenant growth chart data
        $growthData = $this->getTenantGrowthChartData($startDate);

        return view('super-admin.dashboard', compact(
            'stats',
            'recentTenants',
            'topTenants',
            'revenueData',
            'growthData',
            'period'
        ));
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate($period)
    {
        return match($period) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }

    /**
     * Get revenue chart data.
     */
    private function getRevenueChartData($startDate)
    {
        $revenues = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $revenues->pluck('date')->toArray(),
            'data' => $revenues->pluck('revenue')->toArray(),
        ];
    }

    /**
     * Get tenant growth chart data.
     */
    private function getTenantGrowthChartData($startDate)
    {
        $growth = Tenant::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $growth->pluck('date')->toArray(),
            'data' => $growth->pluck('count')->toArray(),
        ];
    }
}




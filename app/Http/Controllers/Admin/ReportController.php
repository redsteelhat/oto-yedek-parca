<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends BaseAdminController
{
    /**
     * Display reports index page.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Generate sales report.
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'view'); // view, excel, pdf

        $query = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'total_subtotal' => $orders->sum('subtotal'),
            'total_tax' => $orders->sum('tax_amount'),
            'total_shipping' => $orders->sum('shipping_cost'),
            'total_discount' => $orders->sum('discount_amount'),
            'average_order_value' => $orders->count() > 0 ? $orders->sum('total') / $orders->count() : 0,
        ];

        // Daily sales breakdown
        $dailySales = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($dayOrders) {
            return [
                'count' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total'),
            ];
        });

        // Status breakdown
        $statusBreakdown = $orders->groupBy('status')->map(function ($statusOrders) {
            return [
                'count' => $statusOrders->count(),
                'revenue' => $statusOrders->sum('total'),
            ];
        });

        // Payment method breakdown
        $paymentMethodBreakdown = $orders->groupBy('payment_method')->map(function ($methodOrders) {
            return [
                'count' => $methodOrders->count(),
                'revenue' => $methodOrders->sum('total'),
            ];
        });

        if ($format === 'excel') {
            return $this->exportSalesToExcel($orders, $stats, $startDate, $endDate);
        }

        return view('admin.reports.sales', compact(
            'orders',
            'stats',
            'dailySales',
            'statusBreakdown',
            'paymentMethodBreakdown',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Generate products report.
     */
    public function products(Request $request)
    {
        $format = $request->get('format', 'view'); // view, excel

        $query = Product::with(['category', 'supplier', 'orderItems']);

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status) {
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

        $products = $query->get()->map(function ($product) {
            $product->total_sales = $product->orderItems->sum('quantity');
            $product->total_revenue = $product->orderItems->sum(function ($item) {
                return $item->total;
            });
            return $product;
        });

        // Sort
        $sort = $request->get('sort', 'sales');
        switch ($sort) {
            case 'sales':
                $products = $products->sortByDesc('total_sales');
                break;
            case 'revenue':
                $products = $products->sortByDesc('total_revenue');
                break;
            case 'views':
                $products = $products->sortByDesc('views');
                break;
            default:
                $products = $products->sortByDesc('created_at');
        }

        if ($format === 'excel') {
            return $this->exportProductsToExcel($products);
        }

        return view('admin.reports.products', compact('products'));
    }

    /**
     * Generate customers report.
     */
    public function customers(Request $request)
    {
        $format = $request->get('format', 'view'); // view, excel

        $query = User::where('user_type', 'customer')
            ->with(['orders', 'orders.items']);

        // Filter by user type
        if ($request->has('user_type') && $request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by verified status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        $customers = $query->get()->map(function ($customer) {
            $customer->total_orders = $customer->orders->count();
            $customer->total_spent = $customer->orders->where('status', '!=', 'cancelled')->sum('total');
            $customer->average_order_value = $customer->total_orders > 0 
                ? $customer->total_spent / $customer->total_orders 
                : 0;
            return $customer;
        });

        // Sort
        $sort = $request->get('sort', 'spent');
        switch ($sort) {
            case 'spent':
                $customers = $customers->sortByDesc('total_spent');
                break;
            case 'orders':
                $customers = $customers->sortByDesc('total_orders');
                break;
            default:
                $customers = $customers->sortByDesc('created_at');
        }

        if ($format === 'excel') {
            return $this->exportCustomersToExcel($customers);
        }

        return view('admin.reports.customers', compact('customers'));
    }

    /**
     * Export sales report to Excel.
     */
    private function exportSalesToExcel($orders, $stats, $startDate, $endDate)
    {
        $filename = 'satis-raporu-' . $startDate . '-' . $endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders, $stats) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, ['Sipariş No', 'Tarih', 'Müşteri', 'Durum', 'Ödeme Durumu', 'Ödeme Yöntemi', 'Ara Toplam', 'KDV', 'Kargo', 'İndirim', 'Toplam']);

            // Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d.m.Y H:i'),
                    $order->user ? $order->user->name : 'Misafir',
                    $this->getStatusLabel($order->status),
                    $this->getPaymentStatusLabel($order->payment_status),
                    $this->getPaymentMethodLabel($order->payment_method),
                    number_format($order->subtotal, 2, ',', '.'),
                    number_format($order->tax_amount, 2, ',', '.'),
                    number_format($order->shipping_cost, 2, ',', '.'),
                    number_format($order->discount_amount, 2, ',', '.'),
                    number_format($order->total, 2, ',', '.'),
                ]);
            }

            // Summary
            fputcsv($file, []);
            fputcsv($file, ['Özet']);
            fputcsv($file, ['Toplam Sipariş', $stats['total_orders']]);
            fputcsv($file, ['Toplam Gelir', number_format($stats['total_revenue'], 2, ',', '.') . ' ₺']);
            fputcsv($file, ['Ara Toplam', number_format($stats['total_subtotal'], 2, ',', '.') . ' ₺']);
            fputcsv($file, ['Toplam KDV', number_format($stats['total_tax'], 2, ',', '.') . ' ₺']);
            fputcsv($file, ['Toplam Kargo', number_format($stats['total_shipping'], 2, ',', '.') . ' ₺']);
            fputcsv($file, ['Toplam İndirim', number_format($stats['total_discount'], 2, ',', '.') . ' ₺']);
            fputcsv($file, ['Ortalama Sipariş Değeri', number_format($stats['average_order_value'], 2, ',', '.') . ' ₺']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export products report to Excel.
     */
    private function exportProductsToExcel($products)
    {
        $filename = 'urun-raporu-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, ['SKU', 'Ürün Adı', 'Kategori', 'Fiyat', 'İndirimli Fiyat', 'Stok', 'Min. Stok', 'Satış Adedi', 'Toplam Gelir', 'Görüntülenme', 'Durum']);

            // Data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->category ? $product->category->name : '-',
                    number_format($product->price, 2, ',', '.'),
                    $product->sale_price ? number_format($product->sale_price, 2, ',', '.') : '-',
                    $product->stock,
                    $product->min_stock_level,
                    $product->total_sales ?? 0,
                    number_format($product->total_revenue ?? 0, 2, ',', '.'),
                    $product->views,
                    $this->getProductStatusLabel($product->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export customers report to Excel.
     */
    private function exportCustomersToExcel($customers)
    {
        $filename = 'musteri-raporu-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, ['Ad Soyad', 'E-posta', 'Telefon', 'Şirket', 'Toplam Sipariş', 'Toplam Harcama', 'Ortalama Sipariş Değeri', 'Kayıt Tarihi', 'Durum']);

            // Data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->name,
                    $customer->email,
                    $customer->phone ?? '-',
                    $customer->company_name ?? '-',
                    $customer->total_orders ?? 0,
                    number_format($customer->total_spent ?? 0, 2, ',', '.') . ' ₺',
                    number_format($customer->average_order_value ?? 0, 2, ',', '.') . ' ₺',
                    $customer->created_at->format('d.m.Y'),
                    $customer->is_verified ? 'Onaylı' : 'Onaysız',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get status label in Turkish.
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Beklemede',
            'processing' => 'İşleniyor',
            'shipped' => 'Kargoya Verildi',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal',
            'returned' => 'İade',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get payment status label in Turkish.
     */
    private function getPaymentStatusLabel($paymentStatus)
    {
        $labels = [
            'pending' => 'Beklemede',
            'paid' => 'Ödendi',
            'failed' => 'Başarısız',
            'refunded' => 'İade Edildi',
        ];

        return $labels[$paymentStatus] ?? $paymentStatus;
    }

    /**
     * Get payment method label in Turkish.
     */
    private function getPaymentMethodLabel($paymentMethod)
    {
        $labels = [
            'credit_card' => 'Kredi Kartı',
            'bank_transfer' => 'Havale/EFT',
            'cash_on_delivery' => 'Kapıda Ödeme',
            'iyzico' => 'İyzico',
            'paytr' => 'PayTR',
        ];

        return $labels[$paymentMethod] ?? $paymentMethod;
    }

    /**
     * Get product status label in Turkish.
     */
    private function getProductStatusLabel($status)
    {
        $labels = [
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'draft' => 'Taslak',
        ];

        return $labels[$status] ?? $status;
    }
}


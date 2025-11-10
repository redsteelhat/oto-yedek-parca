<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Order;
use App\Services\ShippingService;
use App\Mail\OrderConfirmation;
use App\Mail\OrderShipped;
use App\Mail\OrderDelivered;
use App\Notifications\OrderSmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'coupon']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,returned',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Send email and SMS based on status change
        if ($oldStatus !== $request->status) {
            $this->sendStatusEmail($order, $request->status);
            $this->sendStatusSms($order, $request->status);
        }

        return back()->with('success', 'Sipariş durumu güncellendi.');
    }

    /**
     * Send email based on order status
     */
    private function sendStatusEmail(Order $order, $status)
    {
        $order->load(['user', 'items.product']);
        $email = $order->user->email ?? $order->shipping_email ?? null;

        if (!$email) {
            return; // No email to send to
        }

        try {
            switch ($status) {
                case 'shipped':
                    Mail::to($email)->send(new OrderShipped($order));
                    break;
                case 'delivered':
                    Mail::to($email)->send(new OrderDelivered($order));
                    break;
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Email gönderim hatası: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS based on order status
     */
    private function sendStatusSms(Order $order, $status)
    {
        $order->load(['user']);
        $phone = $order->user->phone ?? $order->shipping_phone ?? null;

        if (!$phone) {
            return; // No phone to send SMS to
        }

        // Check if SMS notifications are enabled
        $smsEnabled = \App\Models\Setting::getValue('sms_enabled', false);
        if (!$smsEnabled) {
            return;
        }

        try {
            $notificationTypes = [
                'shipped' => 'shipped',
                'delivered' => 'delivered',
            ];

            if (isset($notificationTypes[$status])) {
                $order->notify(new OrderSmsNotification($order, $notificationTypes[$status]));
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('SMS gönderim hatası: ' . $e->getMessage());
        }
    }

    /**
     * Update tracking information
     */
    public function updateTracking(Request $request, Order $order)
    {
        $request->validate([
            'cargo_company' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
        ]);

        $oldStatus = $order->status;
        $order->update([
            'cargo_company' => $request->cargo_company,
            'tracking_number' => $request->tracking_number,
            'status' => 'shipped',
        ]);

        // Send shipped email and SMS if status changed
        if ($oldStatus !== 'shipped') {
            $this->sendStatusEmail($order, 'shipped');
            $this->sendStatusSms($order, 'shipped');
        }

        return back()->with('success', 'Kargo bilgileri güncellendi.');
    }

    /**
     * Create shipping label automatically
     */
    public function createShippingLabel(Request $request, Order $order)
    {
        $shippingCompany = \App\Models\ShippingCompany::where('name', $order->cargo_company)
            ->orWhere('code', $order->cargo_company)
            ->first();

        if (!$shippingCompany) {
            return back()->with('error', 'Kargo firması bulunamadı.');
        }

        try {
            $result = ShippingService::createShippingLabel($shippingCompany, $order);

            if ($result && isset($result['tracking_number'])) {
                $order->update([
                    'tracking_number' => $result['tracking_number'],
                    'status' => 'shipped',
                ]);

                return back()->with('success', 'Kargo etiketi oluşturuldu. Takip No: ' . $result['tracking_number']);
            } else {
                return back()->with('error', 'Kargo etiketi oluşturulamadı. Lütfen manuel olarak ekleyin.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Kargo etiketi oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Track shipping status
     */
    public function trackShipping(Request $request, Order $order)
    {
        if (!$order->tracking_number) {
            return back()->with('error', 'Takip numarası bulunamadı.');
        }

        $shippingCompany = \App\Models\ShippingCompany::where('name', $order->cargo_company)
            ->orWhere('code', $order->cargo_company)
            ->first();

        if (!$shippingCompany) {
            return back()->with('error', 'Kargo firması bulunamadı.');
        }

        try {
            $result = ShippingService::trackShipping($shippingCompany, $order->tracking_number);

            if ($result) {
                return back()->with('success', 'Kargo durumu sorgulandı.')->with('tracking_info', $result);
            } else {
                return back()->with('error', 'Kargo durumu sorgulanamadı.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Kargo durumu sorgulanırken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Add/update order notes
     */
    public function notes(Request $request, Order $order)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update(['notes' => $request->notes]);

        return back()->with('success', 'Sipariş notları güncellendi.');
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['cancelled', 'delivered'])) {
            return back()->with('error', 'Bu sipariş iptal edilemez.');
        }

        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);

        // Restore stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        return back()->with('success', 'Sipariş iptal edildi ve stok geri yüklendi.');
    }

    /**
     * Mark order as returned
     */
    public function return(Request $request, Order $order)
    {
        if ($order->status !== 'delivered') {
            return back()->with('error', 'Sadece teslim edilmiş siparişler iade edilebilir.');
        }

        $order->update([
            'status' => 'returned',
            'payment_status' => 'refunded',
        ]);

        // Restore stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        return back()->with('success', 'Sipariş iade olarak işaretlendi ve stok geri yüklendi.');
    }

    /**
     * Generate invoice PDF
     */
    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product', 'coupon']);
        
        // For now, return a simple HTML invoice view
        // In production, you would use a PDF library like dompdf or barryvdh/laravel-dompdf
        return view('admin.orders.invoice', compact('order'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class BankTransferController extends Controller
{
    /**
     * List pending bank transfers
     */
    public function index()
    {
        $orders = Order::where('payment_method', 'bank_transfer')
            ->where('payment_status', 'pending')
            ->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bank-transfers.index', compact('orders'));
    }

    /**
     * Show bank transfer details
     */
    public function show(Order $order)
    {
        if ($order->payment_method !== 'bank_transfer') {
            return redirect()->route('admin.bank-transfers.index')
                ->with('error', 'Bu sipariş havale/EFT ile ödeme yapılmamış.');
        }

        $order->load(['user', 'items.product', 'coupon']);

        return view('admin.bank-transfers.show', compact('order'));
    }

    /**
     * Approve bank transfer payment
     */
    public function approve(Request $request, Order $order)
    {
        if ($order->payment_method !== 'bank_transfer') {
            return back()->with('error', 'Bu sipariş havale/EFT ile ödeme yapılmamış.');
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        return redirect()->route('admin.bank-transfers.index')
            ->with('success', 'Havale/EFT ödemesi onaylandı.');
    }

    /**
     * Reject bank transfer payment
     */
    public function reject(Request $request, Order $order)
    {
        if ($order->payment_method !== 'bank_transfer') {
            return back()->with('error', 'Bu sipariş havale/EFT ile ödeme yapılmamış.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $order->update([
            'payment_status' => 'failed',
            'bank_transfer_notes' => ($order->bank_transfer_notes ?? '') . "\n\nRed Sebebi: " . $request->rejection_reason,
        ]);

        return redirect()->route('admin.bank-transfers.index')
            ->with('success', 'Havale/EFT ödemesi reddedildi.');
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\PaymentService;
use App\Services\IyzicoService;
use App\Services\PaytrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Process payment
     */
    public function process(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirm', $order)
                ->with('error', 'Bu sipariş zaten ödenmiş.');
        }

        $paymentMethod = $order->payment_method;

        try {
            $result = PaymentService::processPayment($order, $paymentMethod, $request->all());

            if ($result['success']) {
                if (isset($result['redirect'])) {
                    return redirect($result['redirect'])->with('success', $result['message']);
                }
                if (isset($result['token'])) {
                    // PayTR iframe token
                    return redirect()->route('payment.paytr.form', ['token' => $result['token']]);
                }
                return redirect()->route('checkout.confirm', $order)->with('success', $result['message']);
            } else {
                return redirect()->route('checkout.confirm', $order)->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('checkout.confirm', $order)->with('error', $e->getMessage());
        }
    }

    /**
     * Show bank transfer page
     */
    public function showBankTransfer(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_method !== 'bank_transfer') {
            return redirect()->route('checkout.confirm', $order);
        }

        // Get bank account information from settings
        $bankAccounts = [
            'bank_name' => Setting::getValue('payment_bank_transfer_bank_name'),
            'account_holder' => Setting::getValue('payment_bank_transfer_account_holder'),
            'account_number' => Setting::getValue('payment_bank_transfer_account_number'),
            'iban' => Setting::getValue('payment_bank_transfer_iban'),
            'branch' => Setting::getValue('payment_bank_transfer_branch'),
            'notes' => Setting::getValue('payment_bank_transfer_notes'),
        ];

        return view('frontend.payment.bank-transfer', compact('order', 'bankAccounts'));
    }

    /**
     * Upload bank transfer receipt
     */
    public function uploadReceipt(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Use FileUploadService for secure file upload
            $receiptPath = \App\Services\FileUploadService::uploadDocument(
                $request->file('receipt'),
                'bank-transfers',
                5120 // 5MB
            );

            $order->update([
                'bank_transfer_receipt' => $receiptPath,
                'bank_transfer_receipt_uploaded_at' => now(),
                'bank_transfer_notes' => $request->input('notes'),
            ]);

            return redirect()->route('payment.bank-transfer.show', $order)
                ->with('success', 'Dekont başarıyla yüklendi. Ödeme onayından sonra siparişiniz hazırlanacaktır.');
        } catch (\Exception $e) {
            return back()->with('error', 'Dekont yüklenirken bir hata oluştu.');
        }
    }

    /**
     * Iyzico callback
     */
    public function iyzicoCallback(Request $request)
    {
        $result = IyzicoService::handleCallback($request->all());

        if ($result) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    /**
     * PayTR callback
     */
    public function paytrCallback(Request $request)
    {
        $result = PaytrService::handleCallback($request->all());

        if ($result) {
            return response('OK');
        }

        return response('ERROR', 400);
    }

    /**
     * PayTR payment form
     */
    public function paytrForm(Request $request)
    {
        $token = $request->input('token');
        
        if (!$token) {
            return redirect()->route('cart.index')->with('error', 'Ödeme token\'ı bulunamadı.');
        }

        return view('frontend.payment.paytr-form', compact('token'));
    }

    /**
     * Payment success
     */
    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return redirect()->route('checkout.confirm', $order)
            ->with('success', 'Ödeme başarıyla tamamlandı.');
    }

    /**
     * Payment fail
     */
    public function fail()
    {
        return redirect()->route('cart.index')
            ->with('error', 'Ödeme işlemi başarısız oldu. Lütfen tekrar deneyin.');
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Address;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\CarYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        
        return view('frontend.account.index', compact('user', 'recentOrders'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('frontend.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('account.profile')->with('success', 'Profil bilgileriniz güncellendi.');
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()->with(['items.product'])->latest()->paginate(10);
        
        return view('frontend.account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'coupon']);
        
        return view('frontend.account.order-detail', compact('order'));
    }

    public function addresses()
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        
        return view('frontend.account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();

        // If this is set as default, unset others
        if ($request->is_default) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        // Get current tenant ID
        $tenantId = app(\App\Services\TenantService::class)->getCurrentTenantId();
        $validated['tenant_id'] = $tenantId;
        
        Address::create($validated);

        return redirect()->route('account.addresses')->with('success', 'Adres başarıyla eklendi.');
    }

    public function cars()
    {
        // TODO: Implement user's saved cars functionality
        return view('frontend.account.cars');
    }
}

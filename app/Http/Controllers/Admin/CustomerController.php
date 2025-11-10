<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = User::withCount('orders');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        if ($request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        $customers = $query->latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load(['addresses', 'orders.items.product']);
        
        $stats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->where('payment_status', 'paid')->sum('total'),
            'pending_orders' => $customer->orders()->where('status', 'pending')->count(),
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'user_type' => 'required|in:customer,dealer,admin',
            'is_verified' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }

        // Ensure tenant_id is set (don't allow changing tenant)
        if (!$customer->tenant_id) {
            $validated['tenant_id'] = $this->getCurrentTenantId();
        }
        
        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Müşteri başarıyla güncellendi.');
    }

    public function destroy(User $customer)
    {
        if ($customer->orders()->count() > 0) {
            return back()->with('error', 'Bu müşteriye ait siparişler bulunduğu için silinemez.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Müşteri başarıyla silindi.');
    }
}

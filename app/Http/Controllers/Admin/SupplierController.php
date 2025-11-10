<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Supplier;
use App\Console\Commands\XmlImport;
use Illuminate\Http\Request;

class SupplierController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::with('latestImportLog')->latest()->paginate(20);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:suppliers,code',
            'xml_url' => 'nullable|url',
            'xml_username' => 'nullable|string|max:255',
            'xml_password' => 'nullable|string|max:255',
            'xml_type' => 'required|in:standard,custom',
            'update_frequency' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Add tenant_id to validated data
        $validated['tenant_id'] = $this->getCurrentTenantId();
        
        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['xmlMappings', 'importLogs', 'products']);
        
        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:suppliers,code,' . $supplier->id,
            'xml_url' => 'nullable|url',
            'xml_username' => 'nullable|string|max:255',
            'xml_password' => 'nullable|string|max:255',
            'xml_type' => 'required|in:standard,custom',
            'update_frequency' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla silindi.');
    }

    /**
     * Import products from supplier XML
     */
    public function import(Request $request, Supplier $supplier)
    {
        // Run import in background for better UX
        \Artisan::queue('xml:import', ['supplier_id' => $supplier->id]);

        return back()->with('success', 'XML içe aktarma kuyruğa eklendi. İşlem arka planda devam edecek.');
    }

    /**
     * Get import progress
     */
    public function importProgress(Supplier $supplier)
    {
        $latestLog = $supplier->latestImportLog;
        
        if (!$latestLog) {
            return response()->json([
                'status' => 'no_log',
                'message' => 'Henüz import logu bulunmuyor.',
            ]);
        }

        return response()->json([
            'status' => $latestLog->status,
            'progress' => [
                'total' => $latestLog->total_items,
                'imported' => $latestLog->imported_items,
                'updated' => $latestLog->updated_items,
                'failed' => $latestLog->failed_items,
                'processed' => $latestLog->imported_items + $latestLog->updated_items + $latestLog->failed_items,
            ],
            'percentage' => $latestLog->total_items > 0 
                ? round((($latestLog->imported_items + $latestLog->updated_items + $latestLog->failed_items) / $latestLog->total_items) * 100, 2)
                : 0,
            'started_at' => $latestLog->started_at?->format('Y-m-d H:i:s'),
            'completed_at' => $latestLog->completed_at?->format('Y-m-d H:i:s'),
            'error_message' => $latestLog->error_message,
        ]);
    }
}

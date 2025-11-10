<?php

namespace App\Http\Controllers\XmlIntegration;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\XmlImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class XmlImportController extends Controller
{
    public function import(Request $request, Supplier $supplier)
    {
        try {
            Artisan::call('xml:import', ['supplier_id' => $supplier->id]);
            
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'XML içe aktarma başlatıldı.',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logs(Supplier $supplier)
    {
        $logs = XmlImportLog::where('supplier_id', $supplier->id)
            ->latest()
            ->paginate(20);

        return view('admin.suppliers.logs', compact('supplier', 'logs'));
    }
}

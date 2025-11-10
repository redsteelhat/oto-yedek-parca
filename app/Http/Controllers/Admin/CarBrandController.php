<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use Illuminate\Http\Request;

class CarBrandController extends Controller
{
    public function index()
    {
        $brands = CarBrand::withCount('models')->latest()->paginate(20);
        return view('admin.car-brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.car-brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cars_brands,slug',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        CarBrand::create($validated);

        return redirect()->route('admin.car-brands.index')
            ->with('success', 'Marka başarıyla oluşturuldu.');
    }

    public function show(CarBrand $carBrand)
    {
        $carBrand->load(['models.years']);
        return view('admin.car-brands.show', compact('carBrand'));
    }

    public function edit(CarBrand $carBrand)
    {
        return view('admin.car-brands.edit', compact('carBrand'));
    }

    public function update(Request $request, CarBrand $carBrand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cars_brands,slug,' . $carBrand->id,
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $carBrand->update($validated);

        return redirect()->route('admin.car-brands.index')
            ->with('success', 'Marka başarıyla güncellendi.');
    }

    public function destroy(CarBrand $carBrand)
    {
        if ($carBrand->models()->count() > 0) {
            return back()->with('error', 'Bu markaya ait modeller bulunduğu için silinemez.');
        }

        $carBrand->delete();

        return redirect()->route('admin.car-brands.index')
            ->with('success', 'Marka başarıyla silindi.');
    }

    /**
     * Import cars from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        // Handle UTF-8 BOM
        $content = file_get_contents($path);
        if (substr($content, 0, 3) == "\xEF\xBB\xBF") {
            $content = substr($content, 3);
            file_put_contents($path, $content);
        }
        
        $data = array_map('str_getcsv', file($path));
        
        // Remove header
        array_shift($data);

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                if (count($row) < 3) continue; // Skip invalid rows

                $brandName = trim($row[0] ?? '');
                $modelName = trim($row[1] ?? '');
                $year = trim($row[2] ?? '');
                $motorType = trim($row[3] ?? '');
                $engineCode = trim($row[4] ?? '');

                if (empty($brandName) || empty($modelName) || empty($year)) {
                    continue;
                }

                // Find or create brand
                $brand = \App\Models\CarBrand::where('name', $brandName)->first();
                if (!$brand) {
                    $brand = \App\Models\CarBrand::create([
                        'name' => $brandName,
                        'slug' => \Illuminate\Support\Str::slug($brandName),
                        'is_active' => true,
                    ]);
                }

                // Find or create model
                $model = \App\Models\CarModel::where('brand_id', $brand->id)
                    ->where('name', $modelName)
                    ->first();
                if (!$model) {
                    $model = \App\Models\CarModel::create([
                        'brand_id' => $brand->id,
                        'name' => $modelName,
                        'slug' => \Illuminate\Support\Str::slug($modelName),
                        'is_active' => true,
                    ]);
                    $imported++;
                } else {
                    $updated++;
                }

                // Find or create year
                $yearData = [
                    'model_id' => $model->id,
                    'year' => (int) $year,
                    'motor_type' => !empty($motorType) ? $motorType : null,
                    'engine_code' => !empty($engineCode) ? $engineCode : null,
                    'is_active' => true,
                ];

                $carYear = \App\Models\CarYear::where('model_id', $model->id)
                    ->where('year', $yearData['year'])
                    ->where('motor_type', $yearData['motor_type'])
                    ->first();

                if (!$carYear) {
                    \App\Models\CarYear::create($yearData);
                }
            } catch (\Exception $e) {
                $errors[] = "Satır " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "İçe aktarılan: {$imported}, Güncellenen: {$updated}";
        if (!empty($errors)) {
            $message .= ", Hata: " . count($errors);
        }

        return back()->with('success', $message)->with('errors', $errors);
    }

    /**
     * Export cars to CSV
     */
    public function export()
    {
        $brands = \App\Models\CarBrand::with(['models.years'])->get();

        $filename = 'cars_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($brands) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Header
            fputcsv($file, [
                'Marka',
                'Model',
                'Yıl',
                'Motor Tipi',
                'Motor Kodu',
            ]);

            // CSV Data
            foreach ($brands as $brand) {
                foreach ($brand->models as $model) {
                    foreach ($model->years as $year) {
                        fputcsv($file, [
                            $brand->name,
                            $model->name,
                            $year->year,
                            $year->motor_type ?? '',
                            $year->engine_code ?? '',
                        ]);
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

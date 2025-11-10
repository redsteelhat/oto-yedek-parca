<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use App\Models\CarBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarModelController extends Controller
{
    public function index(Request $request)
    {
        $query = CarModel::with('brand');

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $models = $query->withCount('years')->latest()->paginate(20);
        $brands = CarBrand::where('is_active', true)->orderBy('name')->get();

        return view('admin.car-models.index', compact('models', 'brands'));
    }

    public function create()
    {
        $brands = CarBrand::where('is_active', true)->orderBy('name')->get();
        return view('admin.car-models.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:cars_brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cars_models,slug',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        CarModel::create($validated);

        return redirect()->route('admin.car-models.index')
            ->with('success', 'Model başarıyla oluşturuldu.');
    }

    public function show(CarModel $carModel)
    {
        $carModel->load(['brand', 'years']);
        return view('admin.car-models.show', compact('carModel'));
    }

    public function edit(CarModel $carModel)
    {
        $brands = CarBrand::where('is_active', true)->orderBy('name')->get();
        return view('admin.car-models.edit', compact('carModel', 'brands'));
    }

    public function update(Request $request, CarModel $carModel)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:cars_brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cars_models,slug,' . $carModel->id,
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $carModel->update($validated);

        return redirect()->route('admin.car-models.index')
            ->with('success', 'Model başarıyla güncellendi.');
    }

    public function destroy(CarModel $carModel)
    {
        if ($carModel->years()->count() > 0) {
            return back()->with('error', 'Bu modele ait yıllar bulunduğu için silinemez.');
        }

        $carModel->delete();

        return redirect()->route('admin.car-models.index')
            ->with('success', 'Model başarıyla silindi.');
    }
}

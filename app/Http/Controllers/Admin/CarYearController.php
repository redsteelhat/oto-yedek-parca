<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarYear;
use App\Models\CarModel;
use Illuminate\Http\Request;

class CarYearController extends Controller
{
    public function index(Request $request)
    {
        $query = CarYear::with('model.brand');

        if ($request->model_id) {
            $query->where('model_id', $request->model_id);
        }

        if ($request->search) {
            $query->where('year', 'like', '%' . $request->search . '%')
                  ->orWhere('motor_type', 'like', '%' . $request->search . '%')
                  ->orWhere('engine_code', 'like', '%' . $request->search . '%');
        }

        $years = $query->latest()->paginate(20);
        $models = CarModel::where('is_active', true)->with('brand')->orderBy('name')->get();

        return view('admin.car-years.index', compact('years', 'models'));
    }

    public function create()
    {
        $models = CarModel::where('is_active', true)->with('brand')->orderBy('name')->get();
        return view('admin.car-years.create', compact('models'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'model_id' => 'required|exists:cars_models,id',
            'year' => 'required|integer|min:1900|max:2100',
            'motor_type' => 'nullable|string|max:255',
            'engine_code' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        CarYear::create($validated);

        return redirect()->route('admin.car-years.index')
            ->with('success', 'Yıl başarıyla oluşturuldu.');
    }

    public function show(CarYear $carYear)
    {
        $carYear->load(['model.brand']);
        return view('admin.car-years.show', compact('carYear'));
    }

    public function edit(CarYear $carYear)
    {
        $models = CarModel::where('is_active', true)->with('brand')->orderBy('name')->get();
        return view('admin.car-years.edit', compact('carYear', 'models'));
    }

    public function update(Request $request, CarYear $carYear)
    {
        $validated = $request->validate([
            'model_id' => 'required|exists:cars_models,id',
            'year' => 'required|integer|min:1900|max:2100',
            'motor_type' => 'nullable|string|max:255',
            'engine_code' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $carYear->update($validated);

        return redirect()->route('admin.car-years.index')
            ->with('success', 'Yıl başarıyla güncellendi.');
    }

    public function destroy(CarYear $carYear)
    {
        if ($carYear->products()->count() > 0) {
            return back()->with('error', 'Bu yıla ait ürünler bulunduğu için silinemez.');
        }

        $carYear->delete();

        return redirect()->route('admin.car-years.index')
            ->with('success', 'Yıl başarıyla silindi.');
    }
}

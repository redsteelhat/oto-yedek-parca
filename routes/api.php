<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\CarYear;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Car Brands API - Rate limited (60 requests per minute)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/car-brands', function () {
        $brands = CarBrand::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return response()->json($brands);
    });

    // Car Models API (for dependent dropdowns) - Rate limited
    Route::get('/car-models/{brandId}', function ($brandId) {
        $models = CarModel::where('brand_id', $brandId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return response()->json($models);
    });

    // Car Years API (for dependent dropdowns) - Rate limited
    Route::get('/car-years/{modelId}', function ($modelId) {
        $years = CarYear::where('model_id', $modelId)
            ->where('is_active', true)
            ->orderBy('year', 'desc')
            ->get(['id', 'year', 'motor_type']);
        
        return response()->json($years);
    });
});

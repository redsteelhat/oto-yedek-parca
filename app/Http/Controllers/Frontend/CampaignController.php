<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->paginate(12);

        return view('frontend.campaigns.index', compact('campaigns'));
    }

    public function show($slug)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::with(['primaryImage', 'category'])
            ->where('status', 'active');

        // Apply campaign filters
        if ($campaign->applicable_products) {
            $products->whereIn('id', $campaign->applicable_products);
        }

        if ($campaign->applicable_categories) {
            $products->whereIn('category_id', $campaign->applicable_categories);
        }

        $products = $products->latest()->paginate(20);

        return view('frontend.campaigns.show', compact('campaign', 'products'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends BaseAdminController
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user'])
            ->latest();

        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $reviews = $query->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(ProductReview $review)
    {
        $review->load(['product', 'user']);
        return view('admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, ProductReview $review)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
            'is_approved' => 'boolean',
            'is_verified_purchase' => 'boolean',
        ]);

        $review->update($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Yorum başarıyla güncellendi.');
    }

    /**
     * Approve the specified review.
     */
    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', 'Yorum onaylandı.');
    }

    /**
     * Reject the specified review.
     */
    public function reject(ProductReview $review)
    {
        $review->update(['is_approved' => false]);

        return redirect()->back()
            ->with('success', 'Yorum reddedildi.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(ProductReview $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Yorum başarıyla silindi.');
    }

    /**
     * Bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $reviewIds = $request->review_ids;

        if (!$reviewIds || !is_array($reviewIds)) {
            return redirect()->back()->with('error', 'Lütfen en az bir yorum seçin.');
        }

        switch ($action) {
            case 'approve':
                ProductReview::whereIn('id', $reviewIds)->update(['is_approved' => true]);
                $message = 'Seçili yorumlar onaylandı.';
                break;
            case 'reject':
                ProductReview::whereIn('id', $reviewIds)->update(['is_approved' => false]);
                $message = 'Seçili yorumlar reddedildi.';
                break;
            case 'delete':
                ProductReview::whereIn('id', $reviewIds)->delete();
                $message = 'Seçili yorumlar silindi.';
                break;
            default:
                return redirect()->back()->with('error', 'Geçersiz işlem.');
        }

        return redirect()->back()->with('success', $message);
    }
}


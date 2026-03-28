<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()
            ->where('user_id', auth()->id())
            ->with(['product.images', 'reviewImages'])
            ->latest()
            ->paginate(15);

        return view('customer.reviews.index', ['reviews' => $reviews]);
    }

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $review = DB::transaction(function () use ($request, $validated): Review {
            $review = Review::query()->create([
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
                'order_item_id' => $validated['order_item_id'],
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'images' => null,
                'is_verified_purchase' => true,
                'is_approved' => true,
            ]);

            $this->syncReviewMedia($review, $request->file('media', []));

            return $review->load('reviewImages');
        });

        if ($review->product && $review->product->vendor) {
            $review->product->vendor->notify(new \App\Notifications\Vendor\NewReviewNotification($review));
        }

        return back()->with('status', 'Review submitted.');
    }

    public function update(\App\Http\Requests\Customer\UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        abort_unless((int) $review->user_id === (int) auth()->id(), 403);

        $validated = $request->validated();

        DB::transaction(function () use ($request, $review, $validated): void {
            $review->update([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'images' => null,
            ]);

            if ($request->hasFile('media')) {
                $review->reviewImages->each(function ($reviewImage): void {
                    if (! str_starts_with($reviewImage->file_path, 'http') && ! str_starts_with($reviewImage->file_path, '/storage/')) {
                        Storage::disk('public')->delete($reviewImage->file_path);
                    }
                });

                $review->reviewImages()->delete();
                $this->syncReviewMedia($review->fresh(), $request->file('media', []));
            }
        });

        return back()->with('status', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        abort_unless((int) $review->user_id === (int) auth()->id(), 403);

        $review->delete();

        return back()->with('status', 'Review deleted successfully.');
    }

    /**
     * @param  array<int, UploadedFile>|UploadedFile|null  $files
     */
    protected function syncReviewMedia(Review $review, array|UploadedFile|null $files): void
    {
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        foreach ($files ?? [] as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $review->reviewImages()->create([
                'file_path' => $file->store('reviews', 'public'),
                'media_type' => str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image',
                'sort_order' => $index,
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\CampaignDiscountType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\EnrollCampaignRequest;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('ends_at', '>=', now())
                      ->orWhere('starts_at', '>', now());
            })
            ->withCount(['products' => function ($query) {
                $query->where('vendor_id', auth()->id());
            }])
            ->orderBy('starts_at')
            ->paginate(12);

        return view('vendor.campaigns.index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function show(Campaign $campaign): View
    {
        abort_unless($campaign->is_active, 404);

        $vendorId = (int) auth()->id();

        // Products already enrolled
        $enrolledProducts = $campaign->products()
            ->where('vendor_id', $vendorId)
            ->with(['images'])
            ->get();

        // Products available to enroll (active, not already enrolled)
        $availableProducts = Product::query()
            ->active()
            ->where('vendor_id', $vendorId)
            ->whereNotIn('id', $enrolledProducts->pluck('id'))
            ->with(['images'])
            ->get();

        return view('vendor.campaigns.show', [
            'campaign' => $campaign,
            'enrolledProducts' => $enrolledProducts,
            'availableProducts' => $availableProducts,
        ]);
    }

    public function enroll(EnrollCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        abort_unless($campaign->is_active && $campaign->ends_at >= now(), 403, 'Campaign is not active or has ended.');

        $validated = $request->validated();
        $productIds = $validated['product_ids'];
        $customPrices = $validated['custom_prices'] ?? [];

        $syncData = [];
        $userId = auth()->id();

        foreach ($productIds as $productId) {
            $data = [
                'enrolled_by' => $userId,
            ];

            if ($campaign->discount_type === CampaignDiscountType::Custom && isset($customPrices[$productId])) {
                $data['custom_price'] = $customPrices[$productId];
            }

            $syncData[$productId] = $data;
        }

        $campaign->products()->syncWithoutDetaching($syncData);

        return redirect()->route('vendor.campaigns.show', $campaign)
            ->with('status', 'Products successfully enrolled in the campaign.');
    }

    public function withdraw(Campaign $campaign, Product $product): RedirectResponse
    {
        abort_unless($product->vendor_id === auth()->id(), 403);
        
        $campaign->products()->detach($product->id);

        return redirect()->route('vendor.campaigns.show', $campaign)
            ->with('status', 'Product withdrawn from the campaign.');
    }
}

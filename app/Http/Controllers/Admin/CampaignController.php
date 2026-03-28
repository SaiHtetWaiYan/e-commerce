<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CampaignDiscountType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampaignRequest;
use App\Http\Requests\Admin\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        $tab = (string) $request->string('tab', 'all');
        $search = trim((string) $request->string('q'));

        $campaignsQuery = Campaign::query()
            ->withCount('products')
            ->with('creator:id,name')
            ->latest('starts_at');

        if ($search !== '') {
            $campaignsQuery->where(function (Builder $query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        match ($tab) {
            'active' => $campaignsQuery->active(),
            'upcoming' => $campaignsQuery->upcoming(),
            'ended' => $campaignsQuery->ended(),
            default => null,
        };

        $campaigns = $campaignsQuery->paginate(15)->withQueryString();

        return view('admin.campaigns.index', [
            'campaigns' => $campaigns,
            'tab' => $tab,
            'statusCounts' => [
                'all' => Campaign::query()->count(),
                'active' => Campaign::query()->active()->count(),
                'upcoming' => Campaign::query()->upcoming()->count(),
                'ended' => Campaign::query()->ended()->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.campaigns.create', [
            'campaign' => new Campaign(),
        ]);
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $campaign = Campaign::query()->create([
            'name' => $validated['name'],
            'slug' => $this->resolveSlug((string) ($validated['slug'] ?? ''), (string) $validated['name']),
            'description' => $validated['description'] ?? null,
            'banner_image' => $request->hasFile('banner_image') ? $request->file('banner_image')->store('campaigns/banners', 'public') : null,
            'thumbnail_image' => $request->hasFile('thumbnail_image') ? $request->file('thumbnail_image')->store('campaigns/thumbnails', 'public') : null,
            'badge_text' => $validated['badge_text'] ?? null,
            'badge_color' => $validated['badge_color'] ?? '#f97316',
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_type'] === CampaignDiscountType::Custom->value ? null : ($validated['discount_value'] ?? null),
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'is_active' => $request->boolean('is_active'),
            'created_by' => $request->user()?->id,
        ]);

        return to_route('admin.campaigns.show', $campaign)
            ->with('status', 'Campaign created successfully.');
    }

    public function show(Request $request, Campaign $campaign): View
    {
        $search = trim((string) $request->string('product_search'));

        $enrolledProducts = $campaign->products()
            ->with(['images', 'vendor.vendorProfile'])
            ->orderBy('campaign_product.sort_order')
            ->paginate(20)
            ->withQueryString();

        $availableProducts = Product::query()
            ->active()
            ->whereDoesntHave('campaigns', fn (Builder $query): Builder => $query->where('campaigns.id', $campaign->id))
            ->when($search !== '', function (Builder $query) use ($search): Builder {
                return $query->where(function (Builder $builder) use ($search): Builder {
                    return $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->with(['images', 'vendor.vendorProfile'])
            ->orderBy('name')
            ->limit(20)
            ->get();

        $potentialSavings = $campaign->products()
            ->get()
            ->sum(function (Product $product) use ($campaign): float {
                return max(0, round((float) $product->base_price - $campaign->getCampaignPriceForEnrolledProduct($product), 2));
            });

        return view('admin.campaigns.show', [
            'campaign' => $campaign->loadCount('products'),
            'enrolledProducts' => $enrolledProducts,
            'availableProducts' => $availableProducts,
            'potentialSavings' => $potentialSavings,
            'productSearch' => $search,
        ]);
    }

    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.edit', [
            'campaign' => $campaign,
        ]);
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $validated = $request->validated();

        $campaign->update([
            'name' => $validated['name'],
            'slug' => $this->resolveSlug((string) ($validated['slug'] ?? ''), (string) $validated['name'], $campaign->id),
            'description' => $validated['description'] ?? null,
            'banner_image' => $request->hasFile('banner_image')
                ? $request->file('banner_image')->store('campaigns/banners', 'public')
                : $campaign->banner_image,
            'thumbnail_image' => $request->hasFile('thumbnail_image')
                ? $request->file('thumbnail_image')->store('campaigns/thumbnails', 'public')
                : $campaign->thumbnail_image,
            'badge_text' => $validated['badge_text'] ?? null,
            'badge_color' => $validated['badge_color'] ?? '#f97316',
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_type'] === CampaignDiscountType::Custom->value ? null : ($validated['discount_value'] ?? null),
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return to_route('admin.campaigns.show', $campaign)
            ->with('status', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return to_route('admin.campaigns.index')
            ->with('status', 'Campaign deleted successfully.');
    }

    public function addProducts(Request $request, Campaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['required', 'integer', 'exists:products,id'],
            'custom_price' => ['nullable', 'array'],
            'custom_price.*' => ['nullable', 'numeric', 'min:0'],
            'custom_discount_percentage' => ['nullable', 'array'],
            'custom_discount_percentage.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sort_order' => ['nullable', 'array'],
            'sort_order.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $attachPayload = [];

        foreach ($validated['product_ids'] as $productId) {
            $attachPayload[$productId] = [
                'custom_price' => data_get($validated, "custom_price.{$productId}"),
                'custom_discount_percentage' => data_get($validated, "custom_discount_percentage.{$productId}"),
                'sort_order' => (int) (data_get($validated, "sort_order.{$productId}") ?? 0),
            ];
        }

        $campaign->products()->syncWithoutDetaching($attachPayload);

        return back()->with('status', count($attachPayload).' product(s) added to campaign.');
    }

    public function removeProduct(Campaign $campaign, Product $product): RedirectResponse
    {
        $campaign->products()->detach($product->id);

        return back()->with('status', 'Product removed from campaign.');
    }

    public function toggle(Campaign $campaign): RedirectResponse
    {
        $campaign->update([
            'is_active' => ! $campaign->is_active,
        ]);

        return back()->with('status', $campaign->is_active ? 'Campaign activated.' : 'Campaign deactivated.');
    }

    protected function resolveSlug(string $requestedSlug, string $name, ?int $ignoreCampaignId = null): string
    {
        $baseSlug = Str::slug($requestedSlug !== '' ? $requestedSlug : $name);
        $slug = $baseSlug !== '' ? $baseSlug : 'campaign';
        $counter = 2;

        while (Campaign::query()
            ->where('slug', $slug)
            ->when($ignoreCampaignId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $ignoreCampaignId))
            ->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'campaign').'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}

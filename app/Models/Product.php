<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static ?bool $campaignTablesAvailable = null;

    protected $fillable = [
        'vendor_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'base_price',
        'compare_price',
        'sku',
        'barcode',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'length',
        'width',
        'height',
        'status',
        'is_featured',
        'is_digital',
        'meta_title',
        'meta_description',
        'moderation_notes',
        'moderated_by',
        'moderated_at',
        'total_sold',
        'avg_rating',
        'review_count',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'avg_rating' => 'decimal:2',
            'status' => ProductStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class)
            ->withPivot(['custom_price', 'custom_discount_percentage', 'sort_order'])
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Active);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function getPrimaryImageAttribute(): ?string
    {
        if ($this->relationLoaded('images')) {
            return $this->images->firstWhere('is_primary', true)?->image_path;
        }

        return $this->images()->where('is_primary', true)->value('image_path');
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->compare_price === null || (float) $this->compare_price <= (float) $this->base_price) {
            return null;
        }

        return (int) round((((float) $this->compare_price - (float) $this->base_price) / (float) $this->compare_price) * 100);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getEffectivePrice(): float
    {
        $campaignPrice = $this->getActiveCampaignPrice();

        return $campaignPrice ?? (float) $this->base_price;
    }

    public function getActiveCampaignPrice(): ?float
    {
        if (! $this->hasCampaignTables()) {
            return null;
        }

        $campaign = $this->getActiveCampaign();

        if (! $campaign instanceof Campaign) {
            return null;
        }

        $pivot = $campaign->pivot;

        if ($pivot === null) {
            $pivot = $this->campaigns()
                ->where('campaign_id', $campaign->id)
                ->first()?->pivot;
        }

        if ($pivot?->custom_price !== null) {
            return max(0, round((float) $pivot->custom_price, 2));
        }

        if ($pivot?->custom_discount_percentage !== null) {
            $discount = min(100, max(0, (int) $pivot->custom_discount_percentage));

            return max(0, round((float) $this->base_price * (1 - ($discount / 100)), 2));
        }

        return $campaign->getCampaignPriceForProduct($this);
    }

    public function getActiveCampaign(): ?Campaign
    {
        if (! $this->hasCampaignTables()) {
            return null;
        }

        if ($this->relationLoaded('campaigns')) {
            return $this->campaigns
                ->filter(fn (Campaign $campaign): bool => $campaign->isRunning())
                ->sortByDesc(fn (Campaign $campaign): int => $campaign->starts_at?->getTimestamp() ?? 0)
                ->first();
        }

        return $this->campaigns()
            ->active()
            ->orderByDesc('starts_at')
            ->first();
    }

    protected function hasCampaignTables(): bool
    {
        if (self::$campaignTablesAvailable !== null) {
            return self::$campaignTablesAvailable;
        }

        self::$campaignTablesAvailable = Schema::hasTable('campaigns') && Schema::hasTable('campaign_product');

        return self::$campaignTablesAvailable;
    }
}

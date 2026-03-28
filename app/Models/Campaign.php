<?php

namespace App\Models;

use App\Enums\CampaignDiscountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
    use HasFactory;

    public const DEFAULT_BADGE_COLOR = '#f97316';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_image',
        'thumbnail_image',
        'badge_text',
        'badge_color',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'starts_at',
        'ends_at',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => CampaignDiscountType::class,
            'discount_value' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['custom_price', 'custom_discount_percentage', 'sort_order'])
            ->withTimestamps()
            ->orderBy('campaign_product.sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->where('starts_at', '>', now())
            ->orderBy('starts_at');
    }

    public function scopeEnded(Builder $query): Builder
    {
        return $query
            ->where('ends_at', '<', now())
            ->orderByDesc('ends_at');
    }

    public function isRunning(): bool
    {
        return $this->is_active
            && $this->starts_at !== null
            && $this->ends_at !== null
            && $this->starts_at->lte(now())
            && $this->ends_at->gte(now());
    }

    public function getCampaignPriceForProduct(Product $product): float
    {
        $basePrice = (float) $product->base_price;
        $discountValue = (float) ($this->discount_value ?? 0);
        $maxDiscountAmount = $this->max_discount_amount !== null
            ? (float) $this->max_discount_amount
            : null;

        return match ($this->discount_type) {
            CampaignDiscountType::Percentage => $this->calculatePercentagePrice($basePrice, $discountValue, $maxDiscountAmount),
            CampaignDiscountType::Fixed => $this->calculateFixedPrice($basePrice, $discountValue, $maxDiscountAmount),
            CampaignDiscountType::Custom => round($basePrice, 2),
        };
    }

    public function getCampaignPriceForEnrolledProduct(Product $product): float
    {
        $pivot = $product->pivot;

        if ($pivot !== null && $pivot->custom_price !== null) {
            return max(0, round((float) $pivot->custom_price, 2));
        }

        if ($pivot !== null && $pivot->custom_discount_percentage !== null) {
            $discount = min(100, max(0, (int) $pivot->custom_discount_percentage));

            return max(0, round((float) $product->base_price * (1 - ($discount / 100)), 2));
        }

        return $this->getCampaignPriceForProduct($product);
    }

    public function getBadgeColorAttribute(?string $value): string
    {
        $normalizedColor = $value ?: self::DEFAULT_BADGE_COLOR;

        if (! str_starts_with($normalizedColor, '#')) {
            $normalizedColor = '#'.$normalizedColor;
        }

        if (! preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $normalizedColor)) {
            return self::DEFAULT_BADGE_COLOR;
        }

        return strtolower($normalizedColor);
    }

    protected function calculatePercentagePrice(float $basePrice, float $discountValue, ?float $maxDiscountAmount): float
    {
        $rawDiscountAmount = round($basePrice * ($discountValue / 100), 2);
        $discountAmount = $maxDiscountAmount !== null
            ? min($rawDiscountAmount, $maxDiscountAmount)
            : $rawDiscountAmount;

        return max(0, round($basePrice - $discountAmount, 2));
    }

    protected function calculateFixedPrice(float $basePrice, float $discountValue, ?float $maxDiscountAmount): float
    {
        $discountAmount = $maxDiscountAmount !== null
            ? min($discountValue, $maxDiscountAmount)
            : $discountValue;

        return max(0, round($basePrice - $discountAmount, 2));
    }
}

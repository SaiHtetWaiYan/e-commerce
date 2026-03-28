<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_item_id',
        'rating',
        'comment',
        'images',
        'is_verified_purchase',
        'is_approved',
        'vendor_reply',
        'vendor_replied_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'images' => 'array',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
            'vendor_replied_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Review $review): void {
            $review->reviewImages()->each(function (ReviewImage $reviewImage): void {
                if (! str_starts_with($reviewImage->file_path, 'http') && ! str_starts_with($reviewImage->file_path, '/storage/')) {
                    Storage::disk('public')->delete($reviewImage->file_path);
                }
            });

            $review->reviewImages()->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function reviewImages(): HasMany
    {
        return $this->hasMany(ReviewImage::class)->orderBy('sort_order');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }
}

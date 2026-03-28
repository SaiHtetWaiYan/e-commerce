<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AppSetting extends Model
{
    /** @use HasFactory<\Database\Factories\AppSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public const MARKETPLACE_CACHE_KEY = 'marketplace.settings';

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function marketplaceFieldMeta(): array
    {
        return [
            'marketplace.logo' => [
                'label' => 'Marketplace Logo',
                'type' => 'string',
            ],
            'marketplace.default_currency' => [
                'label' => 'Currency Code',
                'type' => 'string',
            ],
            'marketplace.default_shipping_fee' => [
                'label' => 'Default Shipping Fee',
                'type' => 'float',
            ],
            'marketplace.free_shipping_threshold' => [
                'label' => 'Free Shipping Threshold',
                'type' => 'float',
            ],
            'marketplace.default_tax_rate' => [
                'label' => 'Default Tax Rate',
                'type' => 'float',
            ],
            'marketplace.vendor.default_commission_rate' => [
                'label' => 'Vendor Commission Rate',
                'type' => 'float',
            ],
            'marketplace.default_carrier' => [
                'label' => 'Default Carrier',
                'type' => 'string',
            ],
            'marketplace.order.number_prefix' => [
                'label' => 'Order Number Prefix',
                'type' => 'string',
            ],
            'marketplace.tracking_prefix' => [
                'label' => 'Tracking Prefix',
                'type' => 'string',
            ],
            'marketplace.vendor.require_approval' => [
                'label' => 'Vendor Approval Required',
                'type' => 'boolean',
            ],
        ];
    }

    /**
     * @return array<string, scalar>
     */
    public static function marketplaceDefaults(): array
    {
        return collect(array_keys(static::marketplaceFieldMeta()))
            ->mapWithKeys(fn (string $key): array => [$key => config($key)])
            ->all();
    }

    /**
     * @return array<string, scalar>
     */
    public static function marketplaceOverrides(): array
    {
        if (! static::marketplaceTableExists()) {
            static::clearMarketplaceCache();

            return [];
        }

        return Cache::rememberForever(static::MARKETPLACE_CACHE_KEY, function (): array {
            $fieldMeta = static::marketplaceFieldMeta();
            $storedValues = static::query()
                ->whereIn('key', array_keys($fieldMeta))
                ->pluck('value', 'key');

            $settings = [];

            foreach ($fieldMeta as $key => $meta) {
                if (! $storedValues->has($key)) {
                    continue;
                }

                $settings[$key] = static::castStoredValue(
                    $storedValues->get($key),
                    (string) $meta['type'],
                );
            }

            return $settings;
        });
    }

    /**
     * @return array<string, scalar>
     */
    public static function resolvedMarketplaceSettings(): array
    {
        return array_merge(
            static::marketplaceDefaults(),
            static::marketplaceOverrides(),
        );
    }

    /**
     * @param  array<string, scalar>  $settings
     */
    public static function updateMarketplaceSettings(array $settings): void
    {
        if (! static::marketplaceTableExists()) {
            static::clearMarketplaceCache();

            return;
        }

        foreach ($settings as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => static::prepareValueForStorage($value)],
            );
        }

        static::clearMarketplaceCache();
    }

    public static function clearMarketplaceCache(): void
    {
        Cache::forget(static::MARKETPLACE_CACHE_KEY);
    }

    public static function marketplaceTableExists(): bool
    {
        try {
            return Schema::hasTable('app_settings');
        } catch (\Throwable) {
            return false;
        }
    }

    protected static function castStoredValue(mixed $value, string $type): string|int|float|bool|null
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            'float' => (float) $value,
            'int' => (int) $value,
            default => $value !== null ? (string) $value : null,
        };
    }

    protected static function prepareValueForStorage(string|int|float|bool|null $value): string
    {
        return match (true) {
            is_bool($value) => $value ? '1' : '0',
            $value === null => '',
            default => (string) $value,
        };
    }
}

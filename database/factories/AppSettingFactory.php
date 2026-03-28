<?php

namespace Database\Factories;

use App\Models\AppSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppSetting>
 */
class AppSettingFactory extends Factory
{
    protected $model = AppSetting::class;

    public function definition(): array
    {
        $fieldMeta = AppSetting::marketplaceFieldMeta();
        $key = fake()->randomElement(array_keys($fieldMeta));

        return [
            'key' => $key,
            'value' => $this->fakeSettingValue($key, (string) $fieldMeta[$key]['type']),
        ];
    }

    protected function fakeSettingValue(string $key, string $type): string
    {
        return match ($type) {
            'boolean' => fake()->boolean() ? '1' : '0',
            'float' => (string) fake()->randomFloat(2, 1, 200),
            'int' => (string) fake()->numberBetween(1, 200),
            default => match ($key) {
                'marketplace.default_currency' => fake()->randomElement(['USD', 'EUR', 'LAK']),
                'marketplace.default_carrier' => fake()->randomElement(['Marketplace Express', 'FastShip', 'SwiftDrop']),
                'marketplace.order.number_prefix' => fake()->randomElement(['ORD', 'MKT', 'SALE']),
                'marketplace.tracking_prefix' => fake()->randomElement(['TRK', 'SHP', 'EXP']),
                default => fake()->word(),
            },
        };
    }
}

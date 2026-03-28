<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-'.strtoupper(Str::random(10)),
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
            'payment_method' => 'cod',
            'subtotal' => 100,
            'total' => 100,
            'shipping_fee' => 0,
            'tax_amount' => 0,
            'shipping_address' => ['address' => '123 Test St', 'full_name' => $this->faker->name()],
            'billing_address' => ['address' => '123 Test St', 'full_name' => $this->faker->name()],
        ];
    }
}

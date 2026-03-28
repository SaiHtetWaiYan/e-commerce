<?php

return [
    'default_currency' => env('MARKETPLACE_CURRENCY', 'USD'),
    'default_shipping_fee' => (float) env('MARKETPLACE_DEFAULT_SHIPPING_FEE', 3.99),
    'free_shipping_threshold' => (float) env('MARKETPLACE_FREE_SHIPPING_THRESHOLD', 50),
    'default_tax_rate' => (float) env('MARKETPLACE_DEFAULT_TAX_RATE', 0.07),
    'shipping_methods' => [
        'standard' => ['label' => 'Standard Shipping', 'fee' => 3.99, 'days' => '5-7'],
        'express' => ['label' => 'Express Shipping', 'fee' => 9.99, 'days' => '2-3'],
        'same_day' => ['label' => 'Same Day Delivery', 'fee' => 14.99, 'days' => '0-1'],
    ],
    'low_stock_alert_threshold' => (int) env('MARKETPLACE_LOW_STOCK_THRESHOLD', 5),
    'order' => [
        'number_prefix' => env('MARKETPLACE_ORDER_PREFIX', 'ORD'),
    ],
    'vendor' => [
        'default_commission_rate' => (float) env('MARKETPLACE_VENDOR_COMMISSION_RATE', 10),
        'require_approval' => (bool) env('MARKETPLACE_VENDOR_REQUIRE_APPROVAL', true),
    ],
    'default_carrier' => env('MARKETPLACE_DEFAULT_CARRIER', 'Marketplace Express'),
    'tracking_prefix' => env('MARKETPLACE_TRACKING_PREFIX', 'TRK'),
];

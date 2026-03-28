<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Hold = 'hold';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}

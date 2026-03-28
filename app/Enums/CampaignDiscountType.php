<?php

namespace App\Enums;

enum CampaignDiscountType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';
    case Custom = 'custom';
}

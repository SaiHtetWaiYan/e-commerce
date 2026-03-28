<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Resolved = 'resolved';
    case Closed = 'closed';
}

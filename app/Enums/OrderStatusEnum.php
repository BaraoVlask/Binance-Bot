<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case New = 'NEW';
    case PartiallyFilled = 'PARTIALLY_FILLED';
    case Filled = 'FILLED';
    case Cancelled = 'CANCELED';
    case PendingCancel = 'PENDING_CANCEL';
    case Rejected = 'REJECTED';
    case Expired = 'EXPIRED';
    case ExpiredInMatch = 'EXPIRED_IN_MATCH';
}

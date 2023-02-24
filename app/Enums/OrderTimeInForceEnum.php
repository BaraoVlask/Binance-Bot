<?php

namespace App\Enums;

enum OrderTimeInForceEnum: string
{
    case GoodTilCancel = 'GTC';
    case ImmediateOrCancel = 'IOC';
    case FillOrKill = 'FOK';
}

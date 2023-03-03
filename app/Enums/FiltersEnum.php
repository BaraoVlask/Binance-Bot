<?php

namespace App\Enums;

enum FiltersEnum: string
{
    case LootSize = 'LOT_SIZE';
    case PriceFilter = 'PRICE_FILTER';
    case MaxOrders = 'MAX_NUM_ORDERS';
    case MinAmount = 'MIN_NOTIONAL';

}

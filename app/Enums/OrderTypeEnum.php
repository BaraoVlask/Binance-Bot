<?php

namespace App\Enums;

enum OrderTypeEnum: string
{
    case Limit = 'LIMIT';
    case Market = 'MARKET';
    case StopLoss = 'STOP_LOSS';
    case StopLossLimit = 'STOP_LOSS_LIMIT';
    case TakeProfit = 'TAKE_PROFIT';
    case TakeProfitLimit = 'TAKE_PROFIT_LIMIT';
    case LimitMaker = 'LIMIT_MAKER';
}
